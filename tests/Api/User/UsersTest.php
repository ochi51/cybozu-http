<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use League\Csv\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UsersTest extends TestCase
{
    /**
     * @var UserApi
     */
    private UserApi $api;

    protected function setup(): void
    {
        $this->api = UserTestHelper::getUserApi();
    }

    public function testGet(): void
    {
        $config = UserTestHelper::getConfig();
        $users = $this->api->users()->get([], [$config['login']]);
        $this->assertEquals($users[0]['code'], $config['login']);
    }

    public function testCsv(): void
    {
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
        $content = $this->api->users()->getByCsv();
        $csv = Reader::createFromString($content);
        $records = $csv->getRecords();
        $flg1 = $flg2 = false;
        foreach ($records as $row) {
            if ('test1@example.com' === reset($row)) {
                $flg1 = true;
            }
            if ('test2@example.com' === reset($row)) {
                $flg2 = true;
            }
        }
        $this->assertTrue($flg1 and $flg2);

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
    }
}
