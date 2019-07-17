<?php

namespace CybozuHttp\Middleware;

use CybozuHttp\Service\ResponseService;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class FinishMiddleware
{

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return \Closure
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {

            return $handler($request, $options)->then(
                $this->onFulfilled($request),
                $this->onRejected($request)
            );
        };
    }

    /**
     * @param RequestInterface $request
     * @return \Closure
     * @throws \InvalidArgumentException
     */
    private function onFulfilled(RequestInterface $request): callable
    {
        return static function (ResponseInterface $response) use ($request) {
            $service = new ResponseService($request, $response);
            if ($service->isJsonResponse()) {
                return $response->withBody(new JsonStream($response->getBody()));
            }
            return $response;
        };
    }

    /**
     * @param RequestInterface $request
     * @return \Closure
     * @throws RequestException
     */
    private function onRejected(RequestInterface $request): callable
    {
        return static function ($reason) use ($request) {
            if (!($reason instanceof RequestException)) {
                return $reason;
            }
            $response = $reason->getResponse();
            if ($response === null || $response->getStatusCode() < 300) {
                return $reason;
            }
            $service = new ResponseService($request, $response);
            if ($service->isJsonResponse()) {
                $service->handleJsonError();
            } else {
                $service->handleDomError();
            }

            return $reason;
        };
    }
}
