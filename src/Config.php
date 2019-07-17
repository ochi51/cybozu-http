<?php

namespace CybozuHttp;

use CybozuHttp\Exception\NotExistRequiredException;
use CybozuHttp\Middleware\FinishMiddleware;
use GuzzleHttp\HandlerStack;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Config
{

    /**
     * @var array $config
     */
    private $config;

    /**
     * @var array $default
     */
    private static $default = [
        'domain' => 'cybozu.com',
        'use_api_token' => false,
        'use_basic' => false,
        'use_client_cert' => false,
        'base_uri' => null,
        'concurrency' => 1,
        'response_middleware' => true,
        'debug' => false
    ];

    /**
     * @var array $required
     */
    private static $required = [
        'handler',
        'domain',
        'subdomain',
        'use_api_token',
        'use_basic',
        'use_client_cert',
        'base_uri',
        'debug'
    ];

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge(self::$default, $config);

        $this->config['base_uri'] = $this->getBaseUri();
        $this->config['handler'] = $handler = $config['handler'] ?? HandlerStack::create();

        $this->configureAuth();
        $this->configureBasicAuth();
        $this->configureCert();

        if ($this->config['response_middleware']) {
            $handler->before('http_errors', new FinishMiddleware(), 'cybozu_http.finish');
        }
    }


    private function configureAuth(): void
    {
        if ($this->get('use_api_token')) {
            $this->config['headers']['X-Cybozu-API-Token'] = $this->get('token');
        } else {
            $this->config['headers']['X-Cybozu-Authorization'] =
                base64_encode($this->get('login') . ':' . $this->get('password'));
        }
    }

    private function configureBasicAuth(): void
    {
        if ($this->get('use_basic')) {
            $this->config['auth'] = $this->getBasicAuthOptions();
        }
    }

    private function configureCert(): void
    {
        if ($this->get('use_client_cert')) {
            $this->config['verify'] = true;
            $this->config['cert'] = $this->getCertOptions();
        } else {
            $this->config['verify'] = false;
        }
    }

    /**
     * @return array
     * @throws NotExistRequiredException
     */
    private function getBasicAuthOptions(): array
    {
        if ($this->hasRequiredOnBasicAuth()) {
            return [
                $this->get('basic_login'),
                $this->get('basic_password')
            ];
        }
        throw new NotExistRequiredException('kintone.empty_basic_password');
    }

    /**
     * @return array
     * @throws NotExistRequiredException
     */
    private function getCertOptions(): array
    {
        if ($this->hasRequiredOnCert()) {
            return [
                $this->get('cert_file'),
                $this->get('cert_password')
            ];
        }
        throw new NotExistRequiredException('kintone.empty_cert');
    }

    /**
     * @return array
     */
    public function toGuzzleConfig(): array
    {
        $config = [
            'handler' => $this->get('handler'),
            'base_uri' => $this->get('base_uri'),
            'headers' => $this->get('headers'),
            'debug' => $this->get('debug') ? fopen($this->get('logfile'), 'ab') : false,
            'concurrency' => $this->get('concurrency')
        ];
        if ($this->get('auth')) {
            $config['auth'] = $this->get('auth');
        }
        $config['verify'] = $this->get('verify');
        if ($this->get('cert')) {
            $config['cert'] = $this->get('cert');
        }

        return $config;
    }

    /**
     * @param $key
     * @return string|bool
     */
    public function get($key)
    {
        return $this->config[$key] ?? false;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function hasRequired(): bool
    {
        foreach (self::$required as $r) {
            if (!array_key_exists($r, $this->config)) {
                return false;
            }
        }

        return $this->hasRequiredOnAuth()
                && $this->hasRequiredOnBasicAuth()
                && $this->hasRequiredOnCert();
    }

    /**
     * @return bool
     */
    private function hasRequiredOnAuth(): bool
    {
        if ($this->get('use_api_token')) {
            return !empty($this->get('token'));
        }

        return $this->get('login') && $this->get('password');
    }

    /**
     * @return bool
     */
    private function hasRequiredOnBasicAuth(): bool
    {
        return $this->hasKeysByUse('use_basic', ['basic_login', 'basic_password']);
    }

    /**
     * @return bool
     */
    private function hasRequiredOnCert(): bool
    {
        return $this->hasKeysByUse('use_client_cert', ['cert_file', 'cert_password']);
    }

    /**
     * @param string $use
     * @param string[] $keys
     * @return bool
     */
    private function hasKeysByUse($use, array $keys): bool
    {
        if (!$this->get($use)) {
            return true;
        }

        foreach ($keys as $key) {
            if (!$this->get($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        $subdomain = $this->get('subdomain');
        $uri = 'https://'. $subdomain;

        if (strpos($subdomain, '.') === false) {
            if ($this->get('use_client_cert')) {
                $uri .= '.s';
            }

            $uri .= '.'. $this->get('domain');
        }

        return $uri;
    }
}
