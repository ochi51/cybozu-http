<?php

namespace CybozuHttp\Tests\Api;

require_once __DIR__ . '/../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Api\Kintone\App;
use CybozuHttp\Api\Kintone\Apps;
use CybozuHttp\Api\Kintone\File;
use CybozuHttp\Api\Kintone\Graph;
use CybozuHttp\Api\Kintone\Guests;
use CybozuHttp\Api\Kintone\PreviewApp;
use CybozuHttp\Api\Kintone\Record;
use CybozuHttp\Api\Kintone\Records;
use CybozuHttp\Api\Kintone\Space;
use CybozuHttp\Api\Kintone\Thread;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class KintoneApiTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var KintoneApi
     */
    private $api;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
    }

    public function testGenerateUrl()
    {
        self::assertEquals('/k/v1/record.json', KintoneApi::generateUrl('record.json'));
        self::assertEquals('/k/guest/123/v1/record.json', KintoneApi::generateUrl('record.json', 123));
    }

    public function testGetClient()
    {
        self::assertTrue($this->api->getClient() instanceof Client);
    }

    public function testApp()
    {
        self::assertTrue($this->api->app() instanceof App);
    }

    public function testApps()
    {
        self::assertTrue($this->api->apps() instanceof Apps);
    }

    public function testPreview()
    {
        self::assertTrue($this->api->preview() instanceof PreviewApp);
    }

    public function testRecord()
    {
        self::assertTrue($this->api->record() instanceof Record);
    }

    public function testRecords()
    {
        self::assertTrue($this->api->records() instanceof Records);
    }

    public function testFile()
    {
        self::assertTrue($this->api->file() instanceof File);
    }

    public function testGraph()
    {
        self::assertTrue($this->api->graph() instanceof Graph);
    }

    public function testSpace()
    {
        self::assertTrue($this->api->space() instanceof Space);
    }

    public function testThread()
    {
        self::assertTrue($this->api->thread() instanceof Thread);
    }

    public function testGuests()
    {
        self::assertTrue($this->api->guests() instanceof Guests);
    }

    public function testPostBulkRequest()
    {
        $spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($spaceId);
        $appId = KintoneTestHelper::createTestApp($spaceId, $space['defaultThread']);
        $recordId = KintoneTestHelper::postTestRecord($appId);
        $requests = [
            [
                "method" => "DELETE",
                "api" => KintoneApi::generateUrl('records.json'),
                "payload" => [
                    "app" => $appId,
                    "ids" => [$recordId]
                ]
            ]
        ];

        $result = $this->api->postBulkRequest($requests);
        self::assertArrayNotHasKey('message', $result);
    }
}
