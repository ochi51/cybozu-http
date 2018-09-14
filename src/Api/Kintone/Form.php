<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 *
 */
class Form
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
     * Get form
     * https://developer.cybozu.io/hc/ja/articles/201941834
     *
     * @param integer $app
     * @param integer $guestSpaceId
     * @return array
     */
    public function get($app, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $app]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('form.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
