<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use League\Csv\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CsvTest extends TestCase
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
        $content = $this->api->csv()->get('user');
        $csv = Reader::createFromString($content);
        $records = $csv->getRecords();
        $flg = false;
        foreach ($records as $record) {
            if (UserTestHelper::getConfig()['login'] === reset($record)) {
                $flg = true;
                break;
            }
        }
        $this->assertTrue($flg);

        try {
            $this->api->csv()->get('aaa');
            self::fail('Not throw InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    public function testPost(): void
    {
        $id = $this->api->csv()->post('user', __DIR__ . '/../../_data/users.csv');
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

        $id = $this->api->csv()->post('user', __DIR__ . '/../../_data/delete-users.csv');
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

    public function testPostKey(): void
    {
        try {
            $this->api->csv()->postKey('aaa', 'key');
            self::fail('Not throw InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
}
