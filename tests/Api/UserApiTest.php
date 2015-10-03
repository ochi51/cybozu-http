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
        $this->assertEquals('/v1/users.json', UserApi::generateUrl('users.json'));
    }

    public function testGetClient()
    {
        $this->assertTrue($this->api->getClient() instanceof Client);
    }

    public function testCsv()
    {
        $this->assertTrue($this->api->csv() instanceof Csv);
    }

    public function testUsers()
    {
        $this->assertTrue($this->api->users() instanceof Users);
    }

    public function testOrganizations()
    {
        $this->assertTrue($this->api->organizations() instanceof Organizations);
    }

    public function testTitles()
    {
        $this->assertTrue($this->api->titles() instanceof Titles);
    }

    public function testGroups()
    {
        $this->assertTrue($this->api->groups() instanceof Groups);
    }

    public function testUserOrganizations()
    {
        $this->assertTrue($this->api->userOrganizations() instanceof UserOrganizations);
    }

    public function testUserGroups()
    {
        $this->assertTrue($this->api->userGroups() instanceof UserGroups);
    }

    public function testUserServices()
    {
        $this->assertTrue($this->api->userServices() instanceof UserServices);
    }

    public function testOrganizationUsers()
    {
        $this->assertTrue($this->api->organizationUsers() instanceof OrganizationUsers);
    }
}
