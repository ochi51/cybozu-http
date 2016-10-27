<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Comments
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
     * Get record comments
     * https://cybozudev.zendesk.com/hc/ja/articles/208242326
     *
     * @param int $appId
     * @param int $recordId
     * @param string $order "asc" or "desc"
     * @param int $offset
     * @param int $limit Max = 10
     * @param int $guestSpaceId
     * @return array
     */
    public function get($appId, $recordId, $order = 'desc', $offset = 0, $limit = 10, $guestSpaceId = null)
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $recordId,
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit
        ]];

        return $this->client
            ->get(KintoneApi::generateUrl('record/comments.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize()['comments'];
    }
}