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
                'filename' => basename($filename),
                'contents' => fopen($filename, 'r')
            ]
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('file.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize()["fileKey"];
    }
}