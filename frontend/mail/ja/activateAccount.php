<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/activate', 'token' => $user->auth_key, 'email' => $user->email]);
?>

<?php echo Html::encode($user->userProfile && $user->userProfile->firstname ? $user->userProfile->firstname : $user->email) ?>さん、こんにちは<br/>
<br/>
ご登録していただいたメールアドレスは、下記のURLリンクをクリックしていただくことでご登録完了となります。<br/>
<br/>
<?php echo Html::a(Html::encode($link), $link) ?><br/>
<br/>
＊このURLリンクは24時間が経過すると無効となりますのでご注意ください。
