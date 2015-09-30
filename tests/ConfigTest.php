<?php

namespace CybozuHttp\Tests;

use CybozuHttp\Config;
use CybozuHttp\Exception\NotExistRequiredException;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigureDefaults()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);

        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('configureDefaults');
        $method->setAccessible(true);
        $method->invoke($config);
        $this->assertArrayHasKey('X-Cybozu-Authorization', $config->get('defaults')['headers']);
        $this->assertFalse($config->get('defaults')['verify']);
    }

    public function testGetBasicAuthOptions()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'useBasic' => true,
            'basicLogin' => 'basic',
            'basicPassword' => 'password'
        ]);

        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('getBasicAuthOptions');
        $method->setAccessible(true);
        $res = $method->invoke($config);
        $this->assertEquals($res, ['basic', 'password']);

        try {
            new Config([
                'domain' => 'cybozu.com',
                'subdomain' => 'test',
                'login' => 'test@ochi51.com',
                'password' => 'password',
                'useBasic' => true,
                'basicLogin' => 'basic'
            ]);
            $this->fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        }
    }

    public function testCertOptions()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'useBasic' => false,
            'useClientCert' => true,
            'certFile' => '/path/to/cert',
            'certPassword' => 'password'
        ]);

        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('getCertOptions');
        $method->setAccessible(true);
        $res = $method->invoke($config);
        $this->assertEquals($res, ['/path/to/cert', 'password']);

        try {
            new Config([
                'domain' => 'cybozu.com',
                'subdomain' => 'test',
                'login' => 'test@ochi51.com',
                'password' => 'password',
                'useBasic' => false,
                'useClientCert' => true,
                'certFile' => '/path/to/cert'
            ]);
            $this->fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        }
    }

    public function testToArray()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'useBasic' => false,
            'useClientCert' => false,
            'debug' => false
        ]);

        $array = $config->toArray();
        $this->assertEquals('cybozu.com', $array['domain']);
        $this->assertEquals('test', $array['subdomain']);
        $this->assertEquals('test@ochi51.com', $array['login']);
        $this->assertEquals('password', $array['password']);
        $this->assertEquals(false, $array['useBasic']);
        $this->assertEquals(false, $array['useClientCert']);
        $this->assertEquals(false, $array['debug']);
    }

    public function testGet()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);

        $this->assertEquals('cybozu.com', $config->get('domain'));
        $this->assertEquals('test', $config->get('subdomain'));
        $this->assertEquals('test@ochi51.com', $config->get('login'));
        $this->assertEquals('password', $config->get('password'));
        $this->assertFalse($config->get('not_exist_parameter'));
    }

    public function testHasRequired()
    {
        $config = new Config([
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        $this->assertTrue($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'login' => 'test@ochi51.com'
        ]);
        $this->assertFalse($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'password' => 'password'
        ]);
        $this->assertFalse($config->hasRequired());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals("https://test.cybozu.com", (new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]))->getBaseUrl());

        $this->assertEquals("https://test.s.cybozu.com", (new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'useClientCert' => true,
            'certFile' => '/path/to/cert',
            'certPassword' => 'password'
        ]))->getBaseUrl());
    }
}
