<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    //'enableStrictParsing' => true,
    'showScriptName'=>false,
    'rules'=> [
        ['pattern' => 'apple-app-site-association', 'route' => 'site/apple-app-site-association'],
        ['pattern' => 'deep-link', 'route' => 'site/deep-link'],
        ['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],

        // Site
        ['pattern'=>'/', 'route'=>'site/index'],
        ['pattern'=>'/contact', 'route'=>'site/contact'],

        // Pages
        ['pattern'=>'page/<slug>', 'route'=>'page/view'],

        // Webview
        ['pattern'=>'webview/<code>', 'route'=>'webview/view'],

        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article'],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/user'],
        /*[
            'class' => 'yii\rest\UrlRule',
            'controller' => 'api/v1/user',
            'pluralize' => false,
            'extraPatterns' => [
                'GET auth' => 'auth',
            ],
        ],*/
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/message-room'],
    ]
];
