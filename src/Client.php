<?php

namespace CybozuHttp;

use GuzzleHttp\Client as GuzzleClient;
use CybozuHttp\Exception\NotExistRequiredException;

/**
 * @author ochi51<ochiai07@gmail.com>
 */
class Client extends GuzzleClient
{

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config = new Config($config);
        if (!$config->hasRequired()) {
            throw new NotExistRequiredException();
        }

        parent::__construct($config->toGuzzleConfig());
    }

    /**
     * @param string $prefix
     */
    public function connectionTest($prefix = '/')
    {
        $this->request('GET', $prefix, ['allow_redirects' => false]);
    }
}
