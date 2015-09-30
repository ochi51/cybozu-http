<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Titles
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
     * Get titles by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124764
     *
     * @return string
     */
    public function getByCsv()
    {
        return $this->csv->get('title');
    }

    /**
     * Post titles by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202361180
     *
     * @param $filename
     * @return int
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('title', $filename);
    }
}