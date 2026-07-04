<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = null;

if ($app_link) {
    $link = Yii::$app->urlManager->createAbsoluteUrl(['/deep-link', 'link' => $app_link]);
}

?>

Hello <?php echo Html::encode($user->userProfile && $user->userProfile->firstname ? $user->userProfile->firstname : $user->username) ?>,<br/>
<br/>
<?= $message ?><br/>
<br/>

<?php if($link): ?>
    <?php echo Html::a(Html::encode($link), $link) ?><br>
    <br/>
    *This URL link will be available for 24 hours.<br/>
    <br/>
<?php endif; ?>

*If you don't want to receive email notifications, please go to the settings inside OodyMate app to make changes under "Notifications".
