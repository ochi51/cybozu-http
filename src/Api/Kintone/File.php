<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;


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
                'headers' => [
                    'Content-Type' => mime_content_type($filename)
                ]
            ]
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('file.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize()['fileKey'];
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
}