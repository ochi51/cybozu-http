<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class AppTest extends TestCase
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
     * @var array
     */
    private $space;

    /**
     * @var int
     */
    private $guestSpaceId;

    /**
     * @var array
     */
    private $guestSpace;

    /**
     * @var int
     */
    private $appId;

    /**
     * @var int
     */
    private $guestAppId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->space = $this->api->space()->get($this->spaceId);
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $this->space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testGet(): void
    {
        $app = $this->api->app()->get($this->appId);
        $this->assertEquals($app['appId'], $this->appId);
        $this->assertEquals($app['name'], 'cybozu-http test app');
        $this->assertEquals($app['spaceId'], $this->spaceId);
        $this->assertEquals($app['threadId'], $this->space['defaultThread']);

        $app = $this->api->app()->get($this->guestAppId, $this->guestSpaceId);
        $this->assertEquals($app['appId'], $this->guestAppId);
        $this->assertEquals($app['name'], 'cybozu-http test app');
        $this->assertEquals($app['spaceId'], $this->guestSpaceId);
        $this->assertEquals($app['threadId'], $this->guestSpace['defaultThread']);
    }

    public function testGetSetting(): void
    {
        $settings = $this->api->app()->getSettings($this->appId);
        $this->assertEquals('cybozu-http test app', $settings['name']);
        $this->assertEquals('cybozu-http test app', $settings['description']);
        $this->assertEquals(['type' => 'PRESET', 'key' => 'APP72'], $settings['icon']);
        $this->assertEquals('WHITE', $settings['theme']);

        $settings = $this->api->app()->getSettings($this->guestAppId, $this->guestSpaceId);
        $this->assertEquals('cybozu-http test app', $settings['name']);
        $this->assertEquals('cybozu-http test app', $settings['description']);
        $this->assertEquals(['type' => 'PRESET', 'key' => 'APP72'], $settings['icon']);
        $this->assertEquals('WHITE', $settings['theme']);
    }

    public function testGetForm(): void
    {
        $putFields = KintoneTestHelper::getFields();

        $forms = $this->api->app()->getForm($this->appId);
        $f = function ($forms, $code) {
            foreach ($forms as $form) {
                if ($form['code'] === $code) {
                    return true;
                }
            }
            return false;
        };
        foreach ($putFields as $code => $field) {
            $this->assertTrue($f($forms, $code));
        }

        $forms = $this->api->app()->getForm($this->guestAppId, $this->guestSpaceId);
        $f = function ($forms, $code) {
            foreach ($forms as $form) {
                if ($form['code'] === $code) {
                    return true;
                }
            }
            return false;
        };
        foreach ($putFields as $code => $field) {
            $this->assertTrue($f($forms, $code));
        }
    }

    public function testGetFields(): void
    {
        $putFields = KintoneTestHelper::getFields();

        $fields = $this->api->app()->getFields($this->appId)['properties'];
        foreach ($putFields as $code => $field) {
            $this->assertEquals($fields[$code], $field);
        }

        $fields = $this->api->app()->getFields($this->guestAppId, $this->guestSpaceId)['properties'];
        foreach ($putFields as $code => $field) {
            $this->assertEquals($fields[$code], $field);
        }
    }

    public function testGetLayout(): void
    {
        $putLayout = KintoneTestHelper::getLayout();

        $layout = $this->api->app()->getLayout($this->appId)['layout'];
        $this->assertEquals($layout, $putLayout);

        $layout = $this->api->app()->getLayout($this->guestAppId, $this->guestSpaceId)['layout'];
        $this->assertEquals($layout, $putLayout);
    }

    public function testGetViews(): void
    {
        $putViews = KintoneTestHelper::getViews();

        $views = $this->api->app()->getViews($this->appId)['views'];
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k === 'id') {
                    continue;
                }
                $this->assertEquals($v, $views[$key][$k]);
            }
        }

        $views = $this->api->app()->getViews($this->guestAppId, $this->guestSpaceId)['views'];
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k === 'id') {
                    continue;
                }
                $this->assertEquals($v, $views[$key][$k]);
            }
        }
    }

    public function testGetAcl(): void
    {
        $acl = $this->api->app()->getAcl($this->appId)['rights'];
        $this->assertEquals($acl[0]['entity'], [
            'code' => null,
            'type' => 'CREATOR'
        ]);
        $this->assertEquals($acl[1]['entity'], [
            'code' => 'everyone',
            'type' => 'GROUP'
        ]);

        $acl = $this->api->app()->getAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        $this->assertEquals($acl[0]['entity'], [
            'code' => null,
            'type' => 'CREATOR'
        ]);
        $this->assertEquals($acl[1]['entity'], [
            'code' => 'everyone',
            'type' => 'GROUP'
        ]);
    }

    public function testGetRecordAcl(): void
    {
        $acl = $this->api->app()->getRecordAcl($this->appId)['rights'];
        $this->assertEquals($acl, []);

        $acl = $this->api->app()->getRecordAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        $this->assertEquals($acl, []);
    }

    public function testGetFieldAcl(): void
    {
        $acl = $this->api->app()->getFieldAcl($this->appId)['rights'];
        $this->assertEquals($acl, []);

        $acl = $this->api->app()->getFieldAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        $this->assertEquals($acl, []);
    }

    public function testGetCustomize(): void
    {
        $customize = $this->api->app()->getCustomize($this->appId);
        $this->assertEquals($customize['desktop'], ['js' => [], 'css' => []]);
        $this->assertEquals($customize['mobile'], ['js' => [], 'css' => []]);
        $this->assertEquals($customize['scope'], 'ALL');

        $customize = $this->api->app()->getCustomize($this->guestAppId, $this->guestSpaceId);
        $this->assertEquals($customize['desktop'], ['js' => [], 'css' => []]);
        $this->assertEquals($customize['mobile'], ['js' => [], 'css' => []]);
        $this->assertEquals($customize['scope'], 'ALL');
    }

    public function testGetStatus(): void
    {
        $states = KintoneTestHelper::getStates();
        $actions = KintoneTestHelper::getActions();
        $response = $this->api->app()->getStatus($this->appId);
        $this->assertEquals($response['enable'], true);
        $this->assertEquals($response['states']['test1']['name'], $states['test1']['name']);
        $this->assertEquals($response['states']['test2']['name'], $states['test2']['name']);
        $this->assertEquals($response['actions'], $actions);

        $response = $this->api->app()->getStatus($this->guestAppId, 'ja', $this->guestSpaceId);
        $this->assertEquals($response['enable'], true);
        $this->assertEquals($response['states']['test1']['name'], $states['test1']['name']);
        $this->assertEquals($response['states']['test2']['name'], $states['test2']['name']);
        $this->assertEquals($response['actions'], $actions);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
