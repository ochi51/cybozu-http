<?php

use Symfony\Component\Yaml\Yaml;
use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class KintoneTestHelper
{

    /**
     * @var array
     */
    private static $config;

    /**
     * @var KintoneApi
     */
    private static $api;

    /**
     * @var integer
     */
    private static $spaceTemplateId;

    private static $graph;

    /**
     * @var array
     */
    private static $fields = [
        'single_text' =>
            [
                'code' => 'single_text',
                'defaultValue' => '',
                'expression' => '',
                'hideExpression' => false,
                'maxLength' => 64,
                'minLength' => 0,
                'label' => 'Single text',
                'noLabel' => false,
                'required' => true,
                'type' => 'SINGLE_LINE_TEXT',
                'unique' => false
            ],
        'number' =>
            [
                'code' => 'number',
                'defaultValue' => '12345',
                'digit' => true,
                'displayScale' => '',
                'maxValue' => '64',
                'minValue' => '0',
                'label' => 'Number',
                'noLabel' => true,
                'required' => false,
                'type' => 'NUMBER',
                'unique' => false,
                'unit' => '$',
                'unitPosition' => 'BEFORE'
            ],
        'radio_button' =>
            [
                'code' => 'radio_button',
                'defaultValue' => 'sample2',
                'label' => 'Radio button',
                'noLabel' => false,
                'options' =>
                    [
                        'sample1' =>
                            [
                                'label' => 'sample1',
                                'index' => 0,
                            ],
                        'sample2' =>
                            [
                                'label' => 'sample2',
                                'index' => 1,
                            ],
                        'sample3' =>
                            [
                                'label' => 'sample3',
                                'index' => 2,
                            ],
                    ],
                'align' => 'HORIZONTAL',
                'required' => true,
                'type' => 'RADIO_BUTTON',
            ],
        'checkbox' =>
            [
                'code' => 'checkbox',
                'defaultValue' =>
                    [
                        0 => 'sample1',
                        1 => 'sample3',
                    ],
                'label' => 'Checkbox',
                'noLabel' => false,
                'options' =>
                    [
                        'sample1' =>
                            [
                                'label' => 'sample1',
                                'index' => 0,
                            ],
                        'sample2' =>
                            [
                                'label' => 'sample2',
                                'index' => 2,
                            ],
                        'sample3' =>
                            [
                                'label' => 'sample3',
                                'index' => 1,
                            ],
                    ],
                'align' => 'HORIZONTAL',
                'required' => false,
                'type' => 'CHECK_BOX',
            ],
        'date' =>
            [
                'code' => 'date',
                'defaultNowValue' => true,
                'defaultValue' => '',
                'label' => 'Date',
                'noLabel' => false,
                'required' => false,
                'type' => 'DATE',
                'unique' => false,
            ],
        'datetime' =>
            [
                'code' => 'datetime',
                'defaultNowValue' => false,
                'defaultValue' => '2012-07-19T00:00:00.000Z',
                'label' => 'Datetime',
                'noLabel' => false,
                'required' => false,
                'type' => 'DATETIME',
                'unique' => false,
            ],
        'attachment_file' =>
            [
                'code' => 'attachment_file',
                'label' => 'Attachment file',
                'noLabel' => true,
                'required' => false,
                'type' => 'FILE',
                'thumbnailSize' => '150',
            ],
        'link' =>
            [
                'code' => 'link',
                'defaultValue' => 'https://kintoneapp.com',
                'maxLength' => 64,
                'minLength' => 0,
                'label' => 'Link',
                'noLabel' => true,
                'protocol' => 'WEB',
                'required' => false,
                'type' => 'LINK',
                'unique' => false,
            ],
        'table' =>
            [
                'code' => 'table',
                'type' => 'SUBTABLE',
                'fields' =>
                    [
                        'single_text_in_table' =>
                            [
                                'code' => 'single_text_in_table',
                                'defaultValue' => '',
                                'expression' => '',
                                'hideExpression' => false,
                                'maxLength' => 64,
                                'minLength' => 0,
                                'label' => 'Single text in table',
                                'noLabel' => false,
                                'required' => true,
                                'type' => 'SINGLE_LINE_TEXT',
                                'unique' => false,
                            ],
                    ]
            ]
    ];

    /**
     * @var array
     */
    private static $layout = [
        [
            'type' => 'ROW',
            'fields' => [
                [
                    'code' => 'single_text',
                    'type' => 'SINGLE_LINE_TEXT',
                    'size' => ['width' => 200]
                ],
                [
                    'code' => 'number',
                    'type' => 'NUMBER',
                    'size' => ['width' => 100]
                ],
                [
                    'code' => 'radio_button',
                    'type' => 'RADIO_BUTTON',
                    'size' => ['width' => 200]
                ],
                [
                    'code' => 'checkbox',
                    'type' => 'CHECK_BOX',
                    'size' => ['width' => 200]
                ],
                [
                    'code' => 'date',
                    'type' => 'DATE',
                    'size' => ['width' => 100]
                ],
                [
                    'code' => 'datetime',
                    'type' => 'DATETIME',
                    'size' => ['width' => 200]
                ],
                [
                    'code' => 'attachment_file',
                    'type' => 'FILE',
                    'size' => ['width' => 100]
                ],
                [
                    'code' => 'link',
                    'type' => 'LINK',
                    'size' => ['width' => 200]
                ]
            ]
        ],
        [
            'type' => 'SUBTABLE',
            'code' => 'table',
            'fields' => [[
                'code' => 'single_text_in_table',
                'type' => 'SINGLE_LINE_TEXT',
                'size' => ['width' => 500]
            ]]
        ]
    ];

    /**
     * @var array
     */
    private static $views = [
        'test index' => [
            'type' => 'LIST',
            'name' => 'test index',
            'fields' =>['single_text', 'number', 'date', 'datetime'],
            'filterCond' => 'datetime < NOW()',
            'sort' => 'single_text asc',
            'index' => 0,
        ],
        'test calendar' => [
            'type' => 'CALENDAR',
            'name' => 'test calendar',
            'date' => 'date',
            'title' => 'single_text',
            'filterCond' => '',
            'sort' => 'number asc',
            'index' => 1,
        ],
        'test custom view' => [
            'type' => 'CUSTOM',
            'name' => 'test custom view',
            'html' => '<p>test custom view html code</p>',
            'filterCond' => '',
            'sort' => 'date asc',
            'index' => 2,
        ]
    ];

    /**
     * @var array
     */
    private static $appAcl = [
        [
            'entity' => [
                'code' => null,
                'type' => 'CREATOR'
            ],
            "includeSubs" => false,
            "appEditable" => true,
            "recordViewable" => true,
            "recordAddable" => true,
            "recordEditable" => true,
            "recordDeletable" => true,
            "recordImportable" => false,
            "recordExportable" => false
        ],
        [
            'entity' => [
                "type" => 'GROUP',
                "code" => 'Administrators'
            ],
            "includeSubs" => false,
            "appEditable" => true,
            "recordViewable" => true,
            "recordAddable" => true,
            "recordEditable" => false,
            "recordDeletable" => false,
            "recordImportable" => false,
            "recordExportable" => false
        ]
    ];

    /**
     * @var array
     */
    private static $recordAcl = [[
        'filterCond' => '',
        'entities' => [[
            'entity' => [
                "type" => 'GROUP',
                "code" => 'Administrators'
            ],
            "viewable" => true,
            "editable" => false,
            "deletable" => false,
            "includeSubs" => false
        ]]
    ]];

    /**
     * @var array
     */
    private static $fieldAcl = [
        [
            "code" => "number",
            "entities" => [[
                'entity' => [
                    "type" => 'GROUP',
                    "code" => 'Administrators'
                ],
                "accessibility" => "READ",
                "includeSubs" => false
            ]]
        ],
        [
        "code" => "date",
        "entities" => [[
            'entity' => [
                "type" => 'GROUP',
                "code" => 'Administrators'
            ],
            "accessibility" => "WRITE",
            "includeSubs" => false
        ]]
    ]
    ];

    /**
     * @var array
     */
    private static $record = [
        'single_text' => ['value' => 'single_text value'],
        'number' => ['value' => '10'],
        'radio_button' => ['value' => 'sample1'],
        'checkbox' => ['value' => ['sample1', 'sample2']],
        'date' => ['value' => '2015-09-28'],
        'datetime' => ['value' => '2015-09-28T00:00:00Z'],
        'attachment_file' => ['value' => []],
        'link' => ['value' => 'https://kintoneapp.com'],
        'table' => [
            'value' => [
                [
                    'value' => [
                        'single_text_in_table' => [
                            'value' => 'single_text_in_table value1'
                        ]
                    ]
                ],
                [
                    'value' => [
                        'single_text_in_table' => [
                            'value' => 'single_text_in_table value2'
                        ]
                    ]
                ],
            ]
        ]
    ];

    public static function getConfig()
    {
        return self::$config;
    }

    public static function getSpaceTemplateId()
    {
        return self::$spaceTemplateId;
    }

    public static function getGraph()
    {
        return self::$graph;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    public static function getLayout()
    {
        return self::$layout;
    }

    public static function getViews()
    {
        return self::$views;
    }

    public static function getAppAcl()
    {
        return self::$appAcl;
    }

    public static function getRecordAcl()
    {
        return self::$recordAcl;
    }

    public static function getFieldAcl()
    {
        return self::$fieldAcl;
    }

    public static function getRecord()
    {
        return self::$record;
    }

    public static function createKintoneApi()
    {
        $yml = Yaml::parse(__DIR__ . '/../../parameters.yml');
        $config = $yml['parameters'];
        $config['debug'] = true;
        $config['logfile'] = __DIR__ . '/../_output/connection.log';
        if ($config['use_basic'] and $config['use_client_cert']) {
            $config['use_client_cert'] = false;
        }
        self::$config = $config;
        self::$spaceTemplateId = $yml['space']['templateId'];
        self::$graph = $yml['graph'];

        self::$api = new KintoneApi(Client::factory($config));

        return self::$api;
    }

    public static function getKintoneApi()
    {
        if (self::$api) {
            return self::$api;
        }

        return self::createKintoneApi();
    }

    public static function createTestSpace($isGuest = false)
    {
        $api = self::getKintoneApi();
        $members = [[
            "entity" => [
                "type" => "USER",
                "code" => self::$config['login']
            ],
            "isAdmin" => true
        ]];
        $resp = $api->space()->post(
            self::$spaceTemplateId,
            'cybozu-http test space',
            $members,
            true,
            $isGuest
        );

        return $resp['id'];
    }

    /**
     * @param integer $spaceId
     * @param integer $threadId
     * @param integer $guestSpaceId
     * @return integer
     */
    public static function createTestApp($spaceId, $threadId, $guestSpaceId = null)
    {
        $api = self::getKintoneApi();
        $resp = $api->preview()->post('cybozu-http test app', $spaceId, $threadId, $guestSpaceId);
        $id = $resp['app'];

        $api->preview()->putSettings($id,
            'cybozu-http test app',
            'cybozu-http test app',
            ['type' => 'PRESET', 'key' => 'APP72'],
            'WHITE',
            $guestSpaceId
        );
        $api->preview()->postFields($id, self::$fields, $guestSpaceId);
        $api->preview()->putLayout($id, self::$layout, $guestSpaceId);
        $api->preview()->putViews($id, self::$views, $guestSpaceId);
        $api->preview()->deploy($id, $guestSpaceId);
        while (1) {
            if ('PROCESSING' != $api->preview()->getDeployStatus($id, $guestSpaceId)['status']) {
                break;
            }
        }

        return $id;
    }

    /**
     * @param integer $appId
     * @param integer $guestSpaceId
     * @return integer
     */
    public static function postTestRecord($appId, $guestSpaceId = null)
    {
        $api = self::getKintoneApi();
        return $api->record()->post($appId, self::$record, $guestSpaceId)['id'];
    }
}