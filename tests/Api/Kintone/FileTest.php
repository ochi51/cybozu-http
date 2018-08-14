<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var integer
     */
    private $spaceId;

    /**
     * @var array
     */
    private $space;

    /**
     * @var integer
     */
    private $guestSpaceId;

    /**
     * @var array
     */
    private $guestSpace;

    /**
     * @var integer
     */
    private $appId;

    /**
     * @var integer
     */
    private $guestAppId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->space = $this->api->space()->get($this->spaceId);
        $this->appId = $this->api->preview()
            ->post('test app', $this->spaceId, $this->space['defaultThread'])['app'];

        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $this->guestAppId = $this->api->preview()
            ->post('test app', $this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId)['app'];
    }

    public function testFile()
    {
        $filename = __DIR__ . '/../../_data/sample.js';
        $key = $this->api->file()->post($filename);

        $this->api->preview()->putCustomize($this->appId, [[
            'type' => 'FILE',
            'file' => ['fileKey' => $key]
        ]]);
        $fileKey = $this->api->preview()
            ->getCustomize($this->appId)['desktop']['js'][0]['file']['fileKey'];

        $content = $this->api->file()->get($fileKey);
        self::assertStringEqualsFile($filename, $content);

        $key = $this->api->file()->post($filename, $this->guestSpaceId, 'sample.js');

        $this->api->preview()->putCustomize($this->guestAppId, [[
            'type' => 'FILE',
            'file' => ['fileKey' => $key]
        ]], [], [], $this->guestSpaceId);
        $fileKey = $this->api->preview()
            ->getCustomize($this->guestAppId, $this->guestSpaceId)['desktop']['js'][0]['file']['fileKey'];

        $content = $this->api->file()->get($fileKey, $this->guestSpaceId);
        self::assertStringEqualsFile($filename, $content);
    }

    public function testFileStream()
    {
        $filename = __DIR__ . '/../../_data/sample.js';
        $key = $this->api->file()->post($filename);

        $this->api->preview()->putCustomize($this->appId, [[
            'type' => 'FILE',
            'file' => ['fileKey' => $key]
        ]]);
        $fileKey = $this->api->preview()
            ->getCustomize($this->appId)['desktop']['js'][0]['file']['fileKey'];

        $response = $this->api->file()->getStreamResponse($fileKey);
        self::assertStringEqualsFile($filename, $response->getBody()->read(1024));
    }

    public function testMultiFile()
    {
        $filename = __DIR__ . '/../../_data/sample.js';
        $fileNames = array_fill(0, 10, $filename);
        $keys = $this->api->file()->multiPost($fileNames);
        $files = [];
        foreach ($keys as $key) {
            $files[] = ['type' => 'FILE', 'file' => ['fileKey' => $key]];
        }
        $this->api->preview()->putCustomize($this->appId, $files);
        $js = $this->api->preview()->getCustomize($this->appId)['desktop']['js'];
        $fileKeys = [];
        foreach ($js as $file) {
            $fileKeys[] = $file['file']['fileKey'];
        }
        $results = $this->api->file()->multiGet($fileKeys);
        $content = file_get_contents($filename);
        foreach ($results as $body) {
            self::assertEquals($content, $body);
        }
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
