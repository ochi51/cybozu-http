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
        self::assertInstanceOf(Client::class, $this->api->getClient());
    }

    public function testCsv()
    {
        self::assertInstanceOf(Csv::class, $this->api->csv());
    }

    public function testUsers()
    {
        self::assertInstanceOf(Users::class, $this->api->users());
    }

    public function testOrganizations()
    {
        self::assertInstanceOf(Organizations::class, $this->api->organizations());
    }

    public function testTitles()
    {
        self::assertInstanceOf(Titles::class, $this->api->titles());
    }

    public function testGroups()
    {
        self::assertInstanceOf(Groups::class, $this->api->groups());
    }

    public function testUserOrganizations()
    {
        self::assertInstanceOf(UserOrganizations::class, $this->api->userOrganizations());
    }

    public function testUserGroups()
    {
        self::assertInstanceOf(UserGroups::class, $this->api->userGroups());
    }

    public function testUserServices()
    {
        self::assertInstanceOf(UserServices::class, $this->api->userServices());
    }

    public function testOrganizationUsers()
    {
        self::assertInstanceOf(OrganizationUsers::class, $this->api->organizationUsers());
    }
}
