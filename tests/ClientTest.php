<?php

namespace CybozuHttp\Tests;

use CybozuHttp\Client;
use CybozuHttp\Exception\FailedAuthException;
use CybozuHttp\Exception\NotExistRequiredException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    const NO_CHANGE = 0;
    const CHANGE_SUB_DOMAIN = 1;
    const CHANGE_LOGIN = 2;
    const CHANGE_PASSWORD = 3;
    const CHANGE_BASIC_LOGIN = 4;
    const CHANGE_BASIC_PASSWORD = 5;
    const CHANGE_CERT_FILE = 6;
    const CHANGE_CERT_PASSWORD = 7;

    /**
     * @var array
     */
    private $config;

    protected function setup()
    {
        $yml = Yaml::parse(__DIR__ . '/../parameters.yml');
        $this->config = $yml['parameters'];
        $this->config['debug'] = true;
        $this->config['logfile'] = __DIR__ . '/_output/connection.log';
    }

    public function testGetConfig()
    {
        try {
            $client = Client::factory([
                'domain' => 'cybozu.com',
                'subdomain' => 'test',
                'login' => 'test@ochi51.com',
                'password' => 'password'
            ]);
            $config = $client->getConfig();
            $this->assertEquals($config->get('domain'), 'cybozu.com');
            $this->assertEquals($config->get('subdomain'), 'test');
            $this->assertEquals($config->get('login'), 'test@ochi51.com');
            $this->assertEquals($config->get('password'), 'password');
        } catch (NotExistRequiredException $e) {
            $this->fail("ERROR!! NotExistRequiredException");
        }
    }

    public function testFactory()
    {
        try {
            Client::factory([
                'domain' => 'cybozu.com',
                'subdomain' => 'test',
                'login' => 'test@ochi51.com',
                'password' => 'password'
            ]);
        } catch (NotExistRequiredException $e) {
            $this->fail("ERROR!! NotExistRequiredException");
        }
        $this->assertTrue(true);

        try {
            Client::factory([
                'domain' => 'cybozu.com',
                'subdomain' => 'test'
            ]);
            $this->fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        }
    }

    public function testConnectionTest()
    {
        $config = $this->config;

        $this->successConnection($config, $config['useBasic'], $config['useClientCert']);

        if ($config['useBasic'] and $config['useClientCert']) {
            $config['useBasic'] = true;
            $config['useClientCert'] = false;
            $this->successConnection($config, true, false);
            $this->successConnection($config, false, true);
        }
        $this->errorConnection($config, self::CHANGE_SUB_DOMAIN);
        $this->errorConnection($config, self::CHANGE_LOGIN);
        $this->errorConnection($config, self::CHANGE_PASSWORD);
        if ($config['useBasic']) {
            $this->errorConnection($config, self::CHANGE_BASIC_LOGIN);
            $this->errorConnection($config, self::CHANGE_BASIC_PASSWORD);
        }
        if ($config['useClientCert']) {
            $this->errorConnection($config, self::CHANGE_CERT_FILE);
            $this->errorConnection($config, self::CHANGE_CERT_PASSWORD);
        }
    }

    /**
     * @param array $config
     * @param bool $useBasic
     * @param bool $useCert
     * @throws NotExistRequiredException
     */
    private function successConnection(array $config, $useBasic = true, $useCert = true)
    {
        $config['useBasic'] = $useBasic;
        $config['useClientCert'] = $useCert;
        $client = Client::factory($config);
        try {
            $client->connectionTest();
        } catch (FailedAuthException $e) {
            $this->fail("ERROR!! FailedAuthException : " . $e->getMessage());
        } catch (BadResponseException $e) {
            file_put_contents(
                __DIR__ . '/_output/connectionTestError' . (int)$useBasic . (int)$useCert . '.html',
                $e->getResponse()->getBody()
            );
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }
        $this->assertTrue(true);
    }

    /**
     * @param array $config
     * @param integer $pattern
     * @throws NotExistRequiredException
     */
    private function errorConnection(array $config, $pattern = self::NO_CHANGE)
    {
        switch ($pattern) {
            case self::NO_CHANGE:
                break;
            case self::CHANGE_SUB_DOMAIN:
                $config['subdomain'] = 'change_me';
                break;
            case self::CHANGE_LOGIN:
                $config['login'] = 'change_me';
                break;
            case self::CHANGE_PASSWORD:
                $config['password'] = 'change_me';
                break;
            case self::CHANGE_BASIC_LOGIN:
                $config['basicLogin'] = 'change_me';
                break;
            case self::CHANGE_BASIC_PASSWORD:
                $config['basicPassword'] = 'change_me';
                break;
            case self::CHANGE_CERT_FILE:
                $config['certFile'] = 'change_me';
                break;
            case self::CHANGE_CERT_PASSWORD:
                $config['certPassword'] = 'change_me';
                break;
        }
        $client = Client::factory($config);
        try {
            $client->connectionTest();
        } catch (NotExistRequiredException $e) {
            $this->assertTrue(true);
        } catch (FailedAuthException $e) {
            switch ($pattern) {
                case self::CHANGE_SUB_DOMAIN:
                    $this->assertTrue(true);
                    break;
                default:
                    $this->fail("ERROR!! FailedAuthException : " . $e->getMessage());
                    break;
            }
        } catch (BadResponseException $e) {
            switch ($pattern) {
                case self::CHANGE_BASIC_LOGIN:
                case self::CHANGE_BASIC_PASSWORD:
                    $this->assertTrue(true);
                    break;
                default:
                    file_put_contents(
                        __DIR__ . '/_output/connectionTestError.html',
                        $e->getResponse()->getBody()->getContents()
                    );
                    $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
                    break;
            }
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }
    }
}