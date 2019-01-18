<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Users
{
    public const MAX_GET_USERS = 100;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Csv
     */
    private $csv;

    public function __construct(Client $client, Csv $csv)
    {
        $this->client = $client;
        $this->csv = $csv;
    }

    /**
     * Get users
     * https://cybozudev.zendesk.com/hc/ja/articles/202363040#step2
     *
     * @param array $ids
     * @param array $codes
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function get(array $ids = [], array $codes = [], $offset = 0, $limit = self::MAX_GET_USERS): array
    {
        $options = ['json' => compact('ids', 'codes')];
        $options['json']['size'] = $limit;
        $options['json']['offset'] = $offset;

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(UserApi::generateUrl('users.json'), $options)
            ->getBody();

        return $stream->jsonSerialize()['users'];
    }

    /**
     * Get users by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363040#step1
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('user');
    }

    /**
     * Post users by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202111404
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('user', $filename);
    }
}
