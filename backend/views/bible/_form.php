<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Bible */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="bible-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'chapter')->textInput() ?>

    <?php echo $form->field($model, 'verse')->textInput() ?>

    <?php echo $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?php /*echo $form->field($model, 'translation_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\_Category_::find()
            ->orderBy([
                'title' => SORT_ASC
            ])->asArray()->all(), 'id', 'title'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>

    <div class="autoComplete-row">
        <?= $form->field($model, 'translation_id')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['Bible/autocomplete', 'category_id' => null]),
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
                'value' => $model->translation_id
            ],
        ]) ?>
        <input type="hidden" id="bible-translation_id" class="" name="Bible[translation_id]">
    </div>

    <?php /*echo $form->field($model, 'book_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\_Category_::find()
            ->orderBy([
                'title' => SORT_ASC
            ])->asArray()->all(), 'id', 'title'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>

    <div class="autoComplete-row">
        <?= $form->field($model, 'book_id')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['Bible/autocomplete', 'category_id' => null]),
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
                'value' => $model->book_id
            ],
        ]) ?>
        <input type="hidden" id="bible-book_id" class="" name="Bible[book_id]">
    </div>

    <?php echo $form->field($model, 'book_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>