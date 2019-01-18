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

    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * @return UserApi
     */
    public static function createUserApi(): UserApi
    {
        $yml = Yaml::parse(file_get_contents(__DIR__ . '/../../parameters.yml'));
        $config = $yml['parameters'];
        $config['debug'] = true;
        $config['logfile'] = __DIR__ . '/../_output/connection.log';
        if ($config['use_basic'] && $config['use_client_cert']) {
            $config['use_client_cert'] = false;
        }
        self::$config = $config;

        self::$api = new UserApi(new Client($config));

        return self::$api;
    }

    /**
     * @return UserApi
     */
    public static function getUserApi(): UserApi
    {
        if (self::$api) {
            return self::$api;
        }

        return self::createUserApi();
    }

}
