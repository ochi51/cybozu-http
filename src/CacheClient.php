<?php

namespace CybozuHttp;

use Doctrine\Common\Cache\FilesystemCache;
use CybozuHttp\Subscriber\Cache\CacheStorage;
use CybozuHttp\Subscriber\CacheSubscriber;
use GuzzleHttp\Message\RequestInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CacheClient extends Client
{
    /**
     * @var CacheStorage
     */
    private $storage;

    /**
     * CacheClient constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config['use_cache'] = true;
        parent::__construct($config);

        $dir = $this->getConfig()->get('cache_dir');
        $ttl = (int)$this->getConfig()->get('cache_ttl');
        $this->attachCacheSubscriber($dir, $ttl);
    }

    /**
     * @param string $dir
     * @param int    $ttl
     */
    private function attachCacheSubscriber($dir, $ttl = 0)
    {
        $storage = new CacheStorage(new FilesystemCache($dir), $ttl);
        CacheSubscriber::attach($this, $storage);
        $this->storage = $storage;
    }

    /**
     * Clear all cache
     */
    public function cacheClear()
    {
        $dir = $this->getConfig()->get('cache_dir');
        array_map('unlink', glob($dir . "/*/*"));
        foreach (glob($dir . '/*') as $d) {
            rmdir($d);
        }
    }

    /**
     * Delete cache by request
     * @param RequestInterface $request
     */
    public function deleteCache(RequestInterface $request)
    {
        $this->storage->delete($request);
    }
}