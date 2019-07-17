<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class GraphTest extends TestCase
{
    private const OUTPUT_DIR = __DIR__ . '/../../_output/';

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
        $res1 = $this->api->graph()->get($graph['appId'], $graph['reportId']);
        file_put_contents(self::OUTPUT_DIR . 'graph.html', $res1);
        $this->assertTrue(true);

        $res2 = $this->api->graph()->get($graph['appId'], $graph['reportId'], null, true);
        file_put_contents(self::OUTPUT_DIR . 'iframe-graph.html', $res2);
        $this->assertTrue(true);
        $this->assertNotEquals($res1, $res2);
    }
}
