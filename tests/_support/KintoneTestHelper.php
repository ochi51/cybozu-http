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

    /**
     * @var array
     */
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
            'includeSubs' => false,
            'appEditable' => true,
            'recordViewable' => true,
            'recordAddable' => true,
            'recordEditable' => true,
            'recordDeletable' => true,
            'recordImportable' => false,
            'recordExportable' => false
        ],
        [
            'entity' => [
                'type' => 'GROUP',
                'code' => 'Administrators'
            ],
            'includeSubs' => false,
            'appEditable' => true,
            'recordViewable' => true,
            'recordAddable' => true,
            'recordEditable' => false,
            'recordDeletable' => false,
            'recordImportable' => false,
            'recordExportable' => false
        ]
    ];

    /**
     * @var array
     */
    private static $recordAcl = [[
        'filterCond' => '',
        'entities' => [[
            'entity' => [
                'type' => 'GROUP',
                'code' => 'Administrators'
            ],
            'viewable' => true,
            'editable' => false,
            'deletable' => false,
            'includeSubs' => false
        ]]
    ]];

    /**
     * @var array
     */
    private static $fieldAcl = [
        [
            'code' => 'number',
            'entities' => [[
                'entity' => [
                    'type' => 'GROUP',
                    'code' => 'Administrators'
                ],
                'accessibility' => 'READ',
                'includeSubs' => false
            ]]
        ],
        [
        'code' => 'date',
        'entities' => [[
            'entity' => [
                'type' => 'GROUP',
                'code' => 'Administrators'
            ],
            'accessibility' => 'WRITE',
            'includeSubs' => false
        ]]
    ]
    ];

    /**
     * @var array
     */
    private static $states = [
        'test1' => [
            'name' => 'test1',
            'index' => 0,
            'assignee' => [
                'type' => 'ONE',
                'entities' => []
            ]
        ],
        'test2' => [
            'name' => 'test2',
            'index' => 1,
            'assignee' => [
                'type' => 'ONE',
                'entities' => [
                    [
                        'entity' => [
                            'type' => 'USER',
                            'code' => ''
                        ]
                    ]
                ]
            ]
        ],
        'test3' => [
            'name' => 'test3',
            'index' => 2,
            'assignee' => [
                'type' => 'ONE',
                'entities' => []
            ]
        ]
    ];

    /**
     * @var array
     */
    private static $actions = [
        [
            'name' => 'sample',
            'from' => 'test1',
            'to' => 'test2',
            'filterCond' => ''
        ],
        [
            'name' => 'end',
            'from' => 'test2',
            'to' => 'test3',
            'filterCond' => ''
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

    /**
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * @return int
     */
    public static function getSpaceTemplateId(): int
    {
        return self::$spaceTemplateId;
    }

    /**
     * @return array
     */
    public static function getGraph(): array
    {
        return self::$graph;
    }

    /**
     * @return array
     */
    public static function getFields(): array
    {
        return self::$fields;
    }

    /**
     * @return array
     */
    public static function getLayout(): array
    {
        return self::$layout;
    }

    /**
     * @return array
     */
    public static function getViews(): array
    {
        return self::$views;
    }

    /**
     * @return array
     */
    public static function getAppAcl(): array
    {
        return self::$appAcl;
    }

    /**
     * @return array
     */
    public static function getRecordAcl(): array
    {
        return self::$recordAcl;
    }

    /**
     * @return array
     */
    public static function getFieldAcl(): array
    {
        return self::$fieldAcl;
    }

    /**
     * @return array
     */
    public static function getStates(): array
    {
        return self::$states;
    }

    /**
     * @return array
     */
    public static function getActions(): array
    {
        return self::$actions;
    }

    /**
     * @return array
     */
    public static function getRecord(): array
    {
        return self::$record;
    }

    /**
     * @return KintoneApi
     */
    public static function createKintoneApi(): KintoneApi
    {
        $yml = Yaml::parse(file_get_contents(__DIR__ . '/../../parameters.yml'));
        $config = $yml['parameters'];
        $config['debug'] = true;
        $config['logfile'] = __DIR__ . '/../_output/connection.log';
        if ($config['use_basic'] && $config['use_client_cert']) {
            $config['use_client_cert'] = false;
        }
        self::$config = $config;
        self::$spaceTemplateId = $yml['space']['templateId'];
        self::$graph = $yml['graph'];

        self::$api = new KintoneApi(new Client($config));

        return self::$api;
    }

    /**
     * @return KintoneApi
     */
    public static function getKintoneApi(): KintoneApi
    {
        if (self::$api) {
            return self::$api;
        }

        return self::createKintoneApi();
    }

    /**
     * @param bool $isGuest
     * @return int
     */
    public static function createTestSpace($isGuest = false): int
    {
        $api = self::getKintoneApi();
        $members = [[
            'entity' => [
                'type' => 'USER',
                'code' => self::$config['login']
            ],
            'isAdmin' => true
        ]];
        $resp = $api->space()->post(
            self::$spaceTemplateId,
            'cybozu-http test space',
            $members,
            true,
            $isGuest
        );

        return (int)$resp['id'];
    }

    /**
     * @param int $spaceId
     * @param int $threadId
     * @param int $guestSpaceId
     * @return int
     */
    public static function createTestApp($spaceId, $threadId, $guestSpaceId = null): int
    {
        $api = self::getKintoneApi();
        $resp = $api->preview()->post('cybozu-http test app', $spaceId, $threadId, $guestSpaceId);
        $id = (int)$resp['app'];

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
        self::$states['test2']['assignee']['entities'][0]['entity']['code'] = self::$config['login'];
        $api->preview()->putStatus($id, self::$states, self::$actions, true, $guestSpaceId);
        $api->preview()->deploy($id, $guestSpaceId);
        while (1) {
            if ('PROCESSING' !== $api->preview()->getDeployStatus($id, $guestSpaceId)['status']) {
                break;
            }
        }

        return $id;
    }

    /**
     * @param int $appId
     * @param int $guestSpaceId
     * @return int
     */
    public static function postTestRecord($appId, $guestSpaceId = null): int
    {
        $api = self::getKintoneApi();
        return $api->record()->post($appId, self::$record, $guestSpaceId)['id'];
    }
}
