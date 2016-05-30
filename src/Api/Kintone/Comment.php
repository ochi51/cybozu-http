<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Comment
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
     * Post record comment
     * https://cybozudev.zendesk.com/hc/ja/articles/209758903
     *
     * @param integer $appId
     * @param integer $record
     * @param string  $comment
     * @param array   $mentions
     * @param integer $guestSpaceId
     * @return array
     */
    public function post($appId, $record, $comment, $mentions = [], $guestSpaceId = null)
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $record,
            'comment' => [
                'text' => $comment,
                'mentions' => $mentions
            ]
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('record/comment.json', $guestSpaceId), $options)
            ->json();
    }

    /**
     * Delete record comment
     * https://cybozudev.zendesk.com/hc/ja/articles/209758703
     *
     * @param integer $appId
     * @param integer $recordId
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function delete($appId, $recordId, $id, $guestSpaceId = null)
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $recordId,
            'comment' => $id
        ]];

        return $this->client
            ->delete(KintoneApi::generateUrl('record/comment.json', $guestSpaceId), $options)
            ->json();
    }
}