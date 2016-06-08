<?php

namespace CybozuHttp\Subscriber\Cache;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream;
use Doctrine\Common\Cache\Cache;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CacheStorage
{
    /** @var int Default cache TTL */
    private $defaultTtl;

    /** @var Cache */
    private $cache;

    /**
     * @param Cache  $cache     Cache backend.
     * @param int    $defaultTtl
     */
    public function __construct(Cache $cache, $defaultTtl = 0)
    {
        $this->cache = $cache;
        $this->defaultTtl = $defaultTtl;
    }

    public function cache(RequestInterface $request, ResponseInterface $response)
    {
        $ttl = $this->defaultTtl;
        $key = $this->getCacheKey($request);

        // Persist the response body if needed
        if ($response->getStatusCode() === 200
            && $response->getBody()
            && $response->getBody()->getSize() > 0) {
            $this->cache->save($key, (string)$response->getBody(), $ttl);
        }
    }

    public function delete(RequestInterface $request)
    {
        $key = $this->getCacheKey($request);
        $this->cache->delete($key);
    }

    public function fetch(RequestInterface $request)
    {
        $key = $this->getCacheKey($request);
        if (!($body = $this->cache->fetch($key))) {
            return false;
        }

        $response = new Response(200);
        $response->setBody(Stream\Utils::create($body));

        return $response;
    }

    /**
     * Hash a request URL into a string that returns cache metadata
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getCacheKey(RequestInterface $request)
    {
        return $request->getMethod()
        . ' '
        . $request->getUrl()
        . ' '
        . (string)$request->getBody();
    }
}