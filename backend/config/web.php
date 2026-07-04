<?php
$config = [
    // 'language' => 'ja-JP',
    'language' => 'ru-RU',
    'homeUrl'=>Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    //'defaultRoute'=>'timeline-event/index',
    'defaultRoute'=>'site/index',
    'controllerMap'=>[
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path'   => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager']
                ]
            ]
        ]
    ],
    'components'=>[
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => getenv('BACKEND_COOKIE_VALIDATION_KEY'),
            'enableCsrfValidation' => false,
            'baseUrl' => '/panel'
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
        'formatter' => [
            'dateFormat' => 'yyyy.MM.dd',
            'datetimeFormat' => 'yyyy.MM.dd HH:mm',
            'sizeFormatBase' => 1000,
            //'decimalSeparator' => ',',
            //'thousandSeparator' => ' ',
            //'currencyCode' => 'EUR',
        ],
    ],
    'modules'=>[
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
            'defaultRoute'=>'i18n-message/index'
        ],
    ],
    'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions'=>['login', 'create-admin-external']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions'=>['logout']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['agent'],
            ],
            [
                'controllers'=>['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions'=>['error']
            ],
            [
                'controllers'=>['file-storage'],
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'controllers'=>['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['user'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers'=>['user'],
                'allow' => false,
            ],
            [
                'controllers'=>['message-room'],
                'allow' => true,
                'roles' => ['agent'],
            ],
            [
                'controllers'=>['message-room-access'],
                'allow' => true,
                'roles' => ['agent'],
            ],
            [
                'controllers'=>['ajax'],
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'allow' => true,
                'roles' => ['manager'],
            ]
        ]
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class'=>'yii\gii\generators\crud\Generator',
                'templates'=>[
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates'),
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend'
            ],
            'crud-custom' => [
                'class'=>'backend\generators\crud\Generator',
                'templates'=>[
//                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates'),
                    'custom-default' =>  Yii::getAlias('@backend/generators/crud/default')
                ],
                'template' => 'custom-default',
                'messageCategory' => 'backend'
            ],
            'model-custom' => [
                'class'=>'backend\generators\model\Generator',
                'templates' => [
                    'custom-default' => Yii::getAlias('@backend/generators/model/default'),
                ],
                'template' => 'custom-default',
                'messageCategory' => 'backend'
            ],

        ]
    ];
}

return $config;
