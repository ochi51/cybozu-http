<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class RecordsTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var int
     */
    private $spaceId;

    /**
     * @var int
     */
    private $guestSpaceId;

    /**
     * @var int
     */
    private $appId;

    /**
     * @var int
     */
    private $guestAppId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($this->spaceId);
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testRecords(): void
    {
        $postRecord = KintoneTestHelper::getRecord();
        $fields = array_keys($postRecord);

        $ids = $this->api->records()->post(
            $this->appId,
            [$postRecord, $postRecord, $postRecord, $postRecord, $postRecord]
        )['ids'];

        $resp = $this->api->records()->get($this->appId, '', null, true, $fields);
        $record = $resp['records'][0];
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            $this->assertEquals($field['value'], $record[$code]['value']);
        }
        $this->assertEquals(5, $resp['totalCount']);

        $this->api->records()->put($this->appId, [[
            'id' => $ids[0],
            'record' => [
                'single_text' => ['value' => 'change single_text value']
            ]
        ]]);
        $record = $this->api->record()->get($this->appId, $ids[0]);
        $this->assertEquals('change single_text value', $record['single_text']['value']);

        $this->api->records()->delete($this->appId, [1]);
        $resp = $this->api->records()->get($this->appId);
        $record = $resp['records'][0];
        $this->assertEquals(4, $resp['totalCount']);
        $this->assertNotEquals('change single_text value', $record['single_text']['value']);


        $ids = $this->api->records()->post(
            $this->guestAppId,
            [$postRecord, $postRecord, $postRecord, $postRecord, $postRecord],
            $this->guestSpaceId
        )['ids'];

        $resp = $this->api->records()
            ->get($this->guestAppId, '', $this->guestSpaceId, true, $fields);
        $record = $resp['records'][0];
        foreach ($postRecord as $code => $field) {
            if ($code === 'table') {
                continue;
            }
            $this->assertEquals($field['value'], $record[$code]['value']);
        }
        $this->assertEquals(5, $resp['totalCount']);

        $this->api->records()->put($this->guestAppId, [[
            'id' => $ids[0],
            'record' => [
                'single_text' => ['value' => 'change single_text value']
            ]
        ]], $this->guestSpaceId);
        $record = $this->api->record()
            ->get($this->guestAppId, $ids[0], $this->guestSpaceId);
        $this->assertEquals('change single_text value', $record['single_text']['value']);

        $this->api->records()->delete($this->guestAppId, [1], $this->guestSpaceId, [2]);
        $resp = $this->api->records()
            ->get($this->guestAppId, '', $this->guestSpaceId);
        $record = $resp['records'][0];
        $this->assertEquals(4, $resp['totalCount']);
        $this->assertNotEquals('change single_text value', $record['single_text']['value']);
    }

    public function testAll(): void
    {
        $postRecord = KintoneTestHelper::getRecord();
        $fields = array_keys($postRecord);

        $this->api->records()->post(
            $this->appId,
            array_fill(0, 5, $postRecord)
        );

        $allRecords = $this->api->records()->all($this->appId);
        $this->assertCount(5, $allRecords);

        $record100 = array_fill(0, 100, $postRecord);
        $this->api->records()->post($this->appId, $record100);
        $this->api->records()->post($this->appId, $record100);
        $this->api->records()->post($this->appId, $record100);
        $this->api->records()->post($this->appId, $record100);
        $this->api->records()->post($this->appId, $record100);

        $allRecords = $this->api->records()->all($this->appId, '', null, $fields);
        $this->assertCount(505, $allRecords);
    }

    public function testStatus(): void
    {
        // kintone does not have the get process api. so can not test.
        $id = KintoneTestHelper::postTestRecord($this->appId);
        $this->api->records()->putStatus($this->appId, [
            [
                'id' => $id,
                'action' => 'sample',
                'assignee' => KintoneTestHelper::getConfig()['login']
            ]
        ]);
        $this->assertTrue(true);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
