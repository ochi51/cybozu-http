<?php

namespace CybozuHttp\Subscriber;

use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\ResponseInterface;
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
        if (!$response->getStatusCode() || !$response->getBody()) {
            return;
        }

        $this->authError($response);
        $this->jsonError($response);
    }

    private function authError(ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new FailedAuthException('Invalid auth');
        }
    }

    private function jsonError(ResponseInterface $response)
    {
        try {
            $json = $response->json();
        } catch (\RuntimeException $e) {
            // Uncaught response
            return false;
        }

        $message = $json['message'];
        if (isset($json['errors']) && is_array($json['errors'])) {
            $message .= $this->addErrorMessages($json['errors']);
        }
        throw new JsonResponseException($message);
    }

    /**
     * @param array $errors
     * @return string
     */
    private function addErrorMessages(array $errors)
    {
        $message = ' (';
        foreach ($errors as $k => $err) {
            $message .= $k . ' : ';
            if (is_array($err['messages'])) {
                foreach ($err['messages'] as $m) {
                    $message .= $m . ' ';
                }
            } else {
                $message .= $err['messages'];
            }
        }
        $message .= ' )';

        return $message;
    }
}