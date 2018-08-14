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
        self::assertInstanceOf(Client::class, $this->api->getClient());
    }

    public function testApp()
    {
        self::assertInstanceOf(App::class, $this->api->app());
    }

    public function testApps()
    {
        self::assertInstanceOf(Apps::class, $this->api->apps());
    }

    public function testPreview()
    {
        self::assertInstanceOf(PreviewApp::class, $this->api->preview());
    }

    public function testRecord()
    {
        self::assertInstanceOf(Record::class, $this->api->record());
    }

    public function testRecords()
    {
        self::assertInstanceOf(Records::class, $this->api->records());
    }

    public function testFile()
    {
        self::assertInstanceOf(File::class, $this->api->file());
    }

    public function testGraph()
    {
        self::assertInstanceOf(Graph::class, $this->api->graph());
    }

    public function testSpace()
    {
        self::assertInstanceOf(Space::class, $this->api->space());
    }

    public function testThread()
    {
        self::assertInstanceOf(Thread::class, $this->api->thread());
    }

    public function testGuests()
    {
        self::assertInstanceOf(Guests::class, $this->api->guests());
    }

    public function testPostBulkRequest()
    {
        $spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($spaceId);
        $appId = KintoneTestHelper::createTestApp($spaceId, $space['defaultThread']);
        $recordId = KintoneTestHelper::postTestRecord($appId);
        $requests = [
            [
                'method' => 'DELETE',
                'api' => KintoneApi::generateUrl('records.json'),
                'payload' => [
                    'app' => $appId,
                    'ids' => [$recordId]
                ]
            ]
        ];

        $result = $this->api->postBulkRequest($requests);
        self::assertArrayNotHasKey('message', $result);
    }
}
