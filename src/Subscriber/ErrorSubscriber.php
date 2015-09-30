<?php

namespace CybozuHttp\Subscriber;

use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use CybozuHttp\Config;
use CybozuHttp\Exception\FailedAuthException;
use CybozuHttp\Exception\JsonResponseException;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ErrorSubscriber implements SubscriberInterface
{

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getEvents()
    {
        return [
            'error' => ['onError', RequestEvents::VERIFY_RESPONSE]
        ];
    }

    /**
     * @param ErrorEvent $event
     * @throws FailedAuthException|JsonResponseException
     */
    public function onError(ErrorEvent $event)
    {
        $response = $event->getResponse();
        if (!$response->getStatusCode() or !$response->getBody()) {
            return;
        }

        $body = $response->getBody()->getContents();
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new FailedAuthException('Invalid auth');
        }

        try {
            $json = $response->json();
        } catch (\RuntimeException $e) {
            return;
        }
        $message = $json['message'];
        if (isset($json['errors'])) {
            $message .= ' (';
            foreach ($json['errors'] as $k => $err) {
                if (is_array($err['messages'])) {
                    foreach ($err['messages'] as $m) {
                        $message .= $k . ' : ' . $m . PHP_EOL;
                    }
                } else {
                    $message .= $k . ' : ' . $err['messages'] . PHP_EOL;
                }
            }
            $message .= ' )';
        }
        throw new JsonResponseException($message);
    }
}