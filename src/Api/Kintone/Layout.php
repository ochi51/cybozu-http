<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 *
 */
class Layout
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
     * Get layout
     * https://developer.cybozu.io/hc/ja/articles/204783170#anchor_getform_layout
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
            ->get(KintoneApi::generateUrl('app/form/layout.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
