<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\ResetPasswordForm */

$this->title = Yii::t('frontend', 'Activate account');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $link = Yii::$app->keyStorage->get('frontend.apple.deeplink.id', null)."://user/activate/".$model->access_token ?>
<div class="site-reset-password">
    <div class="row">
        <div class="col-lg-12">
            <?php echo Yii::t("frontend", "You can now sign in with your new account, or click {reference} to log in from your smartphone.", [
                'reference' => Html::a(Yii::t("frontend", "here"), $link)
            ]) ?><br>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function() {
        var app = {
            launchApp: function() {
                window.location.replace("<?= $link ?>");
                this.timer = setTimeout(this.openWebApp, 1000);
            },

            openWebApp: function() {
                window.location.replace("<?= Yii::$app->keyStorage->get('frontend.apple.appstore.link', null) ?>");
            }
        };

        var appAndroid = {
            launchApp: function() {
                window.location.replace("<?= $link ?>");
                this.timer = setTimeout(this.openWebApp, 1000);
            },

            openWebApp: function() {
                window.location.replace("market://details?id=<?= Yii::$app->keyStorage->get('frontend.play.market.id', null) ?>");
            }
        };

        if(navigator.userAgent.match(/iPhone/i) ) {
            app.launchApp();
        } else if(navigator.userAgent.match(/Android/i) ) {
            appAndroid.launchApp();
        }
    })();
</script>