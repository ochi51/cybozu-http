<?php

namespace CybozuHttp\Api;

use CybozuHttp\Client;
use CybozuHttp\Api\Kintone\App;
use CybozuHttp\Api\Kintone\Apps;
use CybozuHttp\Api\Kintone\PreviewApp;
use CybozuHttp\Api\Kintone\Record;
use CybozuHttp\Api\Kintone\Records;
use CybozuHttp\Api\Kintone\Cursor;
use CybozuHttp\Api\Kintone\File;
use CybozuHttp\Api\Kintone\Comment;
use CybozuHttp\Api\Kintone\Comments;
use CybozuHttp\Api\Kintone\Graph;
use CybozuHttp\Api\Kintone\Space;
use CybozuHttp\Api\Kintone\Thread;
use CybozuHttp\Api\Kintone\Guests;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class KintoneApi
{
    public const API_PREFIX = '/k/v1/';
    public const GUEST_SPACE_PREFIX = '/k/guest/';

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
     * @var Cursor
     */
    private $cursor;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var Comments
     */
    private $comments;

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

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->app = new App($client);
        $this->apps = new Apps($client);
        $this->preview = new PreviewApp($client);
        $this->record = new Record($client);
        $this->records = new Records($client);
        $this->cursor = new Cursor($client);
        $this->file = new File($client);
        $this->comment = new Comment($client);
        $this->comments = new Comments($client);
        $this->graph = new Graph($client);
        $this->space = new Space($client);
        $this->thread = new Thread($client);
        $this->guests = new Guests($client);
    }

    /**
     * @param string $api
     * @param integer|null $guestSpaceId
     * @return string
     */
    public static function generateUrl($api, $guestSpaceId = null): string
    {
        if ($guestSpaceId && is_numeric($guestSpaceId)) {
            return self::GUEST_SPACE_PREFIX . $guestSpaceId .'/v1/'. $api;
        }

        return self::API_PREFIX . $api;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return App
     */
    public function app(): App
    {
        return $this->app;
    }

    /**
     * @return Apps
     */
    public function apps(): Apps
    {
        return $this->apps;
    }

    /**
     * @return PreviewApp
     */
    public function preview(): PreviewApp
    {
        return $this->preview;
    }

    /**
     * @return Record
     */
    public function record(): Record
    {
        return $this->record;
    }

    /**
     * @return Records
     */
    public function records(): Records
    {
        return $this->records;
    }

    /**
     * @return Cursor
     */
    public function cursor(): Cursor
    {
        return $this->cursor;
    }

    /**
     * @return File
     */
    public function file(): File
    {
        return $this->file;
    }

    /**
     * @return Comment
     */
    public function comment(): Comment
    {
        return $this->comment;
    }

    /**
     * @return Comments
     */
    public function comments(): Comments
    {
        return $this->comments;
    }

    /**
     * @return Graph
     */
    public function graph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return Space
     */
    public function space(): Space
    {
        return $this->space;
    }

    /**
     * @return Thread
     */
    public function thread(): Thread
    {
        return $this->thread;
    }

    /**
     * @return Guests
     */
    public function guests(): Guests
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
    public function postBulkRequest(array $requests, $guestSpaceId = null): array
    {
        $options = ['json' => ['requests' => $requests]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(self::generateUrl('bulkRequest.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize()['results'];
    }
}
