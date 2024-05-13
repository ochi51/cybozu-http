<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ThreadTest extends TestCase
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

    protected function setup(): void
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
    }

    public function testPut(): void
    {
        $space = $this->api->space()->get($this->spaceId);
        $this->api->thread()->put($space['defaultThread'], 'test thread', 'test thread body');
        // kintone does not have the get thread api.
        $this->assertTrue(true);

        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $this->api->thread()->put($guestSpace['defaultThread'], 'test thread', 'test thread body', $this->guestSpaceId);
        // kintone does not have the get thread api.
        $this->assertTrue(true);
    }

    public function testComment(): void
    {
        $space = $this->api->space()->get($this->spaceId);
        $this->api->thread()->comment($this->spaceId, $space['defaultThread'], 'test thread comment');
        // kintone does not have the get thread api.
        $this->assertTrue(true);

        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $this->api->thread()->comment($this->guestSpaceId, $guestSpace['defaultThread'], 'test thread comment', [], [], $this->guestSpaceId);
        // kintone does not have the get thread api.
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
