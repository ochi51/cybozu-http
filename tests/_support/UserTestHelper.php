<?php

use Symfony\Component\Yaml\Yaml;
use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserTestHelper
{

    /**
     * @var array
     */
    private static $config;

    /**
     * @var UserApi
     */
    private static $api;

    public static function getConfig()
    {
        return self::$config;
    }

    public static function createUserApi()
    {
        $yml = Yaml::parse(file_get_contents(__DIR__ . '/../../parameters.yml'));
        $config = $yml['parameters'];
        $config['debug'] = true;
        $config['logfile'] = __DIR__ . '/../_output/connection.log';
        if ($config['use_basic'] and $config['use_client_cert']) {
            $config['use_client_cert'] = false;
        }
        self::$config = $config;

        self::$api = new UserApi(new Client($config));

        return self::$api;
    }

    public static function getUserApi()
    {
        if (self::$api) {
            return self::$api;
        }

        return self::createUserApi();
    }

}