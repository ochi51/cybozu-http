<?php

namespace CybozuHttp\Api;

use CybozuHttp\Client;
use CybozuHttp\Api\Kintone\App;
use CybozuHttp\Api\Kintone\Apps;
use CybozuHttp\Api\Kintone\PreviewApp;
use CybozuHttp\Api\Kintone\Record;
use CybozuHttp\Api\Kintone\Records;
use CybozuHttp\Api\Kintone\File;
use CybozuHttp\Api\Kintone\Comment;
use CybozuHttp\Api\Kintone\Comments;
use CybozuHttp\Api\Kintone\Graph;
use CybozuHttp\Api\Kintone\Space;
use CybozuHttp\Api\Kintone\Thread;
use CybozuHttp\Api\Kintone\Guests;
use CybozuHttp\Api\Kintone\Form;
use CybozuHttp\Api\Kintone\Fields;
use CybozuHttp\Api\Kintone\Layout;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class KintoneApi
{
    const API_PREFIX = '/k/v1/';
    const GUEST_SPACE_PREFIX = '/k/guest/';

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

    /**
     * @var Form
     */
    private $form;

    /**
     * @var Fields
     */
    private $fields;

    /**
     * @var Layout
     */
    private $layout;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->app = new App($client);
        $this->apps = new Apps($client);
        $this->preview = new PreviewApp($client);
        $this->record = new Record($client);
        $this->records = new Records($client);
        $this->file = new File($client);
        $this->comment = new Comment($client);
        $this->comments = new Comments($client);
        $this->graph = new Graph($client);
        $this->space = new Space($client);
        $this->thread = new Thread($client);
        $this->guests = new Guests($client);
        $this->form = new Form($client);
        $this->fields = new Fields($client);
        $this->layout = new Layout($client);
    }

    /**
     * @param string $api
     * @param integer|null $guestSpaceId
     * @return string
     */
    public static function generateUrl($api, $guestSpaceId = null)
    {
        if ($guestSpaceId && is_numeric($guestSpaceId)) {
            return self::GUEST_SPACE_PREFIX . $guestSpaceId .'/v1/'. $api;
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
     * @return Comment
     */
    public function comment()
    {
        return $this->comment;
    }

    /**
     * @return Comments
     */
    public function comments()
    {
        return $this->comments;
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
     * @return Form
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @return Fields
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @return Layout
     */
    public function layout()
    {
        return $this->layout;
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

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(self::generateUrl('bulkRequest.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize()['results'];
    }
}
