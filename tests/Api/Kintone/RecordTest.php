<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class RecordTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private KintoneApi $api;

    /**
     * @var int
     */
    private int $spaceId;

    /**
     * @var int|null
     */
    private ?int $guestSpaceId;

    /**
     * @var int
     */
    private int $appId;

    /**
     * @var int|null
     */
    private ?int $guestAppId;

    protected function setup(): void
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($this->spaceId);
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testRecord(): void
    {
        $postRecord = KintoneTestHelper::getRecord();

        $id = $this->api->record()->post($this->appId, $postRecord)['id'];
        $record = $this->api->record()->get($this->appId, $id);
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            $this->assertEquals($field['value'], $record[$code]['value']);
        }

        $this->api->record()->put($this->appId, $id, [
            'single_text' => ['value' => 'change single_text value']
        ]);
        $record = $this->api->record()->get($this->appId, $id);
        $this->assertEquals('change single_text value', $record['single_text']['value']);
        $this->api->record()->delete($this->appId, $id);
        $count = $this->api->records()->get($this->appId)['totalCount'];
        $this->assertEquals(0, $count);


        $id = $this->api->record()
            ->post($this->guestAppId, $postRecord, $this->guestSpaceId)['id'];
        $record = $this->api->record()->get($this->guestAppId, $id, $this->guestSpaceId);
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            $this->assertEquals($field['value'], $record[$code]['value']);
        }

        $this->api->record()->put($this->guestAppId, $id, [
            'single_text' => ['value' => 'change single_text value']
        ], $this->guestSpaceId);
        $record = $this->api->record()->get($this->guestAppId, $id, $this->guestSpaceId);
        $this->assertEquals('change single_text value', $record['single_text']['value']);
        $this->api->record()->delete($this->guestAppId, $id, $this->guestSpaceId);
        $count = $this->api->records()->get($this->guestAppId, '', $this->guestSpaceId)['totalCount'];
        $this->assertEquals(0, $count);
    }

    public function testStatus(): void
    {
        // kintone does not have the get process api.
        $id = KintoneTestHelper::postTestRecord($this->appId);
        $this->api->record()->putStatus($this->appId, $id, 'sample', KintoneTestHelper::getConfig()['login']);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
