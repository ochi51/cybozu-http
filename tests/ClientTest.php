<?php

namespace CybozuHttp\Tests;

use CybozuHttp\Client;
use CybozuHttp\Exception\NotExistRequiredException;
use CybozuHttp\Exception\RedirectResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
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
        $yml = Yaml::parse(file_get_contents(__DIR__ . '/../parameters.yml'));
        $this->config = $yml['parameters'];
        $this->config['debug'] = true;
        $this->config['logfile'] = __DIR__ . '/_output/connection.log';
    }

    public function testConstruct()
    {
        try {
            new Client([
                'domain' => 'cybozu.com',
                'subdomain' => 'test',
                'login' => 'test@ochi51.com',
                'password' => 'password'
            ]);
        } catch (NotExistRequiredException $e) {
            self::fail('ERROR!! NotExistRequiredException');
        }
        self::assertTrue(true);

        try {
            new Client([
                'domain' => 'cybozu.com',
                'subdomain' => 'test'
            ]);
            self::fail('Not throw NotExistRequiredException.');
        } catch (NotExistRequiredException $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testConnectionTest()
    {
        $config = $this->config;

        $this->successConnection($config, $config['use_basic'], $config['use_client_cert']);

        if ($config['use_basic'] && $config['use_client_cert']) {
            $config['use_basic'] = true;
            $config['use_client_cert'] = false;
            $this->successConnection($config, true, false);
            $this->successConnection($config, false, true);
        }
        $this->errorConnection($config, self::CHANGE_SUB_DOMAIN);
        $this->errorConnection($config, self::CHANGE_LOGIN);
        $this->errorConnection($config, self::CHANGE_PASSWORD);
        if ($config['use_basic']) {
            $this->errorConnection($config, self::CHANGE_BASIC_LOGIN);
            $this->errorConnection($config, self::CHANGE_BASIC_PASSWORD);
        }
        if ($config['use_client_cert']) {
            $this->errorConnection($config, self::CHANGE_CERT_FILE);
            $this->errorConnection($config, self::CHANGE_CERT_PASSWORD);
        }
    }

    /**
     * @param array $config
     * @param bool $useBasic
     * @param bool $useCert
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function successConnection(array $config, $useBasic = true, $useCert = true)
    {
        $config['use_basic'] = $useBasic;
        $config['use_client_cert'] = $useCert;
        $client = new Client($config);
        try {
            $client->connectionTest();
        } catch (RequestException $e) {
            file_put_contents(
                __DIR__ . '/_output/connectionTestError' . (int)$useBasic . (int)$useCert . '.html',
                $e->getResponse()->getBody()
            );
            self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
        } catch (\Exception $e) {
            self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
        }
        self::assertTrue(true);
    }

    /**
     * @param array $config
     * @param integer $pattern
     * @throws \GuzzleHttp\Exception\GuzzleException
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
                $config['basic_login'] = 'change_me';
                break;
            case self::CHANGE_BASIC_PASSWORD:
                $config['basic_password'] = 'change_me';
                break;
            case self::CHANGE_CERT_FILE:
                $config['cert_file'] = 'change_me';
                break;
            case self::CHANGE_CERT_PASSWORD:
                $config['cert_password'] = 'change_me';
                break;
        }
        $client = new Client($config);
        try {
            $client->connectionTest();
        } catch (ClientException $e) {
            switch ($pattern) {
                case self::CHANGE_BASIC_LOGIN:
                case self::CHANGE_BASIC_PASSWORD:
                    self::assertTrue(true);
                    break;
                default:
                    file_put_contents(
                        __DIR__ . '/_output/connectionTestError.html',
                        (string)$e->getResponse()->getBody()
                    );
                    self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
                    break;
            }
        } catch (ServerException $e) {
            switch ($pattern) {
                case self::CHANGE_LOGIN:
                case self::CHANGE_PASSWORD:
                    self::assertTrue(true);
                    break;
                default:
                    file_put_contents(
                        __DIR__ . '/_output/connectionTestError.html',
                        (string)$e->getResponse()->getBody()
                    );
                    self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
                    break;
            }
        } catch (RedirectResponseException $e) {
            if ($pattern === self::CHANGE_SUB_DOMAIN) {
                self::assertTrue(true);
            } else {
                file_put_contents(
                    __DIR__.'/_output/connectionTestError.html',
                    (string)$e->getResponse()->getBody()
                );
                self::fail('ERROR!! '.get_class($e).' : '.$e->getMessage());
            }
        } catch (\Exception $e) {
            self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
        }
    }
}