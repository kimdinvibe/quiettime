<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BibleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Bibles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bible-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <!-- <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
            'modelClass' => 'Bible',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            // 'chapter',
            [
                'class' => \common\grid\EnumColumn::className(),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->groupBy('chapter')->orderBy([
                    'chapter' => SORT_ASC
                ])->asArray()->all(), 'chapter', 'chapter'),
                'attribute' => 'chapter',
                'value' => function ($model) {
                    return $model->chapter;
                },
                'format' => 'html'
            ],
            // 'verse',
            [
                'class' => \common\grid\EnumColumn::className(),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->groupBy('verse')->orderBy([
                    'verse' => SORT_ASC
                ])->asArray()->all(), 'verse', 'verse'),
                'attribute' => 'verse',
                'value' => function ($model) {
                    return $model->verse;
                },
                'format' => 'html'
            ],
            'text:ntext',
            // 'translation_id',
            // [
            //     'class' => \common\grid\EnumColumn::className(),
            //     'filter' => \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->groupBy('translation_id')->orderBy([
            //         'translation_id' => SORT_ASC
            //     ])->asArray()->all(), 'translation_id', 'translation_id'),
            //     'attribute' => 'translation_id',
            //     'value' => function ($model) {
            //         return $model->translation_id;
            //     },
            //     'format' => 'html'
            // ],
            // 'book_id',
            [
                'class' => \common\grid\EnumColumn::className(),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->groupBy('book_id')->orderBy([
                    'book_id' => SORT_ASC
                ])->asArray()->all(), 'book_id', 'book_id'),
                'attribute' => 'book_id',
                'value' => function($model){
                    return $model->book_id;
                },
                'format' => 'html'
            ],
            // 'book_name',
            [
                'class' => \common\grid\EnumColumn::className(),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->groupBy('book_name')->orderBy([
                    'book_name' => SORT_ASC
                ])->asArray()->all(), 'book_name', 'book_name'),
                'attribute' => 'book_name',
                'value' => function($model){
                    return $model->book_name;
                },
                'format' => 'html'
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>

</div>