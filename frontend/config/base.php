<?php
return [
    'id' => 'frontend',
    'basePath' => dirname(__DIR__),
    'components' => [
        'urlManager' => require(__DIR__.'/_urlManager.php'),
        /*'urlManager'=>[
            'scriptUrl'=>'/index.php',
        ],*/        
        'cache' => require(__DIR__.'/_cache.php'),
    ],
    'params' => require dirname(__FILE__).'/params.php',
];
