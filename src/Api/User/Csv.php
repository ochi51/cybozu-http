<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Api\Kintone\File;
use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Csv
{
    private static $type = [
        'user',
        'organization',
        'title',
        'group',
        'userOrganizations',
        'userGroups',
        'userServices'
    ];

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get csv file
     *
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public function get($type): string
    {
        if (!in_array($type, self::$type, true)) {
            throw new \InvalidArgumentException('Invalid type parameter');
        }

        $content = (string)$this->client
            ->get(UserApi::generateUrl("csv/{$type}.csv"))
            ->getBody();

        return $content;
    }

    /**
     * Post csv file
     *
     * @param string $type
     * @param string $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function post($type, $filename): int
    {
        return $this->postKey($type, $this->file($filename));
    }

    /**
     * Post file key
     *
     * @param string $type
     * @param string $fileKey
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postKey($type, $fileKey): int
    {
        if (!in_array($type, self::$type, true)) {
            throw new \InvalidArgumentException('Invalid type parameter');
        }

        $options = ['json' => ['fileKey' => $fileKey]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(UserApi::generateUrl("csv/{$type}.json"), $options)
            ->getBody();

        return $stream->jsonSerialize()['id'];
    }

    /**
     * Post file
     * https://cybozudev.zendesk.com/hc/ja/articles/202350470
     *
     * @param string $filename
     * @return string
     */
    public function file($filename): string
    {
        $options = ['multipart' =>  [
            [
                'name' => 'file',
                'filename' => File::getFilename($filename),
                'contents' => fopen($filename, 'rb'),
                'headers' => [
                    'Content-Type' => mime_content_type($filename)
                ]
            ]
        ]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(UserApi::generateUrl('file.json'), $options)
            ->getBody();

        return $stream->jsonSerialize()['fileKey'];
    }

    /**
     * Get post csv result
     * https://cybozudev.zendesk.com/hc/ja/articles/202361320
     *
     * @param int $id
     * @return array
     */
    public function result($id): array
    {
        $options = ['query' => ['id' => $id]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(UserApi::generateUrl('csv/result.json'), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
