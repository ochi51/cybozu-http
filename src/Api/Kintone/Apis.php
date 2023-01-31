<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

class Apis
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
     * Get apis
     * https://developer.cybozu.io/hc/ja/articles/201941934
     *
     * @return array
     */
    public function get(): array
    {
        /** @var JsonStream $stream */
        $stream = $this->client->get(KintoneApi::generateUrl('apis.json'))->getBody();

        return $stream->jsonSerialize();
    }
}
