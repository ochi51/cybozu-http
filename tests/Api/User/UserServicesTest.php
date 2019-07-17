<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use EasyCSV\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserServicesTest extends TestCase
{
    /**
     * @var UserApi
     */
    private $api;

    protected function setup()
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
    }

    public function testCsv(): void
    {
        $filename = __DIR__ . '/../../_data/user-services.csv';
        $id = $this->api->userServices()->postByCsv($filename);
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

        $content = $this->api->userServices()->getByCsv();
        $path = __DIR__ . '/../../_output/export-user-services.csv';
        file_put_contents($path, $content);
        $getCsv = new Reader($path, 'r+', false);
        while ($row = $getCsv->getRow()) {
            if ('example-title1' === reset($row)) {
                $this->assertEquals($row, [
                    'test1@example.com','ki','ga','sa','of'
                ]);
            }
            if ('example-title2' === reset($row)) {
                $this->assertEquals($row, [
                    'test2@example.com','ki','sa'
                ]);
            }
        }
    }

    protected function tearDown()
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
    }

}
