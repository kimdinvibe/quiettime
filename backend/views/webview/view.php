<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Webview */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Webviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webview-view">

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
        table th:first-child {
            width: 25%;
        }

        img {max-width: 100%;}
    </style>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'code',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status?\common\models\Webview::getStateLabel($model->status):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <?= $model->content ?>

</div>
