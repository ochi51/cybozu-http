<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use League\Csv\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserGroupsTest extends TestCase
{
    /**
     * @var UserApi
     */
    private UserApi $api;

    protected function setup(): void
    {
        $this->api = UserTestHelper::getUserApi();

        $filename = __DIR__ . '/../../_data/users.csv';
        $id = $this->api->users()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                $this->assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $filename = __DIR__ . '/../../_data/groups.csv';
        $id = $this->api->groups()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                $this->assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }
    }

    public function testCsv(): void
    {
        $filename = __DIR__ . '/../../_data/user-groups.csv';
        $id = $this->api->userGroups()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                $this->assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $content = $this->api->userGroups()->getByCsv();
        $csv = Reader::createFromString($content);
        $records = $csv->getRecords();
        foreach ($records as $row) {
            if ('example-title1' === reset($row)) {
                $this->assertEquals([
                    'test1@example.com','example-group1'
                ], $row);
            }
            if ('example-title2' === reset($row)) {
                $this->assertEquals([
                    'test2@example.com','example-group2'
                ], $row);
            }
        }
    }

    protected function tearDown(): void
    {
        $filename = __DIR__ . '/../../_data/delete-users.csv';
        $id = $this->api->users()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                $this->assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $filename = __DIR__ . '/../../_data/delete-groups.csv';
        $id = $this->api->groups()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                $this->assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }
    }

}
