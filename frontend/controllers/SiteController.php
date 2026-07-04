<?php

namespace frontend\controllers;

use Yii;
use yii\db\Query;
use common\models\User;
use yii\web\Controller;
use yii\helpers\BaseUrl;
use yii\helpers\VarDumper;
use frontend\helpers\SMSRU;
use common\models\UserLocation;
use frontend\helpers\SmsHelper;

use frontend\models\ContactForm;
use yii\web\NotFoundHttpException;
use common\models\RbacAuthAssignment;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'set-locale' => [
                'class' => 'common\actions\SetLocaleAction',
                'locales' => array_keys(Yii::$app->params['availableLocales'])
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['/user/default/index']);
    }

    public function actionAppleAppSiteAssociation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pkcs7-mime');

        $this->layout = "_empty";

        return json_encode([
            'applinks' => [
                'apps' => [],
                'details' => [
                    [
                        'appID' => Yii::$app->keyStorage->get('frontend.apple.application.id', Yii::$app->params['appID']),
                        'paths' => [
                            '*'
                        ]
                    ]
                ]
            ]
        ]);


        //        Yii::$app->end();

        /*return [
            'applinks' => [
                'apps' => [],
                'details' => [
                    'appID' => Yii::$app->params['appID'],
                    'paths' => [
                        '*'
                    ]
                ]
            ]
        ];*/
    }

    public function actionDeepLink($link, $v = "iOS", $locale = "en")
    {
        if ($locale) {
            $locale = explode("-", $locale);
            $locale = $locale[0];

            if ($locale) {
                Yii::$app->language = $locale;
            }
        }

        return $this->render('deep-link', [
            'link' => $link,
            'version' => $v
        ]);
    }

    public function actionPaymentSuccess()
    {
        return $this->render('payment-success', [
            
        ]);
    }
}
