<?php

namespace CybozuHttp\Api;

use CybozuHttp\Api\User\Csv;
use CybozuHttp\Api\User\Groups;
use CybozuHttp\Api\User\Organizations;
use CybozuHttp\Api\User\OrganizationUsers;
use CybozuHttp\Api\User\Titles;
use CybozuHttp\Api\User\UserGroups;
use CybozuHttp\Api\User\UserOrganizations;
use CybozuHttp\Api\User\Users;
use CybozuHttp\Api\User\UserServices;
use CybozuHttp\Client;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserApi
{
    public const API_PREFIX = '/v1/';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var Users
     */
    private $users;

    /**
     * @var Organizations
     */
    private $organizations;

    /**
     * @var Titles
     */
    private $titles;

    /**
     * @var Groups
     */
    private $groups;

    /**
     * @var UserOrganizations
     */
    private $userOrganizations;

    /**
     * @var UserGroups
     */
    private $userGroups;

    /**
     * @var UserServices
     */
    private $userServices;

    /**
     * @var OrganizationUsers
     */
    private $organizationUsers;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->csv = new Csv($client);
        $this->users = new Users($client, $this->csv);
        $this->organizations = new Organizations($this->csv);
        $this->titles = new Titles($this->csv);
        $this->groups = new Groups($this->csv);
        $this->userOrganizations = new UserOrganizations($client, $this->csv);
        $this->userGroups = new UserGroups($this->csv);
        $this->userServices = new UserServices($this->csv);
        $this->organizationUsers = new OrganizationUsers($client);
    }

    /**
     * @param string $api
     * @return string
     */
    public static function generateUrl($api): string
    {
        return self::API_PREFIX . $api;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return Csv
     */
    public function csv(): Csv
    {
        return $this->csv;
    }

    /**
     * @return Users
     */
    public function users(): Users
    {
        return $this->users;
    }

    /**
     * @return Organizations
     */
    public function organizations(): Organizations
    {
        return $this->organizations;
    }

    /**
     * @return Titles
     */
    public function titles(): Titles
    {
        return $this->titles;
    }

    /**
     * @return Groups
     */
    public function groups(): Groups
    {
        return $this->groups;
    }

    /**
     * @return UserOrganizations
     */
    public function userOrganizations(): UserOrganizations
    {
        return $this->userOrganizations;
    }

    /**
     * @return UserGroups
     */
    public function userGroups(): UserGroups
    {
        return $this->userGroups;
    }

    /**
     * @return UserServices
     */
    public function userServices(): UserServices
    {
        return $this->userServices;
    }

    /**
     * @return OrganizationUsers
     */
    public function organizationUsers(): OrganizationUsers
    {
        return $this->organizationUsers;
    }
}
