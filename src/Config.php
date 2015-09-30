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
        'useBasic' => false,
        'useClientCert' => false,
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
        'login',
        'password',
        'useBasic',
        'useClientCert',
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
        $this->config['defaults'] = [
            'headers' => [
                'X-Cybozu-Authorization' => base64_encode($this->get('login') . ':' . $this->get('password'))
            ]
        ];

        if ($this->get('useBasic')) {
            $this->config['defaults']['auth'] = $this->getBasicAuthOptions();
        }

        if ($this->get('useClientCert')) {
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
        if ($this->hasRequired()) {
            return [
                $this->get('basicLogin'),
                $this->get('basicPassword')
            ];
        }
        throw new NotExistRequiredException("kintone.empty_basic_password");
    }

    /**
     * @return array
     */
    private function getCertOptions()
    {
        if ($this->hasRequired()) {
            return [
                $this->get('certFile'),
                $this->get('certPassword')
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
        if (!$this->hasKeysByUse('useBasic', ['basicLogin', 'basicPassword'])) {
            return false;
        }
        if (!$this->hasKeysByUse('useClientCert', ['certFile', 'certPassword'])) {
            return false;
        }

        return true;
    }

    /**
     * @param bool $use
     * @param array $keys
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

    public function getBaseUrl()
    {
        $subdomain = $this->config['subdomain'];
        $uri = "https://" . $subdomain;

        if (strpos($subdomain, '.') === false) {
            if ($this->config['useClientCert']) {
                $uri .= ".s";
            }

            $uri .= "." . $this->config['domain'];
        }

        return $uri;
    }
}