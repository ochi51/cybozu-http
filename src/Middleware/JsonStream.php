<?php

namespace CybozuHttp\Middleware;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class JsonStream implements StreamInterface, \JsonSerializable
{
    use StreamDecoratorTrait;

    public function jsonSerialize()
    {
        $contents = (string) $this->getContents();
        if ($contents === '') {
            return null;
        }
        $decodedContents = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Error trying to decode response: ' .
                json_last_error_msg()
            );
        }

        return $decodedContents;
    }
}