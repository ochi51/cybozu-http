<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use GuzzleHttp\Exception\RequestException;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class RecordTest extends \PHPUnit_Framework_TestCase
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
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $this->space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testRecord()
    {
        $postRecord = KintoneTestHelper::getRecord();

        $id = $this->api->record()->post($this->appId, $postRecord)['id'];
        $record = $this->api->record()->get($this->appId, $id);
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            self::assertEquals($field['value'], $record[$code]['value']);
        }

        $this->api->record()->put($this->appId, $id, [
            'single_text' => ['value' => 'change single_text value']
        ]);
        $record = $this->api->record()->get($this->appId, $id);
        self::assertEquals('change single_text value', $record['single_text']['value']);
        $this->api->record()->delete($this->appId, $id);
        $count = $this->api->records()->get($this->appId)['totalCount'];
        self::assertEquals(0, $count);


        $id = $this->api->record()
            ->post($this->guestAppId, $postRecord, $this->guestSpaceId)['id'];
        $record = $this->api->record()->get($this->guestAppId, $id, $this->guestSpaceId);
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            self::assertEquals($field['value'], $record[$code]['value']);
        }

        $this->api->record()->put($this->guestAppId, $id, [
            'single_text' => ['value' => 'change single_text value']
        ], $this->guestSpaceId);
        $record = $this->api->record()->get($this->guestAppId, $id, $this->guestSpaceId);
        self::assertEquals('change single_text value', $record['single_text']['value']);
        $this->api->record()->delete($this->guestAppId, $id, $this->guestSpaceId);
        $count = $this->api->records()->get($this->guestAppId, '', $this->guestSpaceId)['totalCount'];
        self::assertEquals(0, $count);
    }

    public function testStatus()
    {
        // kintone does not have the get process api.
        $id = KintoneTestHelper::postTestRecord($this->appId);
        try {
            $this->api->record()->putStatus($this->appId, $id, 'sample', 'test@example.com');
        } catch (RequestException $e) {}
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
