<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Middleware\JsonStream;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Records
{
    public const MAX_GET_RECORDS = 500;
    public const MAX_POST_RECORDS = 100;

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
    public function get($appId, $query = '', $guestSpaceId = null, $totalCount = true, array $fields = null): array
    {
        $options = ['json' => ['app' => $appId, 'query' => $query]];
        if ($totalCount) {
            $options['json']['totalCount'] = $totalCount;
        }
        if ($fields) {
            $options['json']['fields'] = $fields;
        }
        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Get all records
     *
     * @param integer $appId
     * @param string $query
     * @param integer $guestSpaceId
     * @param array|null $fields
     * @return array
     */
    public function all($appId, $query = '', $guestSpaceId = null, array $fields = null): array
    {
        $result = [];
        $result[0] = $this->get($appId, $query . ' limit ' . self::MAX_GET_RECORDS, $guestSpaceId, true, $fields);
        $totalCount = $result[0]['totalCount'];
        if ($totalCount <= self::MAX_GET_RECORDS) {
            return $result[0]['records'];
        }

        $concurrency = $this->client->getConfig('concurrency');
        $requests = $this->createGetRequestsCallback($appId, $query, $guestSpaceId, $fields, $totalCount);
        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrency ?: 1,
            'fulfilled' => static function (ResponseInterface $response, $index) use (&$result) {
                /** @var JsonStream $stream */
                $stream = $response->getBody();
                $result[$index+1] = array_merge($stream->jsonSerialize());
            }
        ]);
        $pool->promise()->wait();

        return $this->convertResponseToRecords($result);
    }

    /**
     * @param integer $appId
     * @param string $query
     * @param integer $guestSpaceId
     * @param array|null $fields
     * @param integer $totalCount
     * @return \Closure
     */
    private function createGetRequestsCallback($appId, $query, $guestSpaceId, $fields, $totalCount): callable
    {
        $headers = $this->client->getConfig('headers');
        $headers['Content-Type'] = 'application/json';
        return static function () use ($appId, $query, $guestSpaceId, $fields, $totalCount, $headers) {
            $num = ceil($totalCount / self::MAX_GET_RECORDS);
            for ($i = 1; $i < $num; $i++) {
                $body = [
                    'app' => $appId,
                    'query' => $query . ' limit ' . self::MAX_GET_RECORDS . ' offset ' . $i * self::MAX_GET_RECORDS,
                ];
                if ($fields) {
                    $body['fields'] = $fields;
                }
                yield new Request(
                    'GET',
                    KintoneApi::generateUrl('records.json', $guestSpaceId),
                    $headers,
                    \GuzzleHttp\json_encode($body)
                );
            }
        };
    }

    /**
     * @param array $result
     * @return array
     */
    private function convertResponseToRecords(array  $result): array
    {
        ksort($result);
        $allRecords = [];
        foreach ($result as $r) {
            /** @var array $records */
            $records = $r['records'];
            foreach ($records as $record) {
                $allRecords[] = $record;
            }
        }

        return $allRecords;
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
    public function post($appId, array $records, $guestSpaceId = null): array
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
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
    public function put($appId, array $records, $guestSpaceId = null): array
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->put(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
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
    public function delete($appId, array $ids, $guestSpaceId = null, array $revisions = []): array
    {
        $options = ['json' => ['app' => $appId, 'ids' => $ids]];
        if (count($revisions) && count($ids) === count($revisions)) {
            $options['json']['revisions'] = $revisions;
        }

        /** @var JsonStream $stream */
        $stream = $this->client
            ->delete(KintoneApi::generateUrl('records.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
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
    public function putStatus($appId, array $records, $guestSpaceId = null): array
    {
        $options = ['json' => ['app' => $appId, 'records' => $records]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->put(KintoneApi::generateUrl('records/status.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
