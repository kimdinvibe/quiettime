<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'User Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?php //echo Html::a(Yii::t('backend', 'Create {modelClass}', [
//    'modelClass' => 'User Log',
//]), ['create'], ['class' => 'btn btn-success']) ?>
<!--        --><?php //echo Html::a(Yii::t('backend', 'Clear', [
//            'modelClass' => 'User Log',
//        ]), ['clear'], ['class' => 'btn btn-danger', 'data-confirm' => Yii::t('backend', 'Are you sure you want to delete this item?')]) ?>
<!--    </p>-->

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//
//            'id',
            [
                'attribute' =>  'user_id',
                'format' => 'html',
                'value' => function($model) {
                    return $model->user?Html::a(Yii::t('backend', 'User').'_'.$model->id, ['user/view', 'id' => $model->user_id]):null;
                },
                //'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy(['email' => SORT_ASC])->all(), 'id', 'email')
            ],
            //'ip',
            [
                'attribute' =>  'controller',
                'format' => 'html',
                'value' => function($model) {
                    return $model->controller?$model->controller:null;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\UserLog::find()->groupBy(['controller'])->orderBy(['controller' => SORT_ASC])->all(), 'controller', 'controller')
            ],
            [
                'attribute' =>  'action',
                'format' => 'html',
                'value' => function($model) {
                    return $model->action?$model->action:null;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\UserLog::find()->groupBy(['action'])->orderBy(['controller' => SORT_ASC])->all(), 'action', 'action')
            ],
            //'device_id',
            [
                'attribute' =>  'device_name',
                'format' => 'html',
                'value' => function($model) {
                    return $model->device_name?$model->device_name:null;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\UserLog::find()->groupBy(['device_name'])->orderBy(['device_name' => SORT_ASC])->all(), 'device_name', 'device_name')
            ],
            [
                'attribute' =>  'device_type',
                'format' => 'html',
                'value' => function($model) {
                    return $model->device_type?$model->device_type:null;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\UserLog::find()->groupBy(['device_type'])->orderBy(['device_type' => SORT_ASC])->all(), 'device_type', 'device_type')
            ],

            // 'headers:ntext',
            // 'params:ntext',
            // 'created_at:datetime',
            [
                'attribute' =>  'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at);
                },
                'filter' => false
            ],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
