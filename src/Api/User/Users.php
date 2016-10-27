<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Users
{
    const MAX_GET_USERS = 100;

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
    public function get(array $ids = [], array $codes = [], $offset = 0, $limit = self::MAX_GET_USERS)
    {
        $options = ['json' => [
            'ids' => (!empty($ids) ? $ids : []),
            'codes' => (!empty($codes) ? $codes : [])
        ]];
        $options['json']['size'] = $limit;
        $options['json']['offset'] = $offset;

        return $this->client
            ->get(UserApi::generateUrl('users.json'), $options)
            ->getBody()->jsonSerialize()['users'];
    }

    /**
     * Get users by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363040#step1
     *
     * @return string
     */
    public function getByCsv()
    {
        return $this->csv->get('user');
    }

    /**
     * Post users by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202111404
     *
     * @param $filename
     * @return int
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('user', $filename);
    }
}