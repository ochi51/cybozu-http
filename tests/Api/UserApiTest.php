<?php

namespace CybozuHttp\Tests\Api;

require_once __DIR__ . '/../_support/UserTestHelper.php';
use UserTestHelper;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;
use CybozuHttp\Api\User\Csv;
use CybozuHttp\Api\User\Groups;
use CybozuHttp\Api\User\Organizations;
use CybozuHttp\Api\User\OrganizationUsers;
use CybozuHttp\Api\User\Titles;
use CybozuHttp\Api\User\UserGroups;
use CybozuHttp\Api\User\UserOrganizations;
use CybozuHttp\Api\User\Users;
use CybozuHttp\Api\User\UserServices;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserApi
     */
    private $api;

    protected function setup()
    {
        $this->api = UserTestHelper::getUserApi();
    }

    public function testGenerateUrl()
    {
        self::assertEquals('/v1/users.json', UserApi::generateUrl('users.json'));
    }

    public function testGetClient()
    {
        self::assertTrue($this->api->getClient() instanceof Client);
    }

    public function testCsv()
    {
        self::assertTrue($this->api->csv() instanceof Csv);
    }

    public function testUsers()
    {
        self::assertTrue($this->api->users() instanceof Users);
    }

    public function testOrganizations()
    {
        self::assertTrue($this->api->organizations() instanceof Organizations);
    }

    public function testTitles()
    {
        self::assertTrue($this->api->titles() instanceof Titles);
    }

    public function testGroups()
    {
        self::assertTrue($this->api->groups() instanceof Groups);
    }

    public function testUserOrganizations()
    {
        self::assertTrue($this->api->userOrganizations() instanceof UserOrganizations);
    }

    public function testUserGroups()
    {
        self::assertTrue($this->api->userGroups() instanceof UserGroups);
    }

    public function testUserServices()
    {
        self::assertTrue($this->api->userServices() instanceof UserServices);
    }

    public function testOrganizationUsers()
    {
        self::assertTrue($this->api->organizationUsers() instanceof OrganizationUsers);
    }
}
