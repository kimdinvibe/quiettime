<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BibleSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="bible-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'chapter') ?>

    <?php echo $form->field($model, 'verse') ?>

    <?php echo $form->field($model, 'text') ?>

    <?php echo $form->field($model, 'translation_id') ?>

    <?php // echo $form->field($model, 'book_id') ?>

    <?php // echo $form->field($model, 'book_name') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
