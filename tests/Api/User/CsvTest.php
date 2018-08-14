<?php

namespace CybozuHttp\Tests\Api\User;

require_once __DIR__ . '/../../_support/UserTestHelper.php';
use UserTestHelper;

use EasyCSV\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CsvTest extends \PHPUnit_Framework_TestCase
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
        $content = $this->api->csv()->get('user');

        $path = __DIR__ . '/../../_output/export-csv.csv';
        file_put_contents($path, $content);
        $getCsv = new Reader($path, 'r+', false);
        $flg = false;
        while ($row = $getCsv->getRow()) {
            if (UserTestHelper::getConfig()['login'] === reset($row)) {
                $flg = true;
            }
        }
        self::assertTrue($flg);

        try {
            $this->api->csv()->get('aaa');
            self::fail('Not throw InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
    }

    public function testPost()
    {
        $id = $this->api->csv()->post('user', __DIR__ . '/../../_data/users.csv');
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
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
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }
    }

    public function testPostKey()
    {
        try {
            $this->api->csv()->postKey('aaa', 'key');
            self::fail('Not throw InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
    }
}
