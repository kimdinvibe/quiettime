<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserPayment */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'User Payment',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-payment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
