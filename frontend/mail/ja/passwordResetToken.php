<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/reset-password', 'token' => $user->password_reset_token]);
?>

<?php echo Html::encode($user->userProfile && $user->userProfile->firstname ? $user->userProfile->firstname : $user->email) ?>さん、<br/>
<br/>
こんにちは。  下記のURLリンクからパスワードの設定を行えます。<br>
<br/>
<?php echo Html::a(Html::encode($resetLink), $resetLink) ?><br/>
