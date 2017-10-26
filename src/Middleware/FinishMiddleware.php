<?php

namespace CybozuHttp\Middleware;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class FinishMiddleware
{

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return \Closure
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {

            return $handler($request, $options)->then(
                $this->onFulfilled($request),
                $this->onRejected($request)
            );
        };
    }

    /**
     * @param RequestInterface $request
     * @return \Closure
     * @throws \InvalidArgumentException
     */
    private function onFulfilled(RequestInterface $request)
    {
        return function (ResponseInterface $response) {
            return $response->withBody(new JsonStream($response->getBody()));
        };
    }

    /**
     * @param RequestInterface $request
     * @return \Closure
     * @throws RequestException
     */
    private function onRejected(RequestInterface $request)
    {
        return function ($reason) use ($request) {
            if ($reason instanceof RequestException) {
                $response = $reason->getResponse();
                if ($response !== null && $response->getStatusCode() >= 300) {
                    self::jsonError($request, $response);
                    self::domError($request, $response);
                }
            }
        };
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws RequestException
     */
    private static function domError(RequestInterface $request, ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadHTML($body);
        if ($dom->loadHTML($body)) {
            $title = $dom->getElementsByTagName('title');
            if (is_object($title)) {
                $title = $title->item(0)->nodeValue;
            }
            if ($title === 'Error') {
                $message = $dom->getElementsByTagName('h3')->item(0)->nodeValue;
                throw self::createException($message, $request, $response);
            }
            if ($title === 'Unauthorized') {
                $message = $dom->getElementsByTagName('h2')->item(0)->nodeValue;
                throw self::createException($message, $request, $response);
            }

            throw self::createException('Invalid auth.', $request, $response);
        }
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws RequestException
     */
    private static function jsonError(RequestInterface $request, ResponseInterface $response)
    {
        try {
            $body = (string)$response->getBody();
            $json = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException(
                    'Error trying to decode response: ' .
                    json_last_error_msg()
                );
            }
        } catch (\RuntimeException $e) {
            return;
        }

        $message = $json['message'];
        if (isset($json['errors']) && is_array($json['errors'])) {
            $message .= self::addErrorMessages($json['errors']);
        }

        throw self::createException($message, $request, $response);
    }

    /**
     * @param array $errors
     * @return string
     */
    private static function addErrorMessages(array $errors)
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
        $message .= ' )';

        return $message;
    }

    /**
     * @param string $message
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @return RequestException
     */
    private static function createException($message, RequestInterface $request, ResponseInterface $response)
    {
        $level = (int) floor($response->getStatusCode() / 100);
        $className = RequestException::class;

        if ($level === 4) {
            $className = ClientException::class;
        } elseif ($level === 5) {
            $className = ServerException::class;
        }

        return new $className($message, $request, $response);
    }
}