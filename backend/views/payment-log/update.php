<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentLog */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Payment Log',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="payment-log-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
