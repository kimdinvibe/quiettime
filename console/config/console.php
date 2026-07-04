<?php
return [
    'id' => 'console',
    //'language' => 'ja-JP',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'controllerMap'=>[
        'migrate'=>[
            'class'=>'yii\console\controllers\MigrateController',
            'migrationPath'=>'@common/migrations/db',
            'migrationTable'=>'{{%system_db_migration}}'
        ],
        'rbac-migrate'=>[
            'class'=>'console\controllers\RbacMigrateController',
            'migrationPath'=>'@common/migrations/rbac/',
            'migrationTable'=>'{{%system_rbac_migration}}',
            'templateFile' => '@common/rbac/views/migration.php'
        ],
        'article'=>[
            'class'=> 'console\controllers\ArticleController'
        ],
        'bible'=>[
            'class'=> 'console\controllers\BibleController'
        ],
        'payemnt'=>[
            'class'=> 'console\controllers\PaymentController'
        ],
        'notification'=>[
            'class'=> 'console\controllers\NotificationController'
        ],
    ],
    'components' => [
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl' => 'http://support.oodymate.jp', // Setup your domain
            'baseUrl' => 'http://support.oodymate.jp', // Setup your domain
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],

//        'user' => [
//            'class' => 'yii\web\User',
//            'identityClass' => 'app\models\User',
//            //'enableAutoLogin' => true,
//        ],
        'session' => [ // for use session in console application
            'class' => 'yii\web\Session'
        ],
    ],
];
