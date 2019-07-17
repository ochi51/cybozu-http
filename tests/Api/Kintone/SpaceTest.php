<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;
use GuzzleHttp\Exception\RequestException;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class SpaceTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var int
     */
    private $spaceId;

    /**
     * @var int
     */
    private $guestSpaceId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
    }

    public function testGet(): void
    {
        $space = $this->api->space()->get($this->spaceId);
        $this->assertEquals($this->spaceId, $space['id']);
        $this->assertEquals('cybozu-http test space', $space['name']);
        $this->assertEquals(true, $space['isPrivate']);
        $this->assertEquals(KintoneTestHelper::getConfig()['login'], $space['creator']['code']);
        $this->assertEquals(KintoneTestHelper::getConfig()['login'], $space['modifier']['code']);
        $this->assertEquals(false, $space['isGuest']);

        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $this->assertEquals($this->guestSpaceId, $guestSpace['id']);
        $this->assertEquals('cybozu-http test space', $guestSpace['name']);
        $this->assertEquals(true, $guestSpace['isPrivate']);
        $this->assertEquals(KintoneTestHelper::getConfig()['login'], $guestSpace['creator']['code']);
        $this->assertEquals(KintoneTestHelper::getConfig()['login'], $guestSpace['modifier']['code']);
        $this->assertEquals(true, $guestSpace['isGuest']);
    }

    public function testPost(): void
    {
        // Post space in setup
        $space = $this->api->space()->get($this->spaceId);
        $this->assertEquals($this->spaceId, $space['id']);

        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $this->assertEquals($this->guestSpaceId, $guestSpace['id']);
    }

    public function testDelete(): void
    {
        $id = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($id);
        $this->assertEquals($id, $space['id']);

        $this->api->space()->delete($id);
        try {
            $this->api->space()->get($id);
            self::fail('ERROR!! Not throw exception');
        } catch (RequestException $e) {
            $this->assertTrue(true);
        } catch (\Exception $e) {
            self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
        }
    }

    public function testPutBody(): void
    {
        $space = $this->api->space()->get($this->spaceId);
        if ($space['useMultiThread']) {
            $this->api->space()->putBody($this->spaceId, '<p>Change body</p>');
            $space = $this->api->space()->get($this->spaceId);
            $this->assertEquals('<p>Change body</p>', $space['body']);
        } else {
            try {
                $this->api->space()->putBody($this->spaceId, '<p>Change body</p>');
                self::fail('ERROR!! Not throw exception');
            } catch (RequestException $e) {
                $this->assertTrue(true);
            } catch (\Exception $e) {
                self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
            }
        }

        $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        if ($guestSpace['useMultiThread']) {
            $this->api->space()->putBody($this->guestSpaceId, 'Change body', $this->guestSpaceId);
            $guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
            $this->assertEquals('Change body', $guestSpace['body']);
        } else {
            try {
                $this->api->space()->putBody($this->guestSpaceId, 'Change body', $this->guestSpaceId);
                self::fail('ERROR!! Not throw exception');
            } catch (RequestException $e) {
                $this->assertTrue(true);
            } catch (\Exception $e) {
                self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
            }
        }
    }

    public function testGetMembers(): void
    {
        $testMembers = [[
            'entity' => [
                'type' => 'USER',
                'code' => KintoneTestHelper::getConfig()['login']
            ],
            'isAdmin' => true,
            'isImplicit' => false
        ]];

        $members = $this->api->space()->getMembers($this->spaceId);
        $this->assertEquals($members['members'], $testMembers);
        $members = $this->api->space()->getMembers($this->guestSpaceId, $this->guestSpaceId);
        $this->assertEquals($members['members'], $testMembers);
    }

    public function testPutMembers(): void
    {
        $putMembers = [[
            'entity' => [
                'type' => 'USER',
                'code' => KintoneTestHelper::getConfig()['login']
            ],
            'isAdmin' => true
        ],[
            'entity' => [
                'type' => 'GROUP',
                'code' => 'Administrators'
            ],
            'isAdmin' => false
        ]];

        $this->api->space()->putMembers($this->spaceId, $putMembers);
        $members = $this->api->space()->getMembers($this->spaceId);
        foreach ($members['members'] as $member) {
            $code = $member['entity']['code'];
            if ($code === KintoneTestHelper::getConfig()['login']) {
                $this->assertEquals($member, [
                    'entity' => [
                        'type' => 'USER',
                        'code' => KintoneTestHelper::getConfig()['login']
                    ],
                    'isAdmin' => true,
                    'isImplicit' => false
                ]);
            }
            if ($code === 'Administrators') {
                $this->assertEquals($member, [
                    'entity' => [
                        'type' => 'GROUP',
                        'code' => 'Administrators'
                    ],
                    'isAdmin' => false
                ]);
            }
        }
    }

    public function testPutGuest(): void
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
        $this->api->space()->putGuests($this->guestSpaceId, [
            'test1@example.com',
            'test2@example.com'
        ]);
        // kintone does not have the get guest users api.
        $this->assertTrue(true);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
