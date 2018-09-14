<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 *
 */
class Fields
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
     * Get fields
     * https://developer.cybozu.io/hc/ja/articles/204783170
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
            ->get(KintoneApi::generateUrl('app/form/fields.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
