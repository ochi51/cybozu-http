<?php

namespace CybozuHttp;

use CybozuHttp\Exception\RedirectResponseException;
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
     * @throws NotExistRequiredException
     */
    public function __construct(array $config = [])
    {
        $cybozuConfig = new Config($config);
        if (!$cybozuConfig->hasRequired()) {
            throw new NotExistRequiredException('Parameters is invalid.');
        }

        parent::__construct($cybozuConfig->toGuzzleConfig());
    }

    /**
     * @param string $prefix
     * @throws RedirectResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function connectionTest($prefix = '/'): void
    {
        $response = $this->request('GET', $prefix, ['allow_redirects' => false]);
        if ($response->getStatusCode() === 302) {
            throw new RedirectResponseException('', $response);
        }
    }
}
