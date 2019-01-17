<?php

namespace CybozuHttp\Middleware;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class JsonStreamTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $data = ['test' => 'data'];
        $string = json_encode($data);
        $jsonStream = new JsonStream(new Stream(fopen('data://text/plain,'.$string, 'rb')));

        $this->assertEquals($jsonStream->jsonSerialize(), $data);
    }

    public function testJsonSerializeNull(): void
    {
        $string = '';
        $jsonStream = new JsonStream(new Stream(fopen('data://text/plain,'.$string, 'rb')));

        $this->assertNull($jsonStream->jsonSerialize());
    }

    public function testJsonSerializeError(): void
    {
        $string = '{"';
        $jsonStream = new JsonStream(new Stream(fopen('data://text/plain,'.$string, 'rb')));

        try {
            $jsonStream->jsonSerialize();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }
    }
}
