<?php

namespace CybozuHttp\Tests\Api\Kintone;

use CybozuHttp\Api\KintoneApi;
use Exception;
use KintoneTestHelper;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
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
     * @var int
     */
    private int $appId;

    protected function setup(): void
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
        $this->assertEquals(5, $totalCount);
        try {
            $this->api->cursor()->delete($cursorId);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        try {
            $this->api->cursor()->delete($cursorId);
            $this->fail('Delete cursor is failed?');
        } catch (Exception) {
            $this->assertTrue(true);
        }

        $records = $this->api->cursor()->all($this->appId);
        $this->assertCount(5, $records);
        try {
            $this->api->cursor()->delete($cursorId);
            $this->fail('Delete cursor is failed?');
        } catch (Exception) {
            $this->assertTrue(true);
        }
    }

    protected function tearDown(): void
    {
        $this->api->space()->delete($this->spaceId);
    }
}
