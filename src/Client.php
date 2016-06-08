<?php

namespace CybozuHttp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use CybozuHttp\Subscriber\ErrorSubscriber;
use CybozuHttp\Exception\FailedAuthException;
use CybozuHttp\Exception\NotExistRequiredException;

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
     * Client constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config = new Config($config);
        if (!$config->hasRequired()) {
            throw new NotExistRequiredException();
        }

        parent::__construct($config->toArray());

        $this->config = $config;

        $this->attachErrorSubscriber($config);
        $this->attachLogSubscriber($config);
    }

    /**
     * @param Config $config
     */
    protected function attachErrorSubscriber(Config $config)
    {
        $this->getEmitter()->attach(new ErrorSubscriber($config));
    }

    /**
     * @param Config $config
     */
    protected function attachLogSubscriber(Config $config)
    {
        if ($config->get('debug') && $config->get('logfile')) {
            $logSubscriber = new LogSubscriber(fopen($config->get('logfile'), 'a'), Formatter::DEBUG);
            $this->getEmitter()->attach($logSubscriber);
        }
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
        $baseConfig = $this->config->toArray();
        unset($baseConfig['defaults']);
        $mergeConfig = $config + $baseConfig;

        $config = (new Config($mergeConfig));
        $options = $config->toArray()['defaults'];
        foreach ($options as $key => $option) {
            $this->setDefaultOption($key, $option);
        }

        $this->attachLogSubscriber($config);

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
