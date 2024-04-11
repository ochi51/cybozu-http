<?php

namespace CybozuHttp\Service;

use CybozuHttp\Exception\RuntimeException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\RequestException;

class ResponseServiceTest extends TestCase
{

    public function testIsJsonResponse(): void
    {
        $request = new Request('GET', '/');
        $response = new Response(200, ['Content-Type' => 'application/json; charset=utf-8']);
        $service = new ResponseService($request, $response);

        $this->assertTrue($service->isJsonResponse());
    }

    public function testIsHtmlResponse(): void
    {
        $request = new Request('GET', '/');

        $htmlResponse = new Response(200, ['Content-Type' => 'text/html; charset=utf-8']);
        $service = new ResponseService($request, $htmlResponse);

        $this->assertTrue($service->isHtmlResponse());

        $jsonResponse = new Response(200, ['Content-Type' => 'application/json; charset=utf-8']);
        $service = new ResponseService($request, $jsonResponse);

        $this->assertFalse($service->isHtmlResponse());
    }

    public function testIsNotJsonResponse(): void
    {
        $request = new Request('GET', '/');
        $response = new Response(200, ['Content-Type' => 'text/html; charset=utf-8']);
        $service = new ResponseService($request, $response);

        $this->assertFalse($service->isJsonResponse());
    }

    public function testHandleError(): void
    {
        $request = new Request('GET', '/');
        $dom = '<title>Bad request</title><div>bad request</div>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'Invalid auth.');
        }

        $dom = '<title>Error</title>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals($e->getMessage(), 'Failed to extract error message from DOM response.');
            $this->assertEquals($e->getPrevious(), $exception);
            $this->assertEquals($e->getContext()['responseBody'], $dom);
        }

        $dom = '<title>Error</title><h3>bad request</h3>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'bad request');
        }

        $dom = '<title>Unauthorized</title>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals($e->getMessage(), 'Failed to extract error message from DOM response.');
            $this->assertEquals($e->getPrevious(), $exception);
            $this->assertEquals($e->getContext()['responseBody'], $dom);
        }

        $dom = '<title>Unauthorized</title><h2>Bad authorized</h2>';
        $response = new Response(400, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'Bad authorized');
        }

        $dom = '<title>Bad server</title><div>bad server</div>';
        $response = new Response(500, ['Content-Type' => 'text/html; charset=utf-8'], $dom);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);

        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ServerException::class, $e);
        }

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
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);
        try {
            $service->handleError();
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
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);
        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientException::class, $e);
            $this->assertEquals($e->getMessage(), 'simple error (error2 : detail error)');
        }

        $body = json_encode([
            'unknown' => 'simple error',
        ]);
        $response = new Response(400, ['Content-Type' => 'application/json; charset=utf-8'], $body);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);
        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals($e->getMessage(), 'Failed to extract error message from JSON response.');
            $this->assertEquals($e->getPrevious(), $exception);
            $this->assertEquals($e->getContext()['responseBody'], $body);
        }

        $body = 'invalid json';
        $response = new Response(400, ['Content-Type' => 'application/json; charset=utf-8'], $body);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);
        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }

        $body = json_encode([
            'message' => 'simple error',
        ]);
        $response = new Response(400, ['Content-Type' => 'text/plain; charset=utf-8'], $body);
        $exception = new RequestException('raw error', $request, $response);
        $service = new ResponseService($request, $response, $exception);
        try {
            $service->handleError();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals($e->getMessage(), 'Failed to extract error message because Content-Type of error response is unexpected.');
            $this->assertEquals($e->getPrevious(), $exception);
            $this->assertEquals($e->getContext()['responseBody'], $body);
        }
    }
}
