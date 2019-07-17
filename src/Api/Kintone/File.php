<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;
use CybozuHttp\Service\ResponseService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;


/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class File
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get file
     * https://cybozudev.zendesk.com/hc/ja/articles/202166180#step1
     *
     * @param string $fileKey
     * @param int $guestSpaceId
     * @return string
     */
    public function get($fileKey, $guestSpaceId = null): string
    {
        $options = ['json' => ['fileKey' => $fileKey]];
        $response = $this->client->get(KintoneApi::generateUrl('file.json', $guestSpaceId), $options);

        return (string)$response->getBody();
    }

    /**
     * Get file stream response
     * https://cybozudev.zendesk.com/hc/ja/articles/202166180#step1
     *
     * @param string $fileKey
     * @param int $guestSpaceId
     * @return ResponseInterface
     * @throws RequestException
     */
    public function getStreamResponse($fileKey, $guestSpaceId = null): ResponseInterface
    {
        $options = [
            'json' => ['fileKey' => $fileKey],
            'stream' => true
        ];
        $result = $this->client->get(KintoneApi::generateUrl('file.json', $guestSpaceId), $options);
        if ($result instanceof RequestException) {
            $this->handleJsonError($result);
            throw $result;
        }

        return $result;
    }

    /**
     * @param RequestException $result
     * @throws RequestException
     */
    private function handleJsonError(RequestException $result): void
    {
        $response = $result->getResponse();
        if ($response instanceof ResponseInterface) {
            $service = new ResponseService($result->getRequest(), $response);
            if ($service->isJsonResponse()) {
                $service->handleJsonError();
            }
        }
    }

    /**
     * @param array $fileKeys
     * @param int|null $guestSpaceId
     * @return array [contents, contents, ...] The order of $fileKeys
     */
    public function multiGet(array $fileKeys, $guestSpaceId = null): array
    {
        $result = [];
        $concurrency = $this->client->getConfig('concurrency');
        $headers = $this->client->getConfig('headers');
        $headers['Content-Type'] = 'application/json';
        $url = KintoneApi::generateUrl('file.json', $guestSpaceId);
        $requests = static function () use ($fileKeys, $url, $headers) {
            foreach ($fileKeys as $fileKey) {
                $body = \GuzzleHttp\json_encode(['fileKey' => $fileKey]);
                yield new Request('GET', $url, $headers, $body);
            }
        };
        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrency ?: 1,
            'fulfilled' => static function (ResponseInterface $response, $index) use (&$result) {
                $result[$index] = (string)$response->getBody();
            }
        ]);
        $pool->promise()->wait();

        return $result;
    }

    /**
     * Post file
     * https://cybozudev.zendesk.com/hc/ja/articles/201941824#step1
     *
     * @param string $path
     * @param int|null $guestSpaceId
     * @param string|null $filename
     * @return string
     */
    public function post($path, $guestSpaceId = null, $filename = null): string
    {
        $options = ['multipart' => [self::createMultipart($path, $filename)]];
        $this->changeLocale();

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('file.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize()['fileKey'];
    }

    /**
     * @param array $fileNames
     * @param int|null $guestSpaceId
     * @return array [fileKey, fileKey, ...] The order of $fileNames
     * @throws \InvalidArgumentException
     */
    public function multiPost(array $fileNames, $guestSpaceId = null): array
    {
        $this->changeLocale();

        $result = [];
        $concurrency = $this->client->getConfig('concurrency');
        $headers = $this->client->getConfig('headers');
        $url = KintoneApi::generateUrl('file.json', $guestSpaceId);
        $requests = static function () use ($fileNames, $url, $headers) {
            foreach ($fileNames as $filename) {
                $body = new MultipartStream([self::createMultipart($filename)]);
                yield new Request('POST', $url, $headers, $body);
            }
        };
        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrency ?: 1,
            'fulfilled' => static function (ResponseInterface $response, $index) use (&$result) {
                /** @var JsonStream $stream */
                $stream = $response->getBody();
                $result[$index] = $stream->jsonSerialize()['fileKey'];
            }
        ]);
        $pool->promise()->wait();
        ksort($result);

        return $result;
    }

    /**
     * Returns locale independent base name of the given path.
     *
     * @param string $name The new file name
     * @return string containing
     */
    public static function getFilename($name): string
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);

        return $originalName;
    }

    private function changeLocale(): void
    {
        $baseUri = $this->client->getConfig('base_uri');
        if (strpos($baseUri->getHost(), 'cybozu.com') > 0) { // Japanese kintone
            setlocale(LC_ALL, 'ja_JP.UTF-8');
        }
    }

    /**
     * @param string $path
     * @param string|null $filename
     * @return array
     */
    private static function createMultipart($path, $filename = null): array
    {
        return [
            'name' => 'file',
            'filename' => self::getFilename($filename ?: $path),
            'contents' => fopen($path, 'rb'),
            'headers' => ['Content-Type' => mime_content_type($path)]
        ];
    }
}
