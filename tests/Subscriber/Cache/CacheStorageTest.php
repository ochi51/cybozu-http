<?php

namespace CybozuHttp\Tests\Subscriber\Cache;

use CybozuHttp\Subscriber\Cache\CacheStorage;
use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CacheStorageTest extends \PHPUnit_Framework_TestCase
{
    const CACHE_DIR = __DIR__ . '/../../_output/cache';

    public function testCache()
    {
        $request = new Request('GET', 'http://127.0.0.1/' . time());
        $response = new Response(200, [], Stream::factory('test'));

        $storage = new CacheStorage(new FilesystemCache(self::CACHE_DIR), 0);
        $storage->cache($request, $response);

        $cacheResponse = $storage->fetch($request);
        $this->assertEquals((string)$response->getBody(), (string)$cacheResponse->getBody());

        $storage->delete($request);
        $this->assertFalse($storage->fetch($request));
    }
}
