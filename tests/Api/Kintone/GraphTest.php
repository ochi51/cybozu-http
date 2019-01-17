<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class GraphTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
    }

    public function testGet(): void
    {
        $graph = KintoneTestHelper::getGraph();
        $res = $this->api->graph()->get($graph['appId'], $graph['reportId']);
        file_put_contents(__DIR__ . '/../../_output/graph.html', $res);
        $this->assertTrue(true);
    }
}
