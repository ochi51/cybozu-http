<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use GuzzleHttp\Exception\RequestException;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class GuestsTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
    }

    public function testPostAndDelete(): void
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
        $this->assertTrue(true);

        $this->api->guests()->delete([
            'test1@example.com',
            'test2@example.com'
        ]);
        // kintone does not have the get guest users api.
        $this->assertTrue(true);
    }
}
