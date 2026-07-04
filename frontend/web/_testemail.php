<?php

error_reporting(E_ALL & ~E_NOTICE);

// Composer
require(__DIR__ . '/../../vendor/autoload.php');

// Environment
require(__DIR__ . '/../../common/env.php');

// Yii
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

// Bootstrap application
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/base.php'),
    require(__DIR__ . '/../../common/config/web.php'),
    require(__DIR__ . '/../config/base.php'),
    require(__DIR__ . '/../config/web.php')
);

(new yii\web\Application($config));

//echo getenv('ADMIN_EMAIL'); exit;

echo "Admin email: ".Yii::$app->params['adminEmail'].'<br>';
var_dump([Yii::$app->params['adminEmail'] => Yii::$app->name]);
echo '<br>';
echo "To email: ".$_GET['email'].'<br>';


if (Yii::$app
    ->mailer
    ->compose()
    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
    ->setTo($_GET['email'])
    ->setSubject('There is new test')
    ->setTextBody('Текст сообщения')
    ->setHtmlBody('<b>текст сообщения в формате HTML</b>')
    ->send()) {
    echo 'Result: success';
} else {
    echo 'Result: error';
}

//if (Yii::$app->commandBus->handle(new SendEmailCommand([
//    'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
//    'to' => $this->email,
//    'subject' => Yii::t('frontend', 'Password reset for {name}', ['name'=>Yii::$app->name]),
//    'view' => $params['view']?$params['view']:'passwordResetToken',
//    'body' => $params['body']?$params['body']:null,
//    'params' => ['user' => $user]
//]))) {
//    echo 'success';
//} else {
//    echo 'error';
//}

//if (mail("alexandrogreen@gmail.com", "test", "test", "From: ".getenv('ADMIN_EMAIL')." \r\n")) {
//    echo 'success';
//} else {
//    echo 'error';
//}