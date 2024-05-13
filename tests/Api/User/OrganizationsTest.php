<?php

namespace CybozuHttp\Tests\Api\User;

use PHPUnit\Framework\TestCase;
use UserTestHelper;

use League\Csv\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class OrganizationsTest extends TestCase
{
    /**
     * @var UserApi
     */
    private UserApi $api;

    protected function setup(): void
    {
        $this->api = UserTestHelper::getUserApi();
    }

    public function testCsv(): void
    {
        $content = $this->api->organizations()->getByCsv();
        $exportFilepath = __DIR__ . '/../../_output/export-organizations.csv';
        file_put_contents($exportFilepath, $content);

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
        $csv = Reader::createFromString($content);
        $records = $csv->getRecords();
        $flg1 = $flg2 = false;
        foreach ($records as $row) {
            if ('example-org1' === reset($row)) {
                $flg1 = true;
            }
            if ('example-org2' === reset($row)) {
                $flg2 = true;
            }
        }
        $this->assertTrue($flg1 and $flg2);

        $id = $this->api->organizations()->postByCsv($exportFilepath);
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
