<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Bible */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Bibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bible-view">

    <!-- <p>
        <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p> -->

    <style>
        .content table th:first-child{width: 40%}
    </style>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'chapter',
            'verse',
            'text:ntext',
            'book_id',
            'book_name',
        ],
    ]) ?>

</div>
