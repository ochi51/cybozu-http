<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class AppsTest extends TestCase
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
     * @var array
     */
    private array $space;

    /**
     * @var int|null
     */
    private ?int $guestSpaceId;

    /**
     * @var array|null
     */
    private ?array $guestSpace;

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
        $this->space = $this->api->space()->get($this->spaceId);
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $this->space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testGet(): void
    {
        $app = $this->api->apps()->get([$this->appId], [], null, [$this->spaceId])['apps'][0];
        $this->assertEquals($app['appId'], $this->appId);
        $this->assertEquals('cybozu-http test app', $app['name']);
        $this->assertEquals($app['spaceId'], $this->spaceId);
        $this->assertEquals($app['threadId'], $this->space['defaultThread']);

        $app = $this->api->apps()->get([$this->guestAppId], [], null, [$this->guestSpaceId], 100, 0, $this->guestSpaceId)['apps'][0];
        $this->assertEquals($app['appId'], $this->guestAppId);
        $this->assertEquals('cybozu-http test app', $app['name']);
        $this->assertEquals($app['spaceId'], $this->guestSpaceId);
        $this->assertEquals($app['threadId'], $this->guestSpace['defaultThread']);
    }

    protected function tearDown(): void
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
