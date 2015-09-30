<?php

namespace CybozuHttp\tests\Api\User;

require_once __DIR__ . '/../../_support/UserTestHelper.php';
use UserTestHelper;

use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class OrganizationUsersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserApi
     */
    private $api;

    protected function setup()
    {
        $this->api = UserTestHelper::getUserApi();
    }

    public function testGet()
    {
        $config = UserTestHelper::getConfig();
        $this->api->organizationUsers()->get($config['login']);
        $this->assertTrue(true);
    }
}
