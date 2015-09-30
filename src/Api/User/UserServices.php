<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserServices
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
     * Get userServices by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363070
     *
     * @return string
     */
    public function getByCsv()
    {
        return $this->csv->get('userServices');
    }

    /**
     * Post userServices by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124734
     *
     * @param $filename
     * @return int
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('userServices', $filename);
    }
}