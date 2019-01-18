<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

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
    public function post($appId, $record, $comment, array $mentions = [], $guestSpaceId = null): array
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $record,
            'comment' => [
                'text' => $comment,
                'mentions' => $mentions
            ]
        ]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('record/comment.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
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
    public function delete($appId, $recordId, $id, $guestSpaceId = null): array
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $recordId,
            'comment' => $id
        ]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->delete(KintoneApi::generateUrl('record/comment.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
