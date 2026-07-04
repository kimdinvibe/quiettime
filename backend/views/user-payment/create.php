<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserPayment */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'User Payment',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
