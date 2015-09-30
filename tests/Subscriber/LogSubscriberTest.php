<?php

namespace CybozuHttp\tests\Subscriber;

use CybozuHttp\Config;
use CybozuHttp\Subscriber\LogSubscriber;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class LogSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEvents()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        $subscriber = new LogSubscriber($config);
        $this->assertArrayHasKey('error', $subscriber->getEvents());
    }
}
