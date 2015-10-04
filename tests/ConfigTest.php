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
            'use_api_token' => false,
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'use_basic' => true,
            'basic_login' => 'basic',
            'basic_password' => 'password',
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password'
        ]);

        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('configureDefaults');
        $method->setAccessible(true);
        $method->invoke($config);
        $this->assertArrayHasKey('X-Cybozu-Authorization', $config->get('defaults')['headers']);
        $this->assertEquals($config->get('defaults')['auth'], ['basic', 'password']);
        $this->assertTrue($config->get('defaults')['verify']);
        $this->assertEquals($config->get('defaults')['cert'], ['/path/to/cert','password']);
    }

    public function testConfigureAuth()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'use_api_token' => false,
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('configureAuth');
        $method->setAccessible(true);
        $method->invoke($config);
        $this->assertArrayHasKey('X-Cybozu-Authorization', $config->get('defaults')['headers']);
        $this->assertArrayNotHasKey('X-Cybozu-API-Token', $config->get('defaults')['headers']);

        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'use_api_token' => true,
            'token' => 'test_token'
        ]);
        $reflection = new \ReflectionClass($config);
        $method = $reflection->getMethod('configureAuth');
        $method->setAccessible(true);
        $method->invoke($config);
        $this->assertArrayNotHasKey('X-Cybozu-Authorization', $config->get('defaults')['headers']);
        $this->assertArrayHasKey('X-Cybozu-API-Token', $config->get('defaults')['headers']);
    }

    public function testGetBasicAuthOptions()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'use_basic' => true,
            'basic_login' => 'basic',
            'basic_password' => 'password'
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
                'use_basic' => true,
                'basic_login' => 'basic'
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
            'use_basic' => false,
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password'
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
                'use_basic' => false,
                'use_client_cert' => true,
                'cert_file' => '/path/to/cert'
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
            'use_basic' => false,
            'use_client_cert' => false,
            'debug' => false
        ]);

        $array = $config->toArray();
        $this->assertEquals('cybozu.com', $array['domain']);
        $this->assertEquals('test', $array['subdomain']);
        $this->assertEquals('test@ochi51.com', $array['login']);
        $this->assertEquals('password', $array['password']);
        $this->assertEquals(false, $array['use_basic']);
        $this->assertEquals(false, $array['use_client_cert']);
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
            'use_api_token' => true,
            'token' => 'test_token'
        ]);
        $this->assertTrue($config->hasRequired());

        $config = new Config([
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        $this->assertFalse($config->hasRequired());

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

        $config = new Config([
            'subdomain' => 'test',
            'use_api_token' => true
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
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password'
        ]))->getBaseUrl());
    }
}
