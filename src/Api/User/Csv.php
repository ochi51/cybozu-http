<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Csv
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
     * Get csv file
     *
     * @param string $type
     * @return string
     */
    public function get($type)
    {
        if (!in_array($type, [
            'user',
            'organization',
            'title',
            'group',
            'userOrganizations',
            'userGroups',
            'userServices'])
        ) {
            throw new \InvalidArgumentException('Invalid type parameter');
        }

        $content = (string)$this->client
            ->get(UserApi::generateUrl("csv/{$type}.json"))
            ->getBody();

        return substr($content, 0, strlen($content)-3);
    }

    /**
     * Post csv file
     *
     * @param string $type
     * @param string $filename
     * @return int
     */
    public function post($type, $filename)
    {
        return $this->postKey($type, $this->file($filename));
    }

    /**
     * Post file key
     *
     * @param string $type
     * @param string $fileKey
     * @return int
     */
    public function postKey($type, $fileKey)
    {
        if (!in_array($type, [
            'user',
            'organization',
            'title',
            'group',
            'userOrganizations',
            'userGroups',
            'userServices'])
        ) {
            throw new \InvalidArgumentException('Invalid type parameter');
        }

        $options = ['json' => ['fileKey' => $fileKey]];

        return $this->client
            ->post(UserApi::generateUrl("csv/{$type}.json"), $options)
            ->getBody()->jsonSerialize()['id'];
    }

    /**
     * Post file
     * https://cybozudev.zendesk.com/hc/ja/articles/202350470
     *
     * @param string $filename
     * @return string
     */
    public function file($filename)
    {
        $options = ['multipart' =>  [
            [
                'name' => 'file',
                'filename' => basename(mb_convert_encoding($filename, 'UTF-8', 'auto')),
                'contents' => fopen($filename, 'r'),
                'headers' => [
                    'Content-Type' => mime_content_type($filename)
                ]
            ]
        ]];

        return $this->client
            ->post(UserApi::generateUrl('file.json'), $options)
            ->getBody()->jsonSerialize()["fileKey"];
    }

    /**
     * Get post csv result
     * https://cybozudev.zendesk.com/hc/ja/articles/202361320
     *
     * @param int $id
     * @return array
     */
    public function result($id)
    {
        $options = ['query' => ['id' => $id]];

        return $this->client
            ->get(UserApi::generateUrl('csv/result.json'), $options)
            ->getBody()->jsonSerialize();
    }
}