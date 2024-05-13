<?php

namespace CybozuHttp\Service;

use CybozuHttp\Exception\RuntimeException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ResponseService
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string|null
     */
    private $responseBody = null;

    /**
     * @var \Throwable|null
     */
    private $previousThrowable;

    /**
     * ResponseService constructor.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Throwable|null $previousThrowable
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, ?\Throwable $previousThrowable = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->previousThrowable = $previousThrowable;
    }

    /**
     * @return bool
     */
    public function isJsonResponse(): bool
    {
        $contentType = $this->response->getHeader('Content-Type');
        $contentType = is_array($contentType) && isset($contentType[0]) ? $contentType[0] : $contentType;

        return is_string($contentType) && strpos($contentType, 'application/json') === 0;
    }

    /**
     * @return bool
     */
    public function isHtmlResponse(): bool
    {
        $contentType = $this->response->getHeader('Content-Type');
        $contentType = is_array($contentType) && isset($contentType[0]) ? $contentType[0] : $contentType;

        return is_string($contentType) && strpos($contentType, 'text/html') === 0;
    }

    /**
     * @throws RequestException
     * @throws RuntimeException
     */
    public function handleError(): void
    {
        if ($this->isJsonResponse()) {
            $this->handleJsonError();
        } else if ($this->isHtmlResponse()) {
            $this->handleDomError();
        }

        throw $this->createRuntimeException('Failed to extract error message because Content-Type of error response is unexpected.');
    }

    /**
     * @throws RequestException
     * @throws RuntimeException
     */
    private function handleDomError(): void
    {
        $body = $this->getResponseBody();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        if ($dom->loadHTML($body)) {
            $title = $dom->getElementsByTagName('title');
            if (is_object($title)) {
                $title = $title->item(0)->nodeValue;
            }
            $message = match ($title) {
                'Error' => $dom->getElementsByTagName('h3')->item(0)->nodeValue,
                'Unauthorized' => $dom->getElementsByTagName('h2')->item(0)->nodeValue,
                default => 'Invalid auth.',
            };
            if (is_null($message)) {
                throw $this->createRuntimeException('Failed to extract error message from DOM response.');
            }
            throw $this->createException($message);
        }

        throw $this->createRuntimeException('Failed to parse DOM response.');
    }

    /**
     * @throws RequestException
     * @throws RuntimeException
     */
    private function handleJsonError(): void
    {
        $body = $this->getResponseBody();
        try {
            $json = \GuzzleHttp\json_decode($body, true);
        } catch (\InvalidArgumentException) {
            throw $this->createRuntimeException('Failed to decode JSON response.');
        }

        $message = $json['message'];
        if (isset($json['errors']) && is_array($json['errors'])) {
            $message .= $this->addErrorMessages($json['errors']);
        }
        if (is_null($message) && isset($json['reason'])) {
            $message = $json['reason'];
        }

        if (is_null($message)) {
            throw $this->createRuntimeException('Failed to extract error message from JSON response.');
        }
        throw $this->createException($message);
    }

    /**
     * @param array $errors
     * @return string
     */
    private function addErrorMessages(array $errors): string
    {
        $message = ' (';
        foreach ($errors as $k => $err) {
            $message .= $k . ' : ';
            if (is_array($err['messages'])) {
                foreach ($err['messages'] as $m) {
                    $message .= $m . ' ';
                }
            } else {
                $message .= $err['messages'];
            }
        }
        $message .= ')';

        return $message;
    }

    /**
     * In stream mode, contents can be obtained only once, so this method makes it reusable.
     * @return string
     */
    private function getResponseBody(): string
    {
        if (is_null($this->responseBody)) {
            $this->responseBody = $this->response->getBody()->getContents();
        }

        return $this->responseBody;
    }

    /**
     * @param string $message
     * @return RequestException
     */
    private function createException(string $message): RequestException
    {
        $level = (int) floor($this->response->getStatusCode() / 100);
        $className = RequestException::class;

        if ($level === 4) {
            $className = ClientException::class;
        } elseif ($level === 5) {
            $className = ServerException::class;
        }

        return new $className($message, $this->request, $this->response);
    }

    /**
     * @param string $message
     * @return RuntimeException
     */
    private function createRuntimeException(string $message): RuntimeException
    {
        return new RuntimeException(
            $message,
            0,
            $this->previousThrowable,
            ['responseBody' => $this->getResponseBody()]
        );
    }
}
