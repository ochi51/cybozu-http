<?php

namespace CybozuHttp\Api\Kintone;

use GuzzleHttp\Stream\Stream;
use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Records
{
    const MAX_GET_RECORDS = 500;
    const MAX_POST_RECORDS = 100;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get records
     * https://cybozudev.zendesk.com/hc/ja/articles/202331474#step2
     *
     * @param integer $appId
     * @param string $query
     * @param integer $guestSpaceId
     * @param boolean $totalCount
     * @param array|null $fields
     * @return array
     */
    public function get($appId, $query = '', $guestSpaceId = null, $totalCount = true, array $fields = null)
    {
        $options = ['json' => ['app' => $appId, 'query' => $query]];
        if ($totalCount) {
            $options['json']['totalCount'] = $totalCount;
        }
        if ($fields) {
            $options['json']['fields'] = $fields;
        }

        return $this->client
            ->get(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Post records
     * https://cybozudev.zendesk.com/hc/ja/articles/202166160#step2
     *
     * @param integer $appId
     * @param array $records
     * @param integer $guestSpaceId
     * @return array
     */
    public function post($appId, array $records, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        return $this->client
            ->post(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Put records
     * https://cybozudev.zendesk.com/hc/ja/articles/201941784#step2
     *
     * @param integer $appId
     * @param array $records
     * @param integer $guestSpaceId
     * @return array
     */
    public function put($appId, array $records, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        return $this->client
            ->put(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Delete records
     * https://cybozudev.zendesk.com/hc/ja/articles/201941794
     *
     * @param integer $appId
     * @param array $ids
     * @param integer $guestSpaceId
     * @param array $revisions
     * @return array
     */
    public function delete($appId, array $ids, $guestSpaceId = null, array $revisions = [])
    {
        $options = ['json' => ['app' => $appId, 'ids' => $ids]];
        if (count($revisions) && count($ids) === count($revisions)) {
            $options['json']['revisions'] = $revisions;
        }

        return $this->client
            ->delete(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Put records status
     * https://cybozudev.zendesk.com/hc/ja/articles/204791550#anchor_changeRecordStatusBulk
     *
     * @param integer $appId
     * @param array $records
     * @param integer $guestSpaceId
     * @return array
     */
    public function putStatus($appId, array $records, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        return $this->client
            ->put(KintoneApi::generateUrl('records/status.json', $guestSpaceId), $options)
            ->json();
    }
}