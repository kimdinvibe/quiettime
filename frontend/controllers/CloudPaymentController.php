<?php

namespace frontend\controllers;

use Yii;

use yii\web\Controller;
use common\models\UserOrder;
use common\models\UserOrderLog;
use common\helpers\SubscriptionHelper;
use common\models\CloudPayment;
use common\models\CloudPaymentNotification;

/**
 * Site controller
 */
class CloudPaymentController extends Controller
{
    public function beforeAction($action)
    {
        // if ($action->id == 'notification') {
        $this->enableCsrfValidation = false;
        // }

        return true;
    }

    public function actionIndex()
    {
        return $this->render('index', [
            // 'model' => $model,
            // 'isSuccess' => $isSuccess
            // 'result' => $result
        ]);
    }

    public function actionNotification($type = "Unknown")
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $notification = new CloudPayment([
            'type' => $type,
            'transaction_id' => $_POST['TransactionId'],
            'amount' => $_POST['Amount'],
            'currency' => $_POST['Currency'],
            'dateTime' => $_POST['DateTime'],
            'card_first_six' => $_POST['CardFirstSix'],
            'card_last_four' => $_POST['CardLastFour'],
            'card_type' => $_POST['CardType'],
            'card_exp_date' => $_POST['CardExpDate'],
            'test_mode' => $_POST['TestMode'],
            'status' => $_POST['Status'],
            'response' => $_POST[''],
            'operation_type' => $_POST['OperationType'],
            'invoice_id' => $_POST['InvoiceId'],
            'account_id' => $_POST['AccountId'],
            'subscription_id' => $_POST['SubscriptionId'],
            'id_recurent' => $_POST['Id'],
            'token' => $_POST['Token'],
        ]);

        $notification->response = json_encode($_POST);
        $notification->save(false);

        return [
            'code' => 0,
        ];
    }

    public function actionNotificationCheck()
    {
        // if (Yii::$app->request->post('InvoiceId')) {
        //     $order = UserOrder::findOne(Yii::$app->request->post('InvoiceId'));

        //     if ($order) {
        //         (new UserOrderLog([
        //             'action' => UserOrderLog::ACTION_CHECK,
        //             'order_id' => $order->id,
        //         ]))->save();

        //         if ($order->status == UserOrder::STATUS_CREATED) {
        //             $order->status = UserOrder::STATUSIN_IN_PROCESS;
        //             if ($order->update(true, ['status'])) {
        //                 $dataLog['isUpdateOrder'] = true;
        //             }
        //         }
        //     }
        // }

        return $this->actionNotification('Check');
    }

    public function actionNotificationPay()
    {

        // if (
        //     Yii::$app->request->post('InvoiceId') &&
        //     // Yii::$app->request->post('SubscriptionId') &&
        //     Yii::$app->request->post('AccountId')
        // ) {
        //     SubscriptionHelper::start(
        //         Yii::$app->request->post('InvoiceId'),
        //         Yii::$app->request->post('AccountId'),
        //         Yii::$app->request->post('Status')
        //     );
        // }


        return $this->actionNotification('Pay');
    }

    public function actionNotificationFail()
    {
        // if (
        //     Yii::$app->request->post('InvoiceId') &&
        //     // Yii::$app->request->post('SubscriptionId') &&
        //     Yii::$app->request->post('AccountId')
        // ) {

        //     SubscriptionHelper::fail(
        //         Yii::$app->request->post('InvoiceId'),
        //         Yii::$app->request->post('AccountId')
        //     );
        // }

        return $this->actionNotification('Fail');
    }

    public function actionNotificationConfirm()
    {
        return $this->actionNotification('Confirm');
    }

    public function actionNotificationRefund()
    {
        $this->cancelSubscribe();
        return $this->actionNotification('Refund');
    }

    public function actionNotificationReceipt()
    {
        return $this->actionNotification('Receipt');
    }

    public function actionNotificationRecurrent()
    {
        $this->cancelSubscribe();
        return $this->actionNotification('Recurrent');
    }

    public function actionNotificationCancel()
    {
        $this->cancelSubscribe();
        return $this->actionNotification('Cancel');
    }

    private function cancelSubscribe()
    {
        // if (
        //     Yii::$app->request->post('InvoiceId') &&
        //     // Yii::$app->request->post('SubscriptionId') &&
        //     Yii::$app->request->post('AccountId')
        // ) {
        //     SubscriptionHelper::cancel(
        //         Yii::$app->request->post('InvoiceId'),
        //         Yii::$app->request->post('AccountId'),
        //         Yii::$app->request->post('Status')
        //     );
        // }
    }
}
