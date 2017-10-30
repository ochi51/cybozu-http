<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class AppTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var integer
     */
    private $spaceId;

    /**
     * @var array
     */
    private $space;

    /**
     * @var integer
     */
    private $guestSpaceId;

    /**
     * @var array
     */
    private $guestSpace;

    /**
     * @var integer
     */
    private $appId;

    /**
     * @var integer
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

    public function testGet()
    {
        $app = $this->api->app()->get($this->appId);
        self::assertEquals($app['appId'], $this->appId);
        self::assertEquals($app['name'], 'cybozu-http test app');
        self::assertEquals($app['spaceId'], $this->spaceId);
        self::assertEquals($app['threadId'], $this->space['defaultThread']);

        $app = $this->api->app()->get($this->guestAppId, $this->guestSpaceId);
        self::assertEquals($app['appId'], $this->guestAppId);
        self::assertEquals($app['name'], 'cybozu-http test app');
        self::assertEquals($app['spaceId'], $this->guestSpaceId);
        self::assertEquals($app['threadId'], $this->guestSpace['defaultThread']);
    }

    public function testGetSetting()
    {
        $settings = $this->api->app()->getSettings($this->appId);
        self::assertEquals('cybozu-http test app', $settings['name']);
        self::assertEquals('cybozu-http test app', $settings['description']);
        self::assertEquals(['type' => 'PRESET', 'key' => 'APP72'], $settings['icon']);
        self::assertEquals('WHITE', $settings['theme']);

        $settings = $this->api->app()->getSettings($this->guestAppId, $this->guestSpaceId);
        self::assertEquals('cybozu-http test app', $settings['name']);
        self::assertEquals('cybozu-http test app', $settings['description']);
        self::assertEquals(['type' => 'PRESET', 'key' => 'APP72'], $settings['icon']);
        self::assertEquals('WHITE', $settings['theme']);
    }

    public function testGetForm()
    {
        $putFields = KintoneTestHelper::getFields();

        $forms = $this->api->app()->getForm($this->appId);
        $f = function ($forms, $code) {
            foreach ($forms as $form) {
                if ($form['code'] === $code)
                    return true;
            }
            return false;
        };
        foreach ($putFields as $code => $field) {
            self::assertTrue($f($forms, $code));
        }

        $forms = $this->api->app()->getForm($this->guestAppId, $this->guestSpaceId);
        $f = function ($forms, $code) {
            foreach ($forms as $form) {
                if ($form['code'] === $code)
                    return true;
            }
            return false;
        };
        foreach ($putFields as $code => $field) {
            self::assertTrue($f($forms, $code));
        }
    }

    public function testGetFields()
    {
        $putFields = KintoneTestHelper::getFields();

        $fields = $this->api->app()->getFields($this->appId)['properties'];
        foreach ($putFields as $code => $field) {
            self::assertEquals($fields[$code], $field);
        }

        $fields = $this->api->app()->getFields($this->guestAppId, $this->guestSpaceId)['properties'];
        foreach ($putFields as $code => $field) {
            self::assertEquals($fields[$code], $field);
        }
    }

    public function testGetLayout()
    {
        $putLayout = KintoneTestHelper::getLayout();

        $layout = $this->api->app()->getLayout($this->appId)['layout'];
        self::assertEquals($layout, $putLayout);

        $layout = $this->api->app()->getLayout($this->guestAppId, $this->guestSpaceId)['layout'];
        self::assertEquals($layout, $putLayout);
    }

    public function testGetViews()
    {
        $putViews = KintoneTestHelper::getViews();

        $views = $this->api->app()->getViews($this->appId)['views'];
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k == 'id') {
                    continue;
                }
                self::assertEquals($v, $views[$key][$k]);
            }
        }

        $views = $this->api->app()->getViews($this->guestAppId, $this->guestSpaceId)['views'];
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k == 'id') {
                    continue;
                }
                self::assertEquals($v, $views[$key][$k]);
            }
        }
    }

    public function testGetAcl()
    {
        $acl = $this->api->app()->getAcl($this->appId)['rights'];
        self::assertEquals($acl[0]['entity'], [
            'code' => null,
            'type' => 'CREATOR'
        ]);
        self::assertEquals($acl[1]['entity'], [
            'code' => 'everyone',
            'type' => 'GROUP'
        ]);

        $acl = $this->api->app()->getAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        self::assertEquals($acl[0]['entity'], [
            'code' => null,
            'type' => 'CREATOR'
        ]);
        self::assertEquals($acl[1]['entity'], [
            'code' => 'everyone',
            'type' => 'GROUP'
        ]);
    }

    public function testGetRecordAcl()
    {
        $acl = $this->api->app()->getRecordAcl($this->appId)['rights'];
        self::assertEquals($acl, []);

        $acl = $this->api->app()->getRecordAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        self::assertEquals($acl, []);
    }

    public function testGetFieldAcl()
    {
        $acl = $this->api->app()->getFieldAcl($this->appId)['rights'];
        self::assertEquals($acl, []);

        $acl = $this->api->app()->getFieldAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        self::assertEquals($acl, []);
    }

    public function testGetCustomize()
    {
        $customize = $this->api->app()->getCustomize($this->appId);
        self::assertEquals($customize['desktop'], ['js' => [], 'css' => []]);
        self::assertEquals($customize['mobile'], ['js' => []]);
        self::assertEquals($customize['scope'], 'ALL');

        $customize = $this->api->app()->getCustomize($this->guestAppId, $this->guestSpaceId);
        self::assertEquals($customize['desktop'], ['js' => [], 'css' => []]);
        self::assertEquals($customize['mobile'], ['js' => []]);
        self::assertEquals($customize['scope'], 'ALL');
    }

    public function testGetStatus()
    {
        $states = [
            'statusName1' => [
                'name' => 'statusName1',
                'index' => '0',
                'assignee' => [
                    'type' => 'ONE',
                    'entities' => [
                        [
                            'entity' => [
                                'type' => 'FIELD_ENTITY',
                                'code' => '作成者'
                            ],
                            'includeSubs' => false
                        ]
                    ]
                ]
            ],
            'statusName2' => [
                'name' => 'statusName2',
                'index' => '1',
                'assignee' => [
                    'type' => 'ONE',
                    'entities' => [
                        [
                            'entity' => [
                                'type' => 'USER',
                                'code' => KintoneTestHelper::getConfig()['login']
                            ],
                            'includeSubs' => false
                        ]
                    ]
                ]
            ]
        ];
        $actions = [
            [
                'name' => 'actionName1',
                'from' => 'statusName1',
                'to' => 'statusName2',
                'filterCond' => ''
            ]
        ];

        $this->api->preview()->putStatus($this->appId, $states, $actions, true);
        $this->api->preview()->deploy($this->appId);
        while (1) {
            if ('PROCESSING' != $this->api->preview()->getDeployStatus($this->appId)['status']) {
                break;
            }
        }

        $response = $this->api->app()->getStatus($this->appId);
        self::assertEquals($response['enable'], true);
        self::assertEquals($response['states'], $states);
        self::assertEquals($response['actions'], $actions);

        $this->api->preview()->putStatus($this->guestAppId, $states, $actions, true, $this->guestSpaceId);
        $this->api->preview()->deploy($this->guestAppId, $this->guestSpaceId);
        while (1) {
            if ('PROCESSING' != $this->api->preview()->getDeployStatus($this->guestAppId, $this->guestSpaceId)['status']) {
                break;
            }
        }

        $response = $this->api->app()->getStatus($this->guestAppId, 'ja', $this->guestSpaceId);
        self::assertEquals($response['enable'], true);
        self::assertEquals($response['states'], $states);
        self::assertEquals($response['actions'], $actions);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
