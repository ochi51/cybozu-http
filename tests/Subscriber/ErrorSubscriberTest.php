<?php

namespace CybozuHttp\Tests\Subscriber;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use CybozuHttp\Config;
use CybozuHttp\Subscriber\ErrorSubscriber;
use CybozuHttp\Exception\FailedAuthException;
use CybozuHttp\Exception\JsonResponseException;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ErrorSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ErrorSubscriber
     */
    private $subscriber;

    protected function setup()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);

        $this->subscriber = new ErrorSubscriber($config);
    }

    public function testGetEvents()
    {
        $this->assertArrayHasKey('error', $this->subscriber->getEvents());
    }

    public function testAuthError()
    {
        $stream = new Stream(fopen('data://text/plain,' . '<html></html>','r'));
        $response = new Response(401, [], $stream);

        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('authError');
        $method->setAccessible(true);
        try {
            $method->invoke($this->subscriber, $response);
            $this->fail("Not throw FailedAuthException");
        } catch (FailedAuthException $e) {
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }
    }

    public function testJsonError()
    {
        $json = json_encode([
            "message" => 'json error response.',
            "errors" => [
                "sample" => [
                    "messages" => [
                        "error message1",
                        "error message2"
                    ]
                ]
            ]
        ]);
        $stream = new Stream(fopen('data://text/plain,' . $json,'r'));
        $response = new Response(404, [], $stream);

        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('jsonError');
        $method->setAccessible(true);
        try {
            $method->invoke($this->subscriber, $response);
            $this->fail("Not throw JsonResponseException");
        } catch (JsonResponseException $e) {
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }

        $stream = new Stream(fopen('data://text/plain,' . 'failed decode string','r'));
        $response = new Response(404, [], $stream);

        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('jsonError');
        $method->setAccessible(true);
        try {
            $res = $method->invoke($this->subscriber, $response);
            $this->assertFalse($res);
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }
    }

    public function testAddErrorMessages()
    {
        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('addErrorMessages');
        $method->setAccessible(true);

        $errors = [
            "sample" => [
                "messages" => [
                    "error message1",
                    "error message2"
                ]
            ]
        ];
        $message = $method->invoke($this->subscriber, $errors);
        $this->assertEquals(" (sample : error message1 error message2  )", $message);

        $errors = [
            "sample" => [
                "messages" => "error message"
            ]
        ];
        $message = $method->invoke($this->subscriber, $errors);
        $this->assertEquals(" (sample : error message )", $message);
    }
}
