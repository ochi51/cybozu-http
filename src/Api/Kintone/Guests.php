<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Guests
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
     * Post guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param array $guests
     * @return array
     */
    public function post(array $guests): array
    {
        $options = ['json' => ['guests' => $guests]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('guests.json'), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Delete guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param array $guests
     * @return array
     */
    public function delete(array $guests): array
    {
        $options = ['json' => ['guests' => $guests]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->delete(KintoneApi::generateUrl('guests.json'), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

}
