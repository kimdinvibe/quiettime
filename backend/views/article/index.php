<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Article',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            // 'image_path',
            // 'image_base_url:url',
            // 'text:ntext',
            // 'content',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'enum' => common\models\Article::getNameStatus(),
                'filter' => common\models\Article::getNameStatus(),
                'value' => function($model){
                    return $model->status?common\models\Article::getNameStatus($model->status):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],
            [
                'class' => \common\grid\EnumColumn::className(),
//                'enum' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
//                    'username' => SORT_ASC
//                ])->asArray()->all(), 'id', 'username'),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
                    'username' => SORT_ASC
                ])->asArray()->all(), 'id', 'username'),
                'attribute' => 'author_id',
                'value' => function($model){
                    return $model->author?Html::a($model->author->username, ['user/view', 'id' => $model->author->id]):null;
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
