<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class PreviewAppTest extends TestCase
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

        $this->appId = $this->api->preview()
            ->post('test app', $this->spaceId, $this->space['defaultThread'])['app'];
        $this->guestAppId = $this->api->preview()
            ->post('test app', $this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId)['app'];
    }

    public function testDeploy(): void
    {
        $this->space = $this->api->space()->get($this->spaceId);
        $appId = $this->api->preview()
            ->post('test deploy app', $this->spaceId, $this->space['defaultThread'])['app'];
        $this->api->preview()->putSettings(
            $appId,
            'test deploy app',
            'test deploy app description',
            ['type' => 'PRESET', 'key' => 'APP72'],
            'WHITE'
        );
        $putFields = KintoneTestHelper::getFields();
        $this->api->preview()->postFields($appId, $putFields);
        $putViews = KintoneTestHelper::getViews();
        $this->api->preview()->putViews($appId, $putViews)['views'];
        $this->api->preview()->deploy($appId);

        while (1) {
            if ('PROCESSING' !== $this->api->preview()->getDeployStatus($appId)['status']) {
                break;
            }
        }

        $app = $this->api->app()->get($appId);
        $this->assertEquals($app['name'], 'test deploy app');
        $this->assertEquals($app['description'], 'test deploy app description');
        $this->assertEquals($app['spaceId'], $this->spaceId);
        $this->assertEquals($app['threadId'], $this->space['defaultThread']);


        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);
        $appId = $this->api->preview()
            ->post('test deploy app', $this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId)['app'];
        $this->api->preview()->putSettings(
            $appId,
            'test deploy app',
            'test deploy app description',
            ['type' => 'PRESET', 'key' => 'APP72'],
            'WHITE',
            $this->guestSpaceId
        );
        $putFields = KintoneTestHelper::getFields();
        $this->api->preview()->postFields($appId, $putFields, $this->guestSpaceId);
        $putViews = KintoneTestHelper::getViews();
        $this->api->preview()->putViews($appId, $putViews, $this->guestSpaceId)['views'];
        $this->api->preview()->deploy($appId, $this->guestSpaceId);

        while (1) {
            if ('PROCESSING' !== $this->api->preview()->getDeployStatus($appId, $this->guestSpaceId)['status']) {
                break;
            }
        }

        $app = $this->api->app()->get($appId, $this->guestSpaceId);
        $this->assertEquals($app['name'], 'test deploy app');
        $this->assertEquals($app['description'], 'test deploy app description');
        $this->assertEquals($app['spaceId'], $this->guestSpaceId);
        $this->assertEquals($app['threadId'], $this->guestSpace['defaultThread']);
    }

    public function testSettings(): void
    {
        $this->api->preview()->putSettings(
            $this->appId,
            'test app',
            'test app description',
            ['type' => 'PRESET', 'key' => 'APP72'],
            'WHITE'
        );
        $settings = $this->api->preview()->getSettings($this->appId);
        $this->assertEquals('test app', $settings['name']);
        $this->assertEquals('test app description', $settings['description']);
        $this->assertEquals(['type' => 'PRESET', 'key' => 'APP72'], $settings['icon']);
        $this->assertEquals('WHITE', $settings['theme']);

        $this->api->preview()->putSettings(
            $this->guestAppId,
            'test guest space app',
            'test guest space app description',
            ['type' => 'PRESET', 'key' => 'APP60'],
            'WHITE',
            $this->guestSpaceId
        );
        $settings = $this->api->preview()->getSettings($this->guestAppId, $this->guestSpaceId);
        $this->assertEquals('test guest space app', $settings['name']);
        $this->assertEquals('test guest space app description', $settings['description']);
        $this->assertEquals(['type' => 'PRESET', 'key' => 'APP60'], $settings['icon']);
        $this->assertEquals('WHITE', $settings['theme']);
    }

    public function testFields(): void
    {
        $putFields = KintoneTestHelper::getFields();

        $this->api->preview()->postFields($this->appId, $putFields);
        $fields = $this->api->preview()->getFields($this->appId)['properties'];
        foreach ($putFields as $code => $field) {
            $this->assertEquals($fields[$code], $field);
        }

        $this->api->preview()->putFields($this->appId, [
            'single_text' => [
                'type' => 'SINGLE_LINE_TEXT',
                'label' => 'Single text change'
            ]
        ]);
        $fields = $this->api->preview()->getFields($this->appId)['properties'];
        $this->assertEquals($fields['single_text']['label'], 'Single text change');

        $this->api->preview()->deleteFields($this->appId, ['single_text']);
        $fields = $this->api->preview()->getFields($this->appId)['properties'];
        $this->assertArrayNotHasKey('single_text', $fields);

        $this->api->preview()->postFields($this->guestAppId, $putFields, $this->guestSpaceId);
        $fields = $this->api->preview()->getFields($this->guestAppId, $this->guestSpaceId)['properties'];
        foreach ($putFields as $code => $field) {
            $this->assertEquals($fields[$code], $field);
        }

        $this->api->preview()->putFields($this->guestAppId, [
            'single_text' => [
                'type' => 'SINGLE_LINE_TEXT',
                'label' => 'Single text change'
            ]
        ], $this->guestSpaceId);
        $fields = $this->api->preview()->getFields($this->guestAppId, $this->guestSpaceId)['properties'];
        $this->assertEquals($fields['single_text']['label'], 'Single text change');

        $this->api->preview()->deleteFields($this->guestAppId, ['single_text'], $this->guestSpaceId);
        $fields = $this->api->preview()->getFields($this->guestAppId, $this->guestSpaceId)['properties'];
        $this->assertArrayNotHasKey('single_text', $fields);
    }

    public function testLayout(): void
    {
        $putFields = KintoneTestHelper::getFields();
        $this->api->preview()->postFields($this->appId, $putFields);
        $this->api->preview()->postFields($this->guestAppId, $putFields, $this->guestSpaceId);

        $putLayout = KintoneTestHelper::getLayout();

        $this->api->preview()->putLayout($this->appId, $putLayout);
        $layout = $this->api->preview()->getLayout($this->appId)['layout'];
        $this->assertEquals($layout, $putLayout);

        $this->api->preview()->putLayout($this->guestAppId, $putLayout, $this->guestSpaceId);
        $layout = $this->api->preview()->getLayout($this->guestAppId, $this->guestSpaceId)['layout'];
        $this->assertEquals($layout, $putLayout);
    }

    public function testViews(): void
    {
        $putFields = KintoneTestHelper::getFields();
        $this->api->preview()->postFields($this->appId, $putFields);
        $this->api->preview()->postFields($this->guestAppId, $putFields, $this->guestSpaceId);

        $putViews = KintoneTestHelper::getViews();

        $resp = $this->api->preview()->putViews($this->appId, $putViews)['views'];
        $views = $this->api->preview()->getViews($this->appId)['views'];
        foreach ($views as $key => $view) {
            $this->assertEquals($view['id'], $resp[$key]['id']);
        }
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k === 'id') {
                    continue;
                }
                $this->assertEquals($v, $views[$key][$k]);
            }
        }

        $resp = $this->api->preview()->putViews($this->guestAppId, $putViews, $this->guestSpaceId)['views'];
        $views = $this->api->preview()->getViews($this->guestAppId, $this->guestSpaceId)['views'];
        foreach ($views as $key => $view) {
            $this->assertEquals($view['id'], $resp[$key]['id']);
        }
        foreach ($putViews as $key => $view) {
            foreach ($view as $k => $v) {
                if ($k === 'id') {
                    continue;
                }
                $this->assertEquals($v, $views[$key][$k]);
            }
        }
    }

    public function testAcl(): void
    {
        $putAcl = KintoneTestHelper::getAppAcl();

        $this->api->preview()->putAcl($this->appId, $putAcl);
        $acl = $this->api->preview()->getAcl($this->appId)['rights'];
        foreach ($putAcl as $pv) {
            foreach ($acl as $v) {
                if ($pv['entity'] === $v['entity']) {
                    $this->assertEquals($pv, $v);
                }
            }
        }

        $this->api->preview()->putAcl($this->guestAppId, $putAcl, $this->guestSpaceId);
        $acl = $this->api->preview()->getAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        foreach ($putAcl as $pv) {
            foreach ($acl as $v) {
                if ($pv['entity'] === $v['entity']) {
                    $this->assertEquals($pv, $v);
                }
            }
        }
    }

    public function testRecordAcl(): void
    {
        $putAcl = KintoneTestHelper::getRecordAcl();

        $this->api->preview()->putRecordAcl($this->appId, $putAcl);
        $acl = $this->api->preview()->getRecordAcl($this->appId)['rights'];
        foreach ($putAcl[0]['entities'] as $pv) {
            foreach ($acl[0]['entities'] as $v) {
                if ($pv['entity'] === $v['entity']) {
                    $this->assertEquals($pv, $v);
                }
            }
        }

        $this->api->preview()->putRecordAcl($this->guestAppId, $putAcl, $this->guestSpaceId);
        $acl = $this->api->preview()->getRecordAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        foreach ($putAcl[0]['entities'] as $pv) {
            foreach ($acl[0]['entities'] as $v) {
                if ($pv['entity'] === $v['entity']) {
                    $this->assertEquals($pv, $v);
                }
            }
        }
    }

    public function testFieldAcl(): void
    {
        $putFields = KintoneTestHelper::getFields();
        $this->api->preview()->postFields($this->appId, $putFields);
        $this->api->preview()->postFields($this->guestAppId, $putFields, $this->guestSpaceId);

        $putAcl = KintoneTestHelper::getFieldAcl();

        $this->api->preview()->putFieldAcl($this->appId, $putAcl);
        $acl = $this->api->preview()->getFieldAcl($this->appId)['rights'];
        foreach ($putAcl as $k => $tmp) {
            foreach ($putAcl[$k]['entities'] as $pv) {
                foreach ($acl[$k]['entities'] as $v) {
                    if ($pv['entity'] === $v['entity']) {
                        $this->assertEquals($pv, $v);
                    }
                }
            }
        }

        $this->api->preview()->putFieldAcl($this->guestAppId, $putAcl, $this->guestSpaceId);
        $acl = $this->api->preview()->getFieldAcl($this->guestAppId, $this->guestSpaceId)['rights'];
        foreach ($putAcl as $k => $tmp) {
            foreach ($putAcl[$k]['entities'] as $pv) {
                foreach ($acl[$k]['entities'] as $v) {
                    if ($pv['entity'] === $v['entity']) {
                        $this->assertEquals($pv, $v);
                    }
                }
            }
        }
    }

    public function testCustomize(): void
    {
        $this->api->preview()->putCustomize($this->appId, [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example.js'
        ]], [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example.css'
        ]], [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example-mobile.js'
        ]]);
        $customize = $this->api->preview()->getCustomize($this->appId);
        $this->assertEquals($customize['desktop']['js'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example.js'
        ]);
        $this->assertEquals($customize['desktop']['css'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example.css'
        ]);
        $this->assertEquals($customize['mobile']['js'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example-mobile.js'
        ]);

        $this->api->preview()->putCustomize($this->guestAppId, [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example.js'
        ]], [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example.css'
        ]], [[
            'type' => 'URL',
            'url' => 'https://www.example.com/example-mobile.js'
        ]], $this->guestSpaceId, 'ADMIN');
        $customize = $this->api->preview()->getCustomize($this->guestAppId, $this->guestSpaceId);
        $this->assertEquals($customize['desktop']['js'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example.js'
        ]);
        $this->assertEquals($customize['desktop']['css'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example.css'
        ]);
        $this->assertEquals($customize['mobile']['js'][0], [
            'type' => 'URL',
            'url' => 'https://www.example.com/example-mobile.js'
        ]);
        $this->assertEquals($customize['scope'], 'ADMIN');
    }

    public function testStatus(): void
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

        $this->api->preview()->putStatus($this->appId, $states, $actions);
        $response = $this->api->preview()->getStatus($this->appId);
        $this->assertEquals($response['enable'], true);
        $this->assertEquals($response['states'], $states);
        $this->assertEquals($response['actions'], $actions);

        $this->api->preview()->putStatus($this->guestAppId, $states, $actions, true, $this->guestSpaceId);
        $response = $this->api->preview()->getStatus($this->guestAppId, 'ja', $this->guestSpaceId);
        $this->assertEquals($response['enable'], true);
        $this->assertEquals($response['states'], $states);
        $this->assertEquals($response['actions'], $actions);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
