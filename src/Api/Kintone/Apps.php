<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Apps
{
    public const MAX_GET_APPS = 100;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get apps
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step2
     *
     * @param array $ids
     * @param array $codes
     * @param string $name
     * @param array $spaceIds
     * @param integer $limit
     * @param integer $offset
     * @param integer $guestSpaceId
     * @return array
     */
    public function get(array $ids = [], array $codes = [], $name = null, array $spaceIds = [], $limit = self::MAX_GET_APPS, $offset = 0, $guestSpaceId = null): array
    {
        $options = ['json' => compact('ids', 'codes', 'name', 'spaceIds')];
        $options['json']['limit'] = $limit;
        $options['json']['offset'] = $offset;

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('apps.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }


}
