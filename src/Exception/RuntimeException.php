<?php

namespace CybozuHttp\Exception;

use Throwable;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var array
     */
    private $context;

    /**
     * RuntimeException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, array $context = [])
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
