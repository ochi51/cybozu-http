<?php

namespace CybozuHttp\Subscriber;

use CybozuHttp\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Event\HasEmitterInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CacheSubscriber implements SubscriberInterface
{
    /** @var CacheStorage $cache Object used to cache responses */
    protected $storage;

    /**
     * @param CacheStorage $cache    Cache storage
     */
    public function __construct(CacheStorage $cache)
    {
        $this->storage = $cache;
    }

    /**
     * @param HasEmitterInterface $subject
     * @param CacheStorage|null $storage
     * @return array
     */
    public static function attach(HasEmitterInterface $subject, CacheStorage $storage)
    {
        $emitter = $subject->getEmitter();
        $cache = new self($storage);
        $emitter->attach($cache);

        return ['subscriber' => $cache, 'storage' => $storage];
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return [
            'before'   => ['onBefore', RequestEvents::LATE],
            'complete' => ['onComplete', RequestEvents::EARLY]
        ];
    }

    /**
     * Checks if a request can be cached, and if so, intercepts with a cached
     * response is available.
     */
    public function onBefore(BeforeEvent $event)
    {
        $request = $event->getRequest();

        if (!($response = $this->storage->fetch($request))) {
            $this->cacheMiss($request);
            return;
        }

        $request->getConfig()->set('cache_lookup', 'HIT');
        $request->getConfig()->set('cache_hit', true);
        $event->intercept($response);
    }

    /**
     * Checks if the request and response can be cached, and if so, store it
     */
    public function onComplete(CompleteEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Cache the response if it can be cached and isn't already
        if ($request->getConfig()->get('cache_lookup') === 'MISS') {
            $this->storage->cache($request, $response);
        }

        $this->addResponseHeaders($request, $response);
    }

    private function cacheMiss(RequestInterface $request)
    {
        $request->getConfig()->set('cache_lookup', 'MISS');
    }

    private function addResponseHeaders(RequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getConfig();
        $lookup = $params['cache_lookup'] . ' from GuzzleCache';
        $response->addHeader('X-Cache-Lookup', $lookup);

        if ($params['cache_hit'] === true) {
            $response->addHeader('X-Cache', 'HIT from GuzzleCache');
        } else {
            $response->addHeader('X-Cache', 'MISS from GuzzleCache');
        }
    }
}