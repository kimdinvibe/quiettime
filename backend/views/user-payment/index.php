<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\YserPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'User Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!-- 
    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'User Payment',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'amount',
            'period',
            'currency',
            [
                'class' => \common\grid\EnumColumn::className(),
//                'enum' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
//                    'username' => SORT_ASC
//                ])->asArray()->all(), 'id', 'username'),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
                    'username' => SORT_ASC
                ])->asArray()->all(), 'id', 'username'),
                'attribute' => 'user_id',
                'value' => function($model){
                    return $model->user?Html::a($model->user->username, ['user/view', 'id' => $model->user->id]):null;
                },
                'format' => 'html'
            ],
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'enum' => common\models\UserPayment::getNameStatus(),
                'filter' => common\models\UserPayment::getNameStatus(),
                'value' => function($model){
                    return $model->status?common\models\UserPayment::getNameStatus($model->status):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],
            // 'auth_token',
            'payment_id',
            // [
            //     'class' => \common\grid\EnumColumn::className(),
            //      'filter' => \yii\helpers\ArrayHelper::map(\common\models\Payment::find()->orderBy([
            //         'title' => SORT_ASC
            //     ])->limit(1000)->asArray()->all(), 'id', 'title'),
            //     'attribute' => 'payment_id',
            //     'value' => function($model){
            //         return $model->payment?Html::a($model->payment->title, ['payment/view', 'id' => $model->payment->id]):null;
            //     },
            //     'format' => 'html'
            // ],
            [
                'class' => \common\grid\EnumColumn::className(),
                 'filter' => \yii\helpers\ArrayHelper::map(\common\models\PaymentLog::find()->orderBy([
                    'id' => SORT_ASC
                ])->limit(1000)->asArray()->all(), 'id', 'id'),
                'attribute' => 'payment_log_id',
                'value' => function($model){
                    return $model->paymentLog?Html::a("Item #".$model->paymentLog->id, ['payment-log/view', 'id' => $model->paymentLog->id]):null;
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'created_at',
                'filter' => false,
                'value' => function($model){
                    return $model->created_at?Yii::$app->formatter->asDatetime($model->created_at):null;
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'created_at',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],
            [
                'attribute' => 'updated_at',
                'filter' => false,
                'value' => function($model){
                    return $model->updated_at?Yii::$app->formatter->asDatetime($model->updated_at):null;
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'updated_at',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],
            // 'confirmation_url:url',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>

</div>
