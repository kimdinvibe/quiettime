<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/activate', 'token' => $user->auth_key, 'email' => $user->email]);
?>

Здравствуйте!<br/>
Чтобы завершить настройку аккаунта и приступить к работе с приложением «Тихое время», подтвердите правильность указанного адреса электронной почты.<br/>
<br/>
Перейдите по ссылке<br/>
<?php echo Html::a(Html::encode($link), $link) ?>
