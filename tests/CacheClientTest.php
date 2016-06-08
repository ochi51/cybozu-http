<?php

namespace CybozuHttp\Tests;

require_once __DIR__ . '/_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;
use CybozuHttp\CacheClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CacheClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $config;

    protected function setup()
    {
        $yml = Yaml::parse(__DIR__ . '/../parameters.yml');
        $this->config = $yml['parameters'];
        $this->config['debug']     = true;
        $this->config['logfile']   = __DIR__ . '/_output/connection.log';
        $this->config['use_cache'] = true;
        $this->config['cache_dir'] = __DIR__ . '/_output/cache';
        $this->config['cache_ttl'] = 60;
    }

    public function testCache()
    {
        $client = new CacheClient($this->config);
        $api = new KintoneApi($client);
        $spaceId = KintoneTestHelper::createTestSpace();

        $space = $api->space()->get($spaceId);
        $api->space()->delete($spaceId);

        $cachedSpace = $api->space()->get($spaceId);
        $this->assertEquals($space, $cachedSpace);

        $url = KintoneApi::generateUrl('space.json');
        $options = ['json' => ['id' => $spaceId]];
        $request = $client->createRequest('GET', $url, $options);
        $client->deleteCache($request);

        try {
            $api->space()->get($spaceId);
            $this->fail('Not throw JsonResponseException.');
        } catch (RequestException $e) {
            $this->assertRegExp('/指定されたスペース\(id: (.*)\)が見つかりません。削除されている可能性があります。/', $e->getMessage());
        }
    }

    public function testCacheClear()
    {
        $client = new CacheClient($this->config);
        $client->cacheClear();

        $dir = $client->getConfig()->get('cache_dir');
        $dirs = glob($dir . '/*');
        $this->assertEquals(count($dirs), 0);
    }
}