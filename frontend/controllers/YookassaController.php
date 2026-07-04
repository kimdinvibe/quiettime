<?php

namespace frontend\controllers;

use common\models\PaymentLog;
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
use common\models\UserPayment;

/**
 * Site controller
 */
class YookassaController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->enableCsrfValidation = false;

            return true;
        }

        return false;
    }


    public function actionCallback()
    {
        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        try {
            $model =  new PaymentLog([
                'object' => $source,
            ]);

            if (isset($requestBody['type'])) {
                $model->type = $requestBody['type'];
            }

            if (isset($requestBody['event'])) {
                $model->event = $requestBody['event'];
            }

            if (isset($requestBody['object'])) {
                if (isset($requestBody['object']['id'])) {
                    $model->object_id = $requestBody['object']['id'];
                }

                if (isset($requestBody['object']['status'])) {
                    $model->status = $requestBody['object']['status'];
                }

                if (isset($requestBody['object']['amount'])) {
                    if (isset($requestBody['object']['amount']['value'])) {
                        $model->amount = $requestBody['object']['amount']['value'];
                    }

                    if (isset($requestBody['object']['amount']['currency'])) {
                        $model->currency = $requestBody['object']['amount']['currency'];
                    }
                }
            }

            if ($model->save()) {
                
                if (isset($requestBody['event']) &&  $requestBody["event"] == "payment.succeeded") {
                    if (isset($requestBody['object']['metadata']['transaction_id']) && $requestBody['object']['metadata']['transaction_id']) {
                        if ($userPayment = UserPayment::find()->where([
                            'auth_token' => $requestBody['object']['metadata']['transaction_id']
                        ])->one()) {
                            $userPayment->status = UserPayment::STATUS_SUCCESSED;
                            $userPayment->payment_log_id = $model->id;
                            
                            if (isset($requestBody['object']['payment_method']['id']) && $requestBody['object']['payment_method']['id'] && $requestBody['object']['payment_method']['saved']) {
                                $userPayment->payment_id = $requestBody['object']['payment_method']['id'];
                            }

                            if(!$userPayment->save()) {
                                return ["reponse" => "error", "errors" => $userPayment->errors];
                            } else {
                                $userPayment->user->userProfile->premium_at = strtotime("+ ".$userPayment->period." month");
                                $userPayment->user->userProfile->premium_period = $userPayment->period;
                                $userPayment->user->userProfile->premium_price = $userPayment->amount;
                                $userPayment->user->userProfile->update(false, ['premium_at', 'premium_period', 'premium_price']);
                            }
                        }
                    }
                } elseif (isset($requestBody['event']) &&  $requestBody["event"] == "payment.canceled") {
                    // turn on in pod
                    
                    // if (isset($requestBody['object']['metadata']['transaction_id']) && $requestBody['object']['metadata']['transaction_id']) {
                    //     if ($userPayment = UserPayment::find()->where([
                    //         'auth_token' => $requestBody['object']['metadata']['transaction_id']
                    //     ])->one()) {
                    //         $userPayment->status = UserPayment::STATUS_CANCELED;
                    //         $userPayment->payment_log_id = $model->id;
                    //         $userPayment->payment_id = null;
                            
                    //         if(!$userPayment->save()) {
                    //             return ["reponse" => "error", "errors" => $userPayment->errors];
                    //         } else {
                    //             $userPayment->user->userProfile->premium_at = null;
                    //             $userPayment->user->userProfile->premium_period = 0;
                    //             $userPayment->user->userProfile->update(false, ['premium_at', 'premium_period']);
                    //         }
                    //     }
                    // }
                }

                return ["reponse" => "success"];
            } else {
                return ["reponse" => "error", "errors" => $model->errors];
            }
        } catch (\Throwable $th) {
            return ["reponse" => "error", "exception" => $th];
        }
    }
}
