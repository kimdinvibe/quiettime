<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/reset-email', 'token' => $user->email_reset_token]);
?>

Здравствуйте!<br/>
Для восстановления email, проследуйте по ссылке<br/>
<br/>
<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
