<?php

namespace CybozuHttp\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class RedirectResponseException extends \Exception
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * RedirectResponseException constructor.
     * @param string $message
     * @param ResponseInterface $response
     */
    public function __construct($message, ResponseInterface $response)
    {
        $this->response = $response;
        parent::__construct($message);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
