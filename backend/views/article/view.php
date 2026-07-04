<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <p>
        <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <style>
        .content table th:first-child{width: 40%}
    </style>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'image_path',
            'image_base_url:url',
            'text:ntext',
            // 'content',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status?common\models\Article::getNameStatus($model->status):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],
            [
                'class' => \common\grid\EnumColumn::className(),
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
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],
        ],
    ]) ?>

<?= $model->content ?>

</div>
