<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Graph
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
     * @param integer $appId
     * @param integer $id
     * @param integer|null $guestSpaceId
     * @param bool $isIframe
     * @param bool $isShowTitle
     * @param array $options
     * @return string
     */
    public function get($appId, $id, $guestSpaceId = null, $isIframe = false, $isShowTitle = false, array $options = []): string
    {
        $url =  $guestSpaceId ? KintoneApi::GUEST_SPACE_PREFIX . $guestSpaceId . '/' : '/k/';
        $url .= $appId . '/report';
        if ($isIframe) {
            $url .= '/portlet';
        }

        $options = [
            'query' => [
                'report' => $id,
                'title' => $isShowTitle
            ]
        ] + $options;

        return (string)$this->client
            ->get($url, $options)
            ->getBody();
    }
}
