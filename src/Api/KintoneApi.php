<?php

namespace CybozuHttp\Api;

use CybozuHttp\Client;
use CybozuHttp\Api\Kintone\App;
use CybozuHttp\Api\Kintone\Apps;
use CybozuHttp\Api\Kintone\PreviewApp;
use CybozuHttp\Api\Kintone\Record;
use CybozuHttp\Api\Kintone\Records;
use CybozuHttp\Api\Kintone\File;
use CybozuHttp\Api\Kintone\Graph;
use CybozuHttp\Api\Kintone\Space;
use CybozuHttp\Api\Kintone\Thread;
use CybozuHttp\Api\Kintone\Guests;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class KintoneApi
{
    const API_PREFIX = "/k/v1/";
    const GUEST_SPACE_PREFIX = "/k/guest/";

    /**
     * @var Client
     */
    private $client;

    /**
     * @var App
     */
    private $app;

    /**
     * @var Apps
     */
    private $apps;

    /**
     * @var PreviewApp
     */
    private $preview;

    /**
     * @var Record
     */
    private $record;

    /**
     * @var Records
     */
    private $records;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Graph
     */
    private $graph;

    /**
     * @var Space
     */
    private $space;

    /**
     * @var Thread
     */
    private $thread;

    /**
     * @var Guests
     */
    private $guests;

    public function __construct(
        Client $client,
        App $app = null,
        Apps $apps = null,
        PreviewApp $preview = null,
        Record $record = null,
        Records $records = null,
        File $file = null,
        Graph $graph = null,
        Space $space = null,
        Thread $thread = null,
        Guests $guests = null)
    {
        $this->client = $client;
        $this->app = $app ? $app : new App($client);
        $this->apps = $apps ? $apps : new Apps($client);
        $this->preview = $preview ? $preview : new PreviewApp($client);
        $this->record = $record ? $record : new Record($client);
        $this->records = $records ? $records : new Records($client);
        $this->file = $file ? $file : new File($client);
        $this->graph = $graph ? $graph : new Graph($client);
        $this->space = $space ? $space : new Space($client);
        $this->thread = $thread ? $thread : new Thread($client);
        $this->guests = $guests ? $guests : new Guests($client);
    }

    /**
     * @param string $api
     * @param integer|null $guestSpaceId
     * @return string
     */
    public static function generateUrl($api, $guestSpaceId = null)
    {
        if ($guestSpaceId !== null) {
            return self::GUEST_SPACE_PREFIX . $guestSpaceId . "/v1/" . $api;
        }

        return self::API_PREFIX . $api;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return App
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * @return Apps
     */
    public function apps()
    {
        return $this->apps;
    }

    /**
     * @return PreviewApp
     */
    public function preview()
    {
        return $this->preview;
    }

    /**
     * @return Record
     */
    public function record()
    {
        return $this->record;
    }

    /**
     * @return Records
     */
    public function records()
    {
        return $this->records;
    }

    /**
     * @return File
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @return Graph
     */
    public function graph()
    {
        return $this->graph;
    }

    /**
     * @return Space
     */
    public function space()
    {
        return $this->space;
    }

    /**
     * @return Thread
     */
    public function thread()
    {
        return $this->thread;
    }

    /**
     * @return Guests
     */
    public function guests()
    {
        return $this->guests;
    }

    /**
     * Post bulkRequest
     * https://cybozudev.zendesk.com/hc/ja/articles/201941814
     *
     * @param array $requests
     * @param integer $guestSpaceId
     * @return array
     */
    public function postBulkRequest(array $requests, $guestSpaceId = null)
    {
        $options = ['json' => ['requests' => $requests]];

        return $this->client
            ->post(KintoneApi::generateUrl('bulkRequest.json', $guestSpaceId), $options)
            ->json()['results'];
    }
}