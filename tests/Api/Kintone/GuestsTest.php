<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use GuzzleHttp\Exception\RequestException;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class GuestsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
    }

    public function testPostAndDelete()
    {
        $guests = [[
            'code' => 'test1@example.com',
            'password' => 'password',
            'timezone' => 'Asia/Tokyo',
            'name' => 'test guest user1'
        ],[
            'code' => 'test2@example.com',
            'password' => 'password',
            'timezone' => 'Asia/Tokyo',
            'name' => 'test guest user2'
        ]];

        try {
            $this->api->guests()->delete([
                'test1@example.com',
                'test2@example.com'
            ]);
            sleep(1);
        } catch (RequestException $e) {
            // If not exist test guest users, no problem
        }

        $this->api->guests()->post($guests);
        // kintone does not have the get guest users api.
        self::assertTrue(true);

        $this->api->guests()->delete([
            'test1@example.com',
            'test2@example.com'
        ]);
        // kintone does not have the get guest users api.
        self::assertTrue(true);
    }
}
