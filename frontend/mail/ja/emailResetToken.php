<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/reset-email', 'token' => $user->email_reset_token]);
?>

メールアドレスの再発行申請を受け付けました。<br/>
以下のURLにアクセスして設定してください。<br/>
<br/>
<?php echo Html::a(Html::encode($resetLink), $resetLink) ?><br/>
<br/>
※URLの有効期間はメール送信後 24時間 です。