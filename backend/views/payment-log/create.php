<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PaymentLog */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Payment Log',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Payment Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-log-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
