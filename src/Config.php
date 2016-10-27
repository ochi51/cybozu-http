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
    private $config = [];

    /**
     * @var array $default
     */
    private $default = [
        'domain' => "cybozu.com",
        'use_api_token' => false,
        'use_basic' => false,
        'use_client_cert' => false,
        'base_uri' => null,
        'debug' => false
    ];

    /**
     * @var array $required
     */
    private $required = [
        'handler',
        'domain',
        'subdomain',
        'use_api_token',
        'use_basic',
        'use_client_cert',
        'base_uri',
        'debug'
    ];

    public function __construct(array $config)
    {
        $this->config = $config + $this->default;

        $this->config['base_uri'] = $this->getBaseUri();
        $this->config['handler'] = $handler =  HandlerStack::create();

        $this->configureAuth();
        $this->configureBasicAuth();
        $this->configureCert();

        $handler->before('http_errors', new FinishMiddleware(), 'cybozu_http.finish');
    }

    private function configureAuth()
    {
        if ($this->get('use_api_token')) {
            $this->config['headers']['X-Cybozu-API-Token'] = $this->get('token');
        } else {
            $this->config['headers']['X-Cybozu-Authorization'] =
                base64_encode($this->get('login') . ':' . $this->get('password'));
        }
    }

    private function configureBasicAuth()
    {
        if ($this->get('use_basic')) {
            $this->config['auth'] = $this->getBasicAuthOptions();
        }
    }

    private function configureCert()
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
     */
    private function getBasicAuthOptions()
    {
        if ($this->hasRequiredOnBasicAuth()) {
            return [
                $this->get('basic_login'),
                $this->get('basic_password')
            ];
        }
        throw new NotExistRequiredException("kintone.empty_basic_password");
    }

    /**
     * @return array
     */
    private function getCertOptions()
    {
        if ($this->hasRequiredOnCert()) {
            return [
                $this->get('cert_file'),
                $this->get('cert_password')
            ];
        }
        throw new NotExistRequiredException("kintone.empty_cert");
    }

    /**
     * @return array
     */
    public function toGuzzleConfig()
    {
        $config = [
            'handler' => $this->get('handler'),
            'base_uri' => $this->get('base_uri'),
            'headers' => $this->get('headers'),
            'debug' => $this->get('debug') ? fopen($this->get('logfile'), 'a') : false
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
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function hasRequired()
    {
        foreach ($this->required as $r) {
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
    private function hasRequiredOnAuth()
    {
        if ($this->get('use_api_token')) {
            return !empty($this->get('token'));
        }

        return $this->get('login') && $this->get('password');
    }

    /**
     * @return bool
     */
    private function hasRequiredOnBasicAuth()
    {
        return $this->hasKeysByUse('use_basic', ['basic_login', 'basic_password']);
    }

    /**
     * @return bool
     */
    private function hasRequiredOnCert()
    {
        return $this->hasKeysByUse('use_client_cert', ['cert_file', 'cert_password']);
    }

    /**
     * @param string $use
     * @param string[] $keys
     * @return bool
     */
    private function hasKeysByUse($use, array $keys)
    {
        if (!$this->get($use)) {
            return true;
        }

        foreach ($keys as $key) {
            if (is_null($this->get($key)) || $this->get($key) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        $subdomain = $this->get('subdomain');
        $uri = "https://" . $subdomain;

        if (strpos($subdomain, '.') === false) {
            if ($this->get('use_client_cert')) {
                $uri .= ".s";
            }

            $uri .= "." . $this->get('domain');
        }

        return $uri;
    }
}