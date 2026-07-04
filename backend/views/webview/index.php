<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WebviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Webviews');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webview-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Webview',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'code',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'enum' => \common\models\Webview::getStateLabel(),
                'filter' => \common\models\Webview::getStateLabel(),
                'value' => function($model){
                    return $model->status?\common\models\Webview::getStateLabel($model->status):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],

            [
                'attribute' => 'updated_at',
                'filter' => false,
                'value' => function($model){
                    return $model->updated_at?Yii::$app->formatter->asDatetime($model->updated_at):null;
                },
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],
            [
                'attribute' => 'created_at',
                'filter' => false,
                'value' => function($model){
                    return $model->created_at?Yii::$app->formatter->asDatetime($model->created_at):null;
                },
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
