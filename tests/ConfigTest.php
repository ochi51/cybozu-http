<?php

namespace CybozuHttp\Tests;

use PHPUnit\Framework\TestCase;
use CybozuHttp\Config;
use CybozuHttp\Exception\NotExistRequiredException;

class ConfigTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testConfigureAuth(): void
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
        $this->assertArrayHasKey('X-Cybozu-Authorization', (array)$config->get('headers'));
        $this->assertArrayNotHasKey('X-Cybozu-API-Token', (array)$config->get('headers'));

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
        $this->assertArrayNotHasKey('X-Cybozu-Authorization', (array)$config->get('headers'));
        $this->assertArrayHasKey('X-Cybozu-API-Token', (array)$config->get('headers'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetBasicAuthOptions(): void
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
            self::fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testCertOptions(): void
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
            self::fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        }
    }

    public function testToGuzzleConfig(): void
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'use_basic' => true,
            'basic_login' => 'basic',
            'basic_password' => 'password',
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password',
            'debug' => false
        ]);

        $array = $config->toGuzzleConfig();
        $this->assertTrue((bool)$array['handler']);
        $this->assertEquals('https://test.s.cybozu.com', $array['base_uri']);
        $this->assertEquals([
            'X-Cybozu-Authorization' => base64_encode('test@ochi51.com:password')
        ], $array['headers']);
        $this->assertEquals(['basic', 'password'], $array['auth']);
        $this->assertTrue($array['verify']);
        $this->assertEquals(['/path/to/cert', 'password'], $array['cert']);
        $this->assertFalse($array['debug']);
    }

    public function testGet(): void
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

    public function testGetConfig(): void
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'use_basic' => true,
            'basic_login' => 'basic',
            'basic_password' => 'password',
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password',
            'debug' => false
        ]);

        $c = $config->getConfig();

        $this->assertEquals('cybozu.com', $c['domain']);
        $this->assertEquals('test', $c['subdomain']);
        $this->assertEquals('test@ochi51.com', $c['login']);
        $this->assertTrue($c['use_basic']);
        $this->assertEquals('basic', $c['basic_login']);
        $this->assertEquals('password', $c['basic_password']);
        $this->assertTrue($c['use_client_cert']);
        $this->assertEquals('/path/to/cert', $c['cert_file']);
        $this->assertEquals('password', $c['cert_password']);
        $this->assertFalse($c['debug']);
        $this->assertArrayHasKey('handler', $c);
        $this->assertArrayHasKey('base_uri', $c);
    }

    public function testHasRequired(): void
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

    public function testGetBaseUrl(): void
    {
        $this->assertEquals('https://test.cybozu.com', (new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]))->getBaseUri());

        $this->assertEquals('https://test.s.cybozu.com', (new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password',
            'use_client_cert' => true,
            'cert_file' => '/path/to/cert',
            'cert_password' => 'password'
        ]))->getBaseUri());
    }
}
