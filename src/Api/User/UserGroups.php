<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserGroups
{
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
     * Get userGroups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124784
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv()
    {
        return $this->csv->get('userGroups');
    }

    /**
     * Post userGroups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202362890
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('userGroups', $filename);
    }
}