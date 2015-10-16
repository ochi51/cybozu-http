<?php

namespace CybozuHttp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Subscriber\Log\Formatter;
use CybozuHttp\Exception\FailedAuthException;
use CybozuHttp\Exception\NotExistRequiredException;
use CybozuHttp\Subscriber\LogSubscriber;
use CybozuHttp\Subscriber\ErrorSubscriber;

/**
 * @author ochi51<ochiai07@gmail.com>
 */
class Client extends GuzzleClient
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param array $config
     * @return Client
     * @throws NotExistRequiredException
     */
    public static function factory($config = [])
    {
        $config = new Config($config);
        if (!$config->hasRequired()) {
            throw new NotExistRequiredException();
        }

        $client = new self($config->toArray());
        $client->config = $config;
        $emitter = $client->getEmitter();
        $emitter->attach(new ErrorSubscriber($config));

        if ($config->get('debug') && $config->get('logfile')) {
            $emitter->attach(new LogSubscriber(fopen($config->get('logfile'), 'a'), Formatter::DEBUG));
        }

        return $client;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function changeAuthOptions(array $config)
    {
        $mergeConfig = $config + $this->config->toArray();
        $config = (new Config($mergeConfig));
        $options = $config->toArray()['defaults'];
        foreach ($options as $key => $option) {
            $this->setDefaultOption($key, $options);
        }
        $this->config = $config;
    }

    /**
     * @param string $prefix
     * @throws FailedAuthException
     */
    public function connectionTest($prefix = '/')
    {
        $response = $this->get($prefix);
        $url = $response->getEffectiveUrl();
        if ($url && strpos($url, $this->getBaseUrl()) !== 0) {
            throw new FailedAuthException('Wronged auth information.');
        }
    }
}
