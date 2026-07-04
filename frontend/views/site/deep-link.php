<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = Yii::$app->name;
?>
    <div class="site-error">

        <h1><?php echo Html::encode($this->title) ?></h1>

        <p>
            <?php echo Yii::t('frontend', 'Hello. Now you will be redirected in application. Thank you.') ?>
        </p>
        <p>
            <?php echo Yii::t('frontend', 'If you are not redirected automatically, please click on the badges below.') ?>
        </p>
        <div class="row" style="text-align: center; margin-top: 40px;">
            <div class="col-sm-2"></div>
            <div class="col-sm-4">
                <a href="<?= Yii::$app->keyStorage->get('frontend.apple.appstore.link', null) ?>" style="margin-bottom: 20px; display: inline-block">
                    <img src="/img/appstore.png" style="width: 60%">
                </a>
            </div>
            <div class="col-sm-4">
                <a href="https://play.google.com/store/apps/details?id=<?= Yii::$app->keyStorage->get('frontend.play.market.id', null) ?>" style="margin-bottom: 20px; display: inline-block">
                    <img src="/img/googleplay.png" style="width: 60%">
                </a>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/mobile-detect@1.4.3/mobile-detect.min.js"></script>
    <script type="text/javascript">
        (function() {
            <?php //if ($version == "iOS"): ?>
            var app = {
                launchApp: function() {
                    window.location.replace("<?= $link ?>");
                    //this.timer = setTimeout(this.openWebApp, 2000);
                },

                openWebApp: function() {
                    window.location.replace("<?= Yii::$app->keyStorage->get('frontend.apple.appstore.link', null) ?>");
                }
            };

            //app.launchApp();
            <?php //else: ?>

            var appAndroid = {
                launchApp: function() {
                    window.location.replace("<?= $link ?>");
                    //var iframe = document.createElement("iframe");
                    //iframe.style.border = "none";
                    //iframe.style.width = "1px";
                    //iframe.style.height = "1px";
                    //iframe.src = '<?//= $link ?>//';
                    //document.body.appendChild(iframe);

                    // this.timer = setTimeout(this.openWebApp, 2000);
                },

                openWebApp: function() {
                    window.location.replace("market://details?id=<?= Yii::$app->keyStorage->get('frontend.play.market.id', null) ?>");
                }
            };
            //appAndroid.launchApp();
            <?php //endif ?>

            var md = new MobileDetect(window.navigator.userAgent);

            if (md.is('iPhone')) {
                app.launchApp()
            } else {
                appAndroid.launchApp()
            }

        })();
    </script>

<?php if (strpos($link, "user/friend/") != false) {
    $id = explode("user/friend/", $link);

    if (isset($id[1]) && (int)$id[1])
    {
        $id = $id[1];

        if ($user = \common\models\User::find()->where([
            'id' => $id
        ])->with([
            'userProfile'
        ])->one()) {
            $this->registerMetaTag(
                [
                    'property' => 'og:title',
                    'content' => Yii::t("frontend", "OodyMate Friend Request from {user}", [
                        'user' => $user->userProfile->firstname
                    ])
                ]
            );

            $this->registerMetaTag(
                [
                    'property' => 'og:type',
                    'content' => "article"
                ]
            );

            $this->registerMetaTag(
                [
                    'property' => 'og:url',
                    'content' => \yii\helpers\Url::to(['site/deep-link', 'link' => $link], true)
                ]
            );

//            if ($path = $user->userProfile->getAvatar()) {
//                $this->registerMetaTag([
//                    'property' => 'og:image',
//                    'content' =>
//                        Url::to([
//                        '/file/thumb', 'source' => $path,
//                        'width' => 1024,
//                        'height' => 1024,
//                        'crop' => true
//                    ], true)
//                ]);
//            }

            $this->registerMetaTag([
                'property' => 'og:image',
                'content' => Url::to("/img/logo.png", true)
            ]);


            $this->registerMetaTag(
                [
                    'property' => 'og:site_name',
                    'content' => Yii::$app->name
                ]
            );

            $this->registerMetaTag(
                [
                    'property' => 'og:description',
                    'content' => Yii::t("frontend", "Check out {app} - a great free app to easily meet old and new friends over a meal, while  traveling or at home! My nick name there is {user}", [
                        'app' => Yii::$app->name,
                        'user' => $user->userProfile->firstname
                    ])
                ]
            );
        }
    }

} ?>