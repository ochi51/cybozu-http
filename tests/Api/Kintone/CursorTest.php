<?php

namespace CybozuHttp\Tests\Api\Kintone;

use CybozuHttp\Api\KintoneApi;
use KintoneTestHelper;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
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
    private $appId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($this->spaceId);
        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $space['defaultThread']);
    }

    public function testCursor(): void
    {
        $postRecord = KintoneTestHelper::getRecord();
        $this->api->records()->post(
            $this->appId,
            [$postRecord, $postRecord, $postRecord, $postRecord, $postRecord]
        );
        $result = $this->api->cursor()->create($this->appId);
        $cursorId = $result['id'];
        $totalCount = $result['totalCount'];
        $this->assertEquals($totalCount, 5);
        try {
            $this->api->cursor()->delete($cursorId);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        try {
            $this->api->cursor()->delete($cursorId);
            $this->fail('Delete cursor is failed?');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $records = $this->api->cursor()->all($this->appId);
        $this->assertEquals(\count($records), 5);
        try {
            $this->api->cursor()->delete($cursorId);
            $this->fail('Delete cursor is failed?');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
    }
}
