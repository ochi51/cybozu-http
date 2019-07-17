<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Client;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Cursor
{
    public const MAX_GET_RECORDS = 500;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all records
     * https://developer.cybozu.io/hc/ja/articles/360029152012
     *
     * @param int $appId
     * @param string $query
     * @param int|null $guestSpaceId
     * @param array|null $fields
     * @return array
     */
    public function all($appId, $query = '', $guestSpaceId = null, $fields = null): array
    {
        $records = [];
        $cursorId = $this->create($appId, $query, $guestSpaceId, $fields)['id'];

        while(true) {
            $result = $this->proceed($cursorId, $guestSpaceId);
            array_push($records, ...$result['records']);
            if (!$result['next']) {
                break;
            }
        }

        return $records;
    }

    /**
     * https://developer.cybozu.io/hc/ja/articles/360029152012#step1
     *
     * @param int $appId
     * @param string $query
     * @param int|null $guestSpaceId
     * @param array|null $fields
     * @return array ['id' => $cursorId, 'totalCount' => $totalCount]
     */
    public function create($appId, $query = '', $guestSpaceId = null, $fields = null): array
    {
        $options = ['json' => ['app' => $appId, 'query' => $query, 'size' => self::MAX_GET_RECORDS]];
        if ($fields) {
            $options['json']['fields'] = $fields;
        }
        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('records/cursor.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * https://developer.cybozu.io/hc/ja/articles/360029152012#step2
     *
     * @param string $cursorId
     * @param int|null $guestSpaceId
     * @return array|null
     */
    public function proceed(string $cursorId, $guestSpaceId = null): ?array
    {
        $options = ['json' => ['id' => $cursorId]];
        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('records/cursor.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * https://developer.cybozu.io/hc/ja/articles/360029152012#step3
     *
     * @param string $cursorId
     * @param int|null $guestSpaceId
     * @return array|null
     */
    public function delete(string $cursorId, $guestSpaceId = null): ?array
    {
        $options = ['json' => ['id' => $cursorId]];
        /** @var JsonStream $stream */
        $stream = $this->client
            ->delete(KintoneApi::generateUrl('records/cursor.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
