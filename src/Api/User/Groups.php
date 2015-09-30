<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Groups
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
     * Get groups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363060
     *
     * @return string
     */
    public function getByCsv()
    {
        return $this->csv->get('group');
    }

    /**
     * Post groups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202362870
     *
     * @param $filename
     * @return int
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('group', $filename);
    }
}