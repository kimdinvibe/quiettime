<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-log-view">

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
        .content table th:first-child {
            width: 40%
        }
    </style>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
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
                'value' => function ($model) {
                    return $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : null;
                },
                'contentOptions' => [
                    'style' => 'width: 140px;'
                ]
            ],
        ],
    ]) ?>

</div>

<?php if ($model->object && $object = json_decode($model->object, true)) : ?>
    <?php VarDumper::dump($object, $depth = 10, $highlight = true) ?>
<?php endif; ?>