<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

class ApisTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private KintoneApi $api;

    protected function setup(): void
    {
        $this->api = KintoneTestHelper::getKintoneApi();
    }

    public function testGet(): void {
        $apis = $this->api->apis()->get();
        $this->assertTrue(isset($apis['baseUrl']));
        $this->assertIsArray($apis['apis']);
    }

}
