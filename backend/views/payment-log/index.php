<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PaymentLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Payment Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-log-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Payment Log',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'event',
            'object_id',
            'amount',
            'currency',
            // 'object',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
