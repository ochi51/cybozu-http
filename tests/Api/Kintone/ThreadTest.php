<?php

namespace CybozuHttp\tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ThreadTest extends \PHPUnit_Framework_TestCase
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
     * @var integer
     */
    private $guestSpaceId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
    }

    public function testPut()
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

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
