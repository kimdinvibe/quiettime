<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserPayment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'amount')->textInput() ?>

    <?php echo $form->field($model, 'period')->textInput() ?>

    <?php echo $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <?php /*echo $form->field($model, 'user_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\User::find()
            ->orderBy([
                'username' => SORT_ASC
            ])->asArray()->all(), 'id', 'username'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>

    <div class="autoComplete-row">
        <?= $form->field($model, 'user_id')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['UserPayment/autocomplete', 'category_id' => null]),
                'autoFill'=>true,
                'search' => 'js: function() {
                    console.log(111)
                        $(this).parent().next("input[type=hidden]").val("")
                                var term = this.value.split(/,s*/).pop();
                                if(term.length < 2)
                                    return false;
                             }',

                'select'=>new \yii\web\JsExpression('function(event, ui) {
                    $(this).parent().next("input[type=hidden]").val(ui.item.id)
                    $(this).attr("id", "")
            }'),
            ],
            'options'=>[
                'class'=>'form-control autoComplete-field',
                'name' => '',
                'obj' => 'autoComplete-field',
                'value' => $model->user_id
            ],
        ]) ?>
        <input type="hidden" id="userpayment-user_id" class="" name="UserPayment[user_id]">
    </div>

    <?php echo $form->field($model, 'status')->textInput() ?>

    <?php echo $form->field($model, 'auth_token')->textInput(['maxlength' => true]) ?>

    <?php /*echo $form->field($model, 'payment_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\_Category_::find()
            ->orderBy([
                'title' => SORT_ASC
            ])->asArray()->all(), 'id', 'title'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>

    <div class="autoComplete-row">
        <?= $form->field($model, 'payment_id')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['UserPayment/autocomplete', 'category_id' => null]),
                'autoFill'=>true,
                'search' => 'js: function() {
                    console.log(111)
                        $(this).parent().next("input[type=hidden]").val("")
                                var term = this.value.split(/,s*/).pop();
                                if(term.length < 2)
                                    return false;
                             }',

                'select'=>new \yii\web\JsExpression('function(event, ui) {
                    $(this).parent().next("input[type=hidden]").val(ui.item.id)
                    $(this).attr("id", "")
            }'),
            ],
            'options'=>[
                'class'=>'form-control autoComplete-field',
                'name' => '',
                'obj' => 'autoComplete-field',
                'value' => $model->payment_id
            ],
        ]) ?>
        <input type="hidden" id="userpayment-payment_id" class="" name="UserPayment[payment_id]">
    </div>

    <?php /*echo $form->field($model, 'payment_log_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\_Category_::find()
            ->orderBy([
                'title' => SORT_ASC
            ])->asArray()->all(), 'id', 'title'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>

    <div class="autoComplete-row">
        <?= $form->field($model, 'payment_log_id')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['UserPayment/autocomplete', 'category_id' => null]),
                'autoFill'=>true,
                'search' => 'js: function() {
                    console.log(111)
                        $(this).parent().next("input[type=hidden]").val("")
                                var term = this.value.split(/,s*/).pop();
                                if(term.length < 2)
                                    return false;
                             }',

                'select'=>new \yii\web\JsExpression('function(event, ui) {
                    $(this).parent().next("input[type=hidden]").val(ui.item.id)
                    $(this).attr("id", "")
            }'),
            ],
            'options'=>[
                'class'=>'form-control autoComplete-field',
                'name' => '',
                'obj' => 'autoComplete-field',
                'value' => $model->payment_log_id
            ],
        ]) ?>
        <input type="hidden" id="userpayment-payment_log_id" class="" name="UserPayment[payment_log_id]">
    </div>

    <?php echo $form->field($model, 'confirmation_url')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>