<?php

namespace CybozuHttp\Tests\Api;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserApiTest extends TestCase
{

    protected function setup(): void
    {
        $api = UserTestHelper::getUserApi();
        $this->assertTrue((bool)$api->getClient());
    }

    public function testGenerateUrl(): void
    {
        $this->assertEquals('/v1/users.json', UserApi::generateUrl('users.json'));
    }
}
