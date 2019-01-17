<?php

namespace CybozuHttp\Tests\Api\User;

require_once __DIR__ . '/../../_support/UserTestHelper.php';
use PHPUnit\Framework\TestCase;
use UserTestHelper;

use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class OrganizationUsersTest extends TestCase
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
