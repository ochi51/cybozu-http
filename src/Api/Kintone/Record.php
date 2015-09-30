<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Record
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
     * Get record
     * https://cybozudev.zendesk.com/hc/ja/articles/202331474#step1
     *
     * @param integer $appId
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function get($appId, $id, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $appId, 'id' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('record.json', $guestSpaceId), $options)
            ->json()['record'];
    }

    /**
     * Post record
     * https://cybozudev.zendesk.com/hc/ja/articles/202166160#step1
     *
     * @param integer $appId
     * @param array $record
     * @param integer $guestSpaceId
     * @return array
     */
    public function post($appId, array $record, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $appId, 'record' => $record]];

        return $this->client
            ->post(KintoneApi::generateUrl('record.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Put record
     * https://cybozudev.zendesk.com/hc/ja/articles/201941784#step1
     *
     * @param integer $appId
     * @param integer $id
     * @param array $record
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function put($appId, $id, array $record, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => ['app' => $appId, 'id' => $id, 'revision' => $revision, 'record' => $record]];

        return $this->client
            ->put(KintoneApi::generateUrl('record.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Delete record
     * https://cybozudev.zendesk.com/hc/ja/articles/201941794
     *
     * @param integer $appId
     * @param integer $id
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function delete($appId, $id, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $appId,
            'ids' => [$id],
            'revisions' => [$revision]
        ]];

        return $this->client
            ->delete(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Put record status
     * https://cybozudev.zendesk.com/hc/ja/articles/204791550#anchor_changeRecordStatus
     *
     * @param integer $appId
     * @param integer $id
     * @param string $action
     * @param string $assignee
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putStatus($appId, $id, $action, $assignee = null, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $appId,
            'id' => $id,
            'action' => $action,
            'revision' => $revision
        ]];
        if ($assignee !== null) {
            $options['json']['assignee'] = $assignee;
        }

        return $this->client
            ->put(KintoneApi::generateUrl('record/status.json', $guestSpaceId), $options)
            ->json();
    }
}