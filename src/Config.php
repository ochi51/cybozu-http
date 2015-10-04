<?php

namespace CybozuHttp;

use CybozuHttp\Exception\NotExistRequiredException;


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
        'base_url' => null,
        'defaults' => [],
        'debug' => false
    ];

    /**
     * @var array $required
     */
    private $required = [
        'domain',
        'subdomain',
        'use_api_token',
        'use_basic',
        'use_client_cert',
        'base_url',
        'defaults',
        'debug'
    ];

    public function __construct(array $config)
    {
        $this->config = $config + $this->default;

        $this->config['base_url'] = $this->getBaseUrl();

        $this->configureDefaults();
    }

    /**
     * configure default options
     */
    private function configureDefaults()
    {
        $this->configureAuth();
        $this->configureBasicAuth();
        $this->configureCert();
    }

    private function configureAuth()
    {
        if ($this->get('use_api_token')) {
            $this->config['defaults']['headers']['X-Cybozu-API-Token'] = $this->get('token');
        } else {
            $this->config['defaults']['headers']['X-Cybozu-Authorization'] =
                base64_encode($this->get('login') . ':' . $this->get('password'));
        }
    }

    private function configureBasicAuth()
    {
        if ($this->get('use_basic')) {
            $this->config['defaults']['auth'] = $this->getBasicAuthOptions();
        }
    }

    private function configureCert()
    {
        if ($this->get('use_client_cert')) {
            $this->config['defaults']['verify'] = true;
            $this->config['defaults']['cert'] = $this->getCertOptions();
        } else {
            $this->config['defaults']['verify'] = false;
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
    public function toArray()
    {
        return $this->config;
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
            if (!$this->get($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
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