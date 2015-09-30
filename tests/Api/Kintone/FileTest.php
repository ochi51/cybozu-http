<?php

namespace CybozuHttp\tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use CybozuHttp\Api\Kintone\File;
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
    private $appId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->space = $this->api->space()->get($this->spaceId);
        $this->appId = $this->api->preview()
            ->post('test app', $this->spaceId, $this->space['defaultThread'])['app'];
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
        $this->assertEquals(file_get_contents($filename), $content);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
    }
}
