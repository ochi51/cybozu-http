<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;
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
    public function get($fileKey, $guestSpaceId = null)
    {
        $options = ['json' => ['fileKey' => $fileKey]];
        $response = $this->client->get(KintoneApi::generateUrl('file.json', $guestSpaceId), $options);

        return (string)$response->getBody();
    }

    /**
     * @param array $fileKeys
     * @param int|null $guestSpaceId
     * @return array
     */
    public function multiGet(array $fileKeys, $guestSpaceId = null)
    {
        $result = [];
        $concurrency = $this->client->getConfig('concurrency');
        $headers = $this->client->getConfig('headers');
        $headers['Content-Type'] = 'application/json';
        $url = KintoneApi::generateUrl('file.json', $guestSpaceId);
        $requests = function () use ($fileKeys, $url, $headers) {
            foreach ($fileKeys as $fileKey) {
                $body = \GuzzleHttp\json_encode(['fileKey' => $fileKey]);
                yield new Request('GET', $url, $headers, $body);
            }
        };
        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrency ?: 1,
            'fulfilled' => function (ResponseInterface $response, $index) use (&$result) {
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
     * @param string $filename
     * @param int $guestSpaceId
     * @return string
     */
    public function post($filename, $guestSpaceId = null)
    {
        $options = ['multipart' =>  [
            [
                'name' => 'file',
                'filename' => self::getFilename($filename),
                'contents' => fopen($filename, 'rb'),
                'headers' => ['Content-Type' => mime_content_type($filename)]
            ]
        ]];
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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function multiPost(array $fileNames, $guestSpaceId = null)
    {
        $this->changeLocale();

        $result = [];
        $concurrency = $this->client->getConfig('concurrency');
        $headers = $this->client->getConfig('headers');
        $url = KintoneApi::generateUrl('file.json', $guestSpaceId);
        $requests = function () use ($fileNames, $url, $headers) {
            foreach ($fileNames as $filename) {
                $body = new MultipartStream([[
                    'name' => 'file',
                    'filename' => self::getFilename($filename),
                    'contents' => fopen($filename, 'rb'),
                    'headers' => ['Content-Type' => mime_content_type($filename)]
                ]]);
                yield new Request('POST', $url, $headers, $body);
            }
        };
        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrency ?: 1,
            'fulfilled' => function (ResponseInterface $response, $index) use (&$result) {
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
    public static function getFilename($name)
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);

        return $originalName;
    }

    private function changeLocale()
    {
        $baseUri = $this->client->getConfig('base_uri');
        if (strpos($baseUri->getHost(), 'cybozu.com') > 0) { // Japanese kintone
            setlocale(LC_ALL, 'ja_JP.UTF-8');
        }
    }
}