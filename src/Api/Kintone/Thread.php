<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Thread
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
     * Put thread
     * https://cybozudev.zendesk.com/hc/ja/articles/201941894
     *
     * @param integer $id
     * @param string  $name
     * @param string  $body
     * @param integer $guestSpaceId
     * @return array
     */
    public function put($id, $name = null, $body = null, $guestSpaceId = null)
    {
        $options = ['json' => ['id' => $id]];
        if ($name !== null) {
            $options['json']['name'] = $name;
        }
        if ($body !== null) {
            $options['json']['body'] = $body;
        }

        return $this->client
            ->put(KintoneApi::generateUrl('space/thread.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Post thread comment
     * https://cybozudev.zendesk.com/hc/ja/articles/209732306
     *
     * @param int $spaceId
     * @param int $threadId
     * @param string $comment
     * @param array $mentions
     * @param array $files
     * @param int $guestSpaceId
     * @return array
     */
    public function comment($spaceId, $threadId, $comment, array $mentions = [], array $files = [], $guestSpaceId = null)
    {
        $options = ['json' => [
            'space'  => $spaceId,
            'thread' => $threadId,
            'comment'  => [
                'text' => $comment,
                'mentions' => $mentions,
                'files' => $files
            ],
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('space/thread/comment.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }
}