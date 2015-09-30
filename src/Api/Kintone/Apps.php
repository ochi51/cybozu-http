<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Apps
{
    const MAX_GET_APPS = 100;

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
    public function get(array $ids = [], array $codes = [], $name = null, array $spaceIds = [], $limit = self::MAX_GET_APPS, $offset = 0, $guestSpaceId = null)
    {
        $options = ['json' => [
            'ids' => $ids,
            'codes' => $codes,
            'name' => $name,
            'spaceIds' => $spaceIds,
        ]];
        $options['json']['limit'] = $limit;
        $options['json']['offset'] = $offset;

        return $this->client
            ->get(KintoneApi::generateUrl('apps.json', $guestSpaceId), $options)
            ->json();
    }


}