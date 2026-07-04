<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = null;

if ($app_link) {
    $link = Yii::$app->urlManager->createAbsoluteUrl(['/deep-link', 'link' => $app_link, 'locale' => $locale]);
}

?>

<?php echo Html::encode($user->userProfile && $user->userProfile->firstname ? $user->userProfile->firstname : $user->username) ?>さん、こんにちは。<br/>
<br/>
<?= $message ?><br/>
<br/>

<?php if($link): ?>
    <?php echo Html::a(Html::encode($link), $link) ?><br>
    <br/>
    ＊このURLは24時間が経過すると無効となりますのでご注意ください。<br/>
    <br/>
<?php endif; ?>

*メール通知を受信したくない場合は、アプリ内の設定から変更を行ってください。
