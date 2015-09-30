<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Organizations
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
     * Get organizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124754
     *
     * @return string
     */
    public function getByCsv()
    {
        return $this->csv->get('organization');
    }

    /**
     * Post organizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202350640
     *
     * @param $filename
     * @return int
     */
    public function postByCsv($filename)
    {
        return $this->csv->post('organization', $filename);
    }
}