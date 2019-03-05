<?php

namespace CybozuHttp\Service;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResponseServiceTest extends TestCase
{

    public function testIsJsonResponse(): void
    {
        $request = new Request('GET', '/');
        $response = new Response(200, ['Content-Type' => 'application/json; charset=utf-8']);
        $service = new ResponseService($request, $response);

        $this->assertTrue($service->isJsonResponse());
    }

    public function testIsNotJsonResponse(): void
    {
        $request = new Request('GET', '/');
        $response = new Response(200, ['Content-Type' => 'text/html; charset=utf-8']);
        $service = new ResponseService($request, $response);

        $this->assertFalse($service->isJsonResponse());
    }

    public function testHandleDomError(): void
    {
        $request = new Request('GET', '/');
        $dom = '<title>Bad request</title><div>bad request</div>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $service = new ResponseService($request, $response);

        try {
            $service->handleDomError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'Invalid auth.');
        }

        $dom = '<title>Error</title><h3>bad request</h3>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $service = new ResponseService($request, $response);

        try {
            $service->handleDomError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'bad request');
        }

        $dom = '<title>Unauthorized</title><h2>Bad authorized</h2>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $service = new ResponseService($request, $response);

        try {
            $service->handleDomError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'Bad authorized');
        }

        $dom = '<title>Bad server</title><div>bad server</div>';
        $response = new Response(500, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $service = new ResponseService($request, $response);

        try {
            $service->handleDomError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ServerException::class, $e);
        }
    }

    public function testHandleJsonError(): void
    {
        $request = new Request('GET', '/');
        $body = json_encode([
            'message' => 'simple error',
            'errors' => [
                'error1' => [
                    'messages' => [
                        'detail error1',
                        'detail error2'
                    ]
                ]
            ]
        ]);
        $response = new Response(400, ['Content-Type' => 'application/json; charset=utf-8'], $body);
        $service = new ResponseService($request, $response);
        try {
            $service->handleJsonError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'simple error (error1 : detail error1 detail error2 )');
        }

        $body = json_encode([
            'message' => 'simple error',
            'errors' => [
                'error2' => [
                    'messages' => 'detail error'
                ]
            ]
        ]);
        $response = new Response(400, ['Content-Type' => 'application/json; charset=utf-8'], $body);
        $service = new ResponseService($request, $response);
        try {
            $service->handleJsonError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'simple error (error2 : detail error)');
        }

        /** @var Response|MockObject $response */
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willThrowException(new \RuntimeException(''));
        $service = new ResponseService($request, $response);
        $service->handleJsonError();
        $this->assertTrue(true);

        /** @var Response|MockObject $response */
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willThrowException(new \InvalidArgumentException(''));
        $service = new ResponseService($request, $response);
        $service->handleJsonError();
        $this->assertTrue(true);
    }
}
