<?php

namespace CybozuHttp\Tests\Api;

require_once __DIR__ . '/../_support/UserTestHelper.php';
use PHPUnit\Framework\TestCase;
use UserTestHelper;

use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserApiTest extends TestCase
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
}
