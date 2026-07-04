<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserLog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'user_id')->textInput() ?>

    <?php echo $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'device_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'device_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'device_type')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'controller')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'headers')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
