<?php

namespace CybozuHttp\Tests;

use CybozuHttp\Config;
use CybozuHttp\Exception\NotExistRequiredException;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

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
        self::assertArrayHasKey('X-Cybozu-Authorization', $config->get('headers'));
        self::assertArrayNotHasKey('X-Cybozu-API-Token', $config->get('headers'));

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
        self::assertArrayNotHasKey('X-Cybozu-Authorization', $config->get('headers'));
        self::assertArrayHasKey('X-Cybozu-API-Token', $config->get('headers'));
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
        self::assertEquals($res, ['basic', 'password']);

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
            self::assertTrue(true);
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
        self::assertEquals($res, ['/path/to/cert', 'password']);

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
            self::assertTrue(true);
        }
    }

    public function testToGuzzleConfig()
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
        self::assertTrue((bool)$array['handler']);
        self::assertEquals('https://test.s.cybozu.com', $array['base_uri']);
        self::assertEquals([
            'X-Cybozu-Authorization' => base64_encode("test@ochi51.com:password")
        ], $array['headers']);
        self::assertEquals(['basic', 'password'], $array['auth']);
        self::assertTrue($array['verify']);
        self::assertEquals(['/path/to/cert', 'password'], $array['cert']);
        self::assertFalse($array['debug']);
    }

    public function testGet()
    {
        $config = new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);

        self::assertEquals('cybozu.com', $config->get('domain'));
        self::assertEquals('test', $config->get('subdomain'));
        self::assertEquals('test@ochi51.com', $config->get('login'));
        self::assertEquals('password', $config->get('password'));
        self::assertFalse($config->get('not_exist_parameter'));
    }

    public function testGetConfig()
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

        self::assertEquals('cybozu.com', $c['domain']);
        self::assertEquals('test', $c['subdomain']);
        self::assertEquals('test@ochi51.com', $c['login']);
        self::assertTrue($c['use_basic']);
        self::assertEquals('basic', $c['basic_login']);
        self::assertEquals('password', $c['basic_password']);
        self::assertTrue($c['use_client_cert']);
        self::assertEquals('/path/to/cert', $c['cert_file']);
        self::assertEquals('password', $c['cert_password']);
        self::assertFalse($c['debug']);
        self::assertArrayHasKey('handler', $c);
        self::assertArrayHasKey('base_uri', $c);
    }

    public function testHasRequired()
    {
        $config = new Config([
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        self::assertTrue($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'use_api_token' => true,
            'token' => 'test_token'
        ]);
        self::assertTrue($config->hasRequired());

        $config = new Config([
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]);
        self::assertFalse($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'login' => 'test@ochi51.com'
        ]);
        self::assertFalse($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'password' => 'password'
        ]);
        self::assertFalse($config->hasRequired());

        $config = new Config([
            'subdomain' => 'test',
            'use_api_token' => true
        ]);
        self::assertFalse($config->hasRequired());
    }

    public function testGetBaseUrl()
    {
        self::assertEquals("https://test.cybozu.com", (new Config([
            'domain' => 'cybozu.com',
            'subdomain' => 'test',
            'login' => 'test@ochi51.com',
            'password' => 'password'
        ]))->getBaseUri());

        self::assertEquals("https://test.s.cybozu.com", (new Config([
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
