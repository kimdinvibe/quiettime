<?php

/**
 * Eugine Terentev <eugine@terentev.net>
 */

namespace console\controllers;

use common\models\Article;
use Yii;
use common\models\Message;
use common\models\MessageLog;
use common\models\MessageRoom;
use common\models\MessageRoomAccess;
use common\models\MessageRoomApns;
use common\models\MessageView;
use common\models\Price;
use common\models\PriceField;
use common\models\User;
use common\models\UserDevice;
use common\models\UserPayment;
use common\models\UserProfile;
use paragraph1\phpFCM\Recipient\Device;
use yii\console\Controller;
use yii\helpers\Console;

use yii\db\Query;

require_once Yii::$app->basePath . '/../lib/yookassa/lib/autoload.php';

use YooKassa\Client;

/**
 * Class ExtendedMessageController
 * @package console\controllers
 */
class PaymentController extends Controller
{
    public function actionCheckSubscription()
    {
        $today = strtotime("today");
        $tomorrow   = strtotime("tomorrow");



        if ($items = (new Query
        )->select(
            'up.*'
        )->from(UserProfile::tableName() . ' up')
            ->where([
                'and',
                ['is not', 'up.premium_period', null],
                ['is not', 'up.premium_price', null],
                ['!=', 'up.premium_period', 0],
                ['!=', 'up.premium_price', 0],
                ['>=', 'up.premium_at', $today],
                ['<', 'up.premium_at', $tomorrow],
            ])
            ->all()
        ) {
            foreach ($items as $item) {
                print("------\n");
                print("User Id: ".$item['user_id']."\n");

                $isSuccess = false;
                
                if ($userPayment =  UserPayment::find()->where(['and',
                    ['user_id' => $item['user_id']],
                    ['status' => UserPayment::STATUS_SUCCESSED],
                    ['is not', 'payment_id', null],
                ])->orderBy(['id' => SORT_ASC])->asArray()->one()) {

                    $model = new UserPayment([
                        'amount' => $item["premium_price"],
                        'period' => $item["premium_period"],
                        'currency' => "RUB",
                        'user_id' => $item['user_id'],
                        'status' => UserPayment::STATUS_RECURRENT,
                    ]);

                    if ($model->save()) {
                        $this->actionSendNewPayment(
                            $item["premium_price"], 
                            $userPayment['payment_id'], 
                            $model->auth_token);
                        $isSuccess = true;
                    }
                }

                print($isSuccess ? "success": "error");
            }
        }
    }

    public function actionSendNewPayment($amount, $paymentId, $transactionId)
    {
        echo "Amount: $amount\n";
        echo "PaymentId: $paymentId\n";
        echo "TransactionId: $transactionId\n";

        $client = new Client();
        $client->setAuth(Yii::$app->keyStorage->get('yookassa.shop.id', null), Yii::$app->keyStorage->get('yookassa.secret.key', null));

        try {
            $response = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $amount,
                        'currency' => 'RUB',
                    ),
                    'capture' => true,
                    'payment_method_id' => $paymentId,
                    'description' => 'Оплата подписки',
                    'metadata' => array(
                        'cms_name'       => 'Quite Time',
                        // 'order_id'       => $model->id,
                        'language'       => 'ru',
                        'transaction_id' => $transactionId,
                    ),
                ),
                uniqid('', true)
            );

            if ($response->id) {
                var_dump("success", $response->id);
            }
        } catch (\Exception $e) {
            // $response = $e;
            var_dump($e);
        }
    }
}
