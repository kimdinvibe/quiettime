<?php
return [
    'components' => [
        'apns' => [
            'class' => 'bryglen\apnsgcm\Apns',
            'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_PRODUCTION,
//            'pemFile' => dirname(__FILE__).'/apnssert/app_develop.pem',
            'pemFile' => dirname(__FILE__).'/apnssert/aps_prod.pem',
            // 'retryTimes' => 3,
            'options' => [
                'sendRetryTimes' => 5,
                //'providerCertificatePassphrase' => 'your_password_here'
            ],
            'enableLogging' => true
        ],
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AAAAO7cmq0s:APA91bEjIlFFhHR_jeR0LU0KUcae3r7MkVkVwSGYrxUcv5khR8AVtm63ruDqemoZ2M7iC-l2Uztu62ahfI7EpsnfHN_bphJ1SFmjt1IEE63KQi1c6VrRAQZ8Xxifwn4H7v2i3PrjDlFr', // Server API Key (you can get it here: https://firebase.google.com/docs/server/setup#prerequisites)
        ],
    ]
];
