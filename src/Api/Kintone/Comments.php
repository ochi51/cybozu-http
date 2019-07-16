<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

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
    public function get($appId, $recordId, $order = 'desc', $offset = 0, $limit = 10, $guestSpaceId = null): array
    {
        $options = ['json' => [
            'app' => $appId,
            'record' => $recordId,
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit
        ]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('record/comments.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize()['comments'];
    }

    /**
     * @param int $appId
     * @param array $recordIds
     * @param int|null $guestSpaceId
     * @return array [recordId => comments, ...]
     */
    public function allByRecords($appId, array $recordIds, $guestSpaceId = null): array
    {
        $result = [];
        $concurrency = $this->client->getConfig('concurrency');
        $offset = 0;
        while (count($recordIds) > 0) {
            $tmpIds = [];
            $requests = $this->createGetRequestsCallback($appId, $recordIds, $guestSpaceId, $offset);
            $pool = new Pool($this->client, $requests(), [
                'concurrency' => $concurrency ?: 1,
                'fulfilled' => $this->createMergeCommentsCallback($result, $tmpIds, $recordIds)
            ]);
            $pool->promise()->wait();
            $recordIds = $tmpIds;
            $offset += 10;
        }
        ksort($result);

        return $result;
    }

    /**
     * @param integer $appId
     * @param array $recordIds
     * @param integer $guestSpaceId
     * @param int $offset
     * @return \Closure
     */
    private function createGetRequestsCallback($appId, $recordIds, $guestSpaceId = null, $offset = 0): callable
    {
        $headers = $this->client->getConfig('headers');
        $headers['Content-Type'] = 'application/json';
        $url = KintoneApi::generateUrl('record/comments.json', $guestSpaceId);

        return static function () use ($appId, $recordIds, $url, $headers, $offset) {
            foreach ($recordIds as $id) {
                $body = \GuzzleHttp\json_encode([
                    'app' => $appId,
                    'record' => $id,
                    'order' => 'asc',
                    'offset' => $offset,
                    'limit' => 10
                ]);
                yield new Request('GET', $url, $headers, $body);
            }
        };
    }

    /**
     * @param array $result
     * @param array $tmpIds
     * @param array $ids
     * @return \Closure
     */
    private function createMergeCommentsCallback(array &$result, array &$tmpIds, array $ids): callable
    {
        return static function (ResponseInterface $response, $index) use (&$result, &$tmpIds, $ids) {
            $recordId = $ids[$index];
            /** @var JsonStream $stream */
            $stream = $response->getBody();
            $body = $stream->jsonSerialize();
            if ($body['newer']) {
                $tmpIds[] = $recordId;
            }
            if (!isset($result[$recordId])) {
                $result[$recordId] = [];
            }
            /** @var array $comments */
            $comments = $body['comments'];
            foreach ($comments as $comment) {
                $result[$recordId][] = $comment;
            }
        };
    }

    /**
     * @param int $appId
     * @param array $comments [recordId => [['text' => 'comment message', 'mentions' => []], ...], ...]
     * @param int|null $guestSpaceId
     * @return array
     */
    public function postByRecords($appId, $comments, $guestSpaceId = null): array
    {
        $result = [];
        $concurrency = $this->client->getConfig('concurrency');

        while (count($comments) > 0) {
            $requests = $this->createPostRequestsCallback($appId, $comments, $guestSpaceId);
            $pool = new Pool($this->client, $requests(), [
                'concurrency' => $concurrency ?: 1,
                'fulfilled' => $this->createPostFinishedAtCallback($result, $comments)
            ]);
            $pool->promise()->wait();
        }
        ksort($result);

        return $result;
    }

    /**
     * @param int $appId
     * @param array $comments
     * @param int|null $guestSpaceId
     * @return \Closure
     */
    private function createPostRequestsCallback($appId, array $comments, $guestSpaceId = null): callable
    {
        $headers = $this->client->getConfig('headers');
        $headers['Content-Type'] = 'application/json';
        $url = KintoneApi::generateUrl('record/comment.json', $guestSpaceId);

        return static function () use ($appId, $comments, $url, $headers) {
            foreach ($comments as $recordId => $values) {
                $comment = reset($values);
                if (!isset($comment['text'])) {
                    continue;
                }
                $body = \GuzzleHttp\json_encode([
                    'app' => $appId,
                    'record' => $recordId,
                    'comment' => $comment
                ]);
                yield new Request('POST', $url, $headers, $body);
            }
        };
    }

    /**
     * @param array $result
     * @param array $comments
     * @return \Closure
     */
    private function createPostFinishedAtCallback(array &$result, array &$comments): callable
    {
        $recordIds = array_keys($comments);
        return static function (ResponseInterface $response, $index) use (&$result, &$comments, $recordIds) {
            /** @var JsonStream $stream */
            $stream = $response->getBody();
            $commentId = $stream->jsonSerialize()['id'];
            $recordId = $recordIds[$index];
            if (!isset($result[$recordId])) {
                $result[$recordId] = [];
            }
            $result[$recordId][] = $commentId;
            $keys = array_keys($comments[$recordId]);
            $firstKey = reset($keys);
            unset($comments[$recordId][$firstKey]);
            if (count($comments[$recordId]) === 0) {
                unset($comments[$recordId]);
            }
        };
    }
}
