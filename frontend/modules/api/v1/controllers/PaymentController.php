<?php

namespace frontend\modules\api\v1\controllers;

use Yii;
use YooKassa\Client;
use yii\helpers\Html;
use common\models\Note;
use common\models\Task;
use yii\web\HttpException;
use common\models\TaskUser;
use common\models\UserPayment;
use frontend\helpers\ApiHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\modules\api\v1\resources\UserPayment as UserPaymentResource;

require_once Yii::$app->basePath . '/../lib/yookassa/lib/autoload.php';

use frontend\components\ApiController;
use frontend\filters\auth\HttpBearerAuth;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class PaymentController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Task';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            // 'except' => ['view']
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET'],
            'view' => ['GET'],
            'count-new' => ['GET'],
            'user-view' => ['POST']
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }

    public function actionAddTransaction()
    {   
        // return ["url" => "https://yoomoney.ru/checkout/payments/v2/contract?orderId=29451946-000f-5000-9000-1fe3486c49e7"];


        $params = Yii::$app->request->getBodyParams();

        ApiHelper::checkRequiredFields([
            // 'notification_on' => Yii::t('api', 'notification_on'),
            'amount' => Yii::t('api', 'amount'),
            'currency' => Yii::t('api', 'amount'),
            'period' => Yii::t('api', 'period'),
        ], $params);

        $model = new UserPayment([
            'amount' => $params["amount"],
            'period' => $params["period"],
            'currency' => $params["currency"],
            'user_id' => Yii::$app->user->id,
        ]);


        if (!$model->save()) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }

        $model->refresh();
        
        $client = new Client();
        $client->setAuth(Yii::$app->keyStorage->get('yookassa.shop.id', null), Yii::$app->keyStorage->get('yookassa.secret.key', null));
        // $client->setAuth(Yii::$app->keyStorage->get(857243, null), Yii::$app->keyStorage->get('live_KoLyPlobp3m8VYruu7bUa-ymW7h_GP839fxFi4lSvZc', null));

        try {
            $builder = \YooKassa\Request\Payments\CreatePaymentRequest::builder();
            $builder->setAmount($model->amount)
                ->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
                ->setCapture(true)
                ->setDescription('Оплата подписки')
                ->setMetadata(array(
                    'cms_name'       => 'Quite Time',
                    // 'order_id'       => $model->id,
                    'language'       => 'ru',
                    'transaction_id' => $model->auth_token,
                ));

            // Устанавливаем страницу для редиректа после оплаты
            $builder->setConfirmation(array(
                'type'      => \YooKassa\Model\ConfirmationType::REDIRECT,
                'returnUrl' => Url::to(['/site/payment-success'], true),
            ));

            // Можем установить конкретный способ оплаты
            $builder->setPaymentMethodData(\YooKassa\Model\PaymentMethodType::BANK_CARD);
            $builder->setSavePaymentMethod(true);
            
            // Создаем объект запроса
            $request = $builder->build();
            
            // Можно изменить данные, если нужно
            // $request->setDescription($request->getDescription() . ' - merchant comment');

            $idempotenceKey = uniqid('', true);
            // return $request;
            $response = $client->createPayment($request, $idempotenceKey);

            //получаем confirmationUrl для дальнейшего редиректа
            $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
            
            $model->confirmation_url = $confirmationUrl;
            $model->status = UserPayment::STATUS_CREATED;
            $model->update();

            return [
                "url" => $confirmationUrl,
                "hash" => $model->auth_token,
            ];
        } catch (\Exception $e) {
            // $response = $e;
            throw new HttpException(404, Yii::t('api', $e));
        }

        $model->delete();
        throw new HttpException(404, Yii::t('api', $e));
    }

    public function actionCheckTransaction($hash)
    {   
        if ($model = UserPaymentResource::find()->where([
            'auth_token' => $hash,
        ])->one()) {
            return $model;
        }
        
        throw new HttpException(404, Yii::t('api', 'Not found'));
    }

    // /**
    //  * @param $id
    //  * @return null|static
    //  * @throws NotFoundHttpException
    //  */
    // public function findModel($id)
    // {
    //     $model = TaskResource::find()
    //         // ->with(['user'])
    //         // ->where(['id' => $id, 'user_id' => Yii::$app->user->id,])
    //         ->where(['id' => $id])
    //         // ->active()
    //         ->one();

    //     if (!$model) {
    //         throw new HttpException(404, Yii::t('api', 'Not found'));
    //     }
    //     return $model;
    // }
}
