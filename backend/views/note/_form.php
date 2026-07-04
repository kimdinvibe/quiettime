<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Note */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'date')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'type')->textInput() ?>

    <?php echo $form->field($model, 'content')->textInput() ?>

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
                'source' => \yii\helpers\Url::to(['Note/autocomplete', 'category_id' => null]),
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
        <input type="hidden" id="note-user_id" class="" name="Note[user_id]">
    </div>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>