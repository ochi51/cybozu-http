<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use EasyCSV\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class OrganizationsTest extends TestCase
{
    /**
     * @var UserApi
     */
    private $api;

    protected function setup()
    {
        $this->api = UserTestHelper::getUserApi();
    }

    public function testCsv(): void
    {
        $content = $this->api->organizations()->getByCsv();
        $path = __DIR__ . '/../../_output/export-organizations.csv';
        file_put_contents($path, $content);

        $filename = __DIR__ . '/../../_data/orgs.csv';
        $id = $this->api->organizations()->postByCsv($filename);
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

        $content = $this->api->organizations()->getByCsv();
        $path = __DIR__ . '/../../_output/export-organizations1.csv';
        file_put_contents($path, $content);
        $getCsv = new Reader($path, 'r+', false);
        $flg1 = $flg2 = false;
        while ($row = $getCsv->getRow()) {
            if ('example-org1' === reset($row)) {
                $flg1 = true;
            }
            if ('example-org2' === reset($row)) {
                $flg2 = true;
            }
        }
        $this->assertTrue($flg1 and $flg2);

        $filename = __DIR__ . '/../../_output/export-organizations.csv';
        $id = $this->api->organizations()->postByCsv($filename);
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
