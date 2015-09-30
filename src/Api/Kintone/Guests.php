<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Guests
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
     * Post guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param array $guests
     * @return array
     */
    public function post(array $guests)
    {
        $options = ['json' => ['guests' => $guests]];

        return $this->client
            ->post(KintoneApi::generateUrl('guests.json'), $options)
            ->json();
    }

    /**
     * Delete guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param array $guests
     * @return array
     */
    public function delete(array $guests)
    {
        $options = ['json' => ['guests' => $guests]];

        return $this->client
            ->delete(KintoneApi::generateUrl('guests.json'), $options)
            ->json();
    }

}