<?php

namespace CybozuHttp\Subscriber;

use GuzzleHttp\Subscriber\Log\LogSubscriber as BaseLogSubscriber;
use GuzzleHttp\Event\RequestEvents;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class LogSubscriber extends BaseLogSubscriber
{
    public function getEvents()
    {
        return [
            'error'    => ['onError', RequestEvents::EARLY]
        ];
    }
}