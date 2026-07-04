<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DictionarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Dictionaries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Dictionary',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'category_id',
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\DictionaryCategory::find()->orderBy([
                    'title' => SORT_ASC
                ])->asArray()->all(), 'id', 'title'),
                'value' => function($model){
                    return $model->category?Html::a($model->category->title, ['dictionary-category/view', 'id' => $model->category->id]):null;
                },
                'format' => 'html'
            ],
            'title',
            'code',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'parent_id',
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Dictionary::find()->where([
                        // '!=', 'category_id', \common\models\DictionaryCategory::CATEGORY_CITY
                ])->orderBy([
                    'title' => SORT_ASC
                ])->asArray()->all(), 'id', 'title'),
                'value' => function($model){
                    return $model->parent?Html::a($model->parent->title, ['dictionary/view', 'id' => $model->parent->id]):null;
                },
                'format' => 'html'
            ],
//            [
//                'attribute' => 'data',
//                'value' => function($model){
//                    return $model->data ? json_encode($model->data) : '';
//                }
//            ],
            // 'slug',
            // 'code',
            // 'status',
            // 'order',
            // 'author_id',
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
