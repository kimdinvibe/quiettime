<?php

use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Dictionary */
/* @var $form yii\bootstrap\ActiveForm */

?>

<div class="template-step row" style="display: none">
    <section class="col-md-3">
        <div class="form-group">
            <label class="control-label"><?= Yii::t("backend", "Name") ?></label>
            <input class="form-control" name="name" style="width: 100%">
        </div>
    </section>
    <section class="col-md-8">
        <div class="form-group">
            <label class="control-label"><?= Yii::t("backend", "Value") ?></label>
            <input class="form-control" name="value" style="width: 100%">
        </div>
    </section>
    <section class="col-md-1" style="text-align: center; padding-top: 30px;">
        <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', ['class' => 'item-remove']) ?>
    </section>
</div>

<div class="dictionary-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\DictionaryCategory::find()
        ->orderBy([
            'title' => SORT_ASC
        ])->asArray()->all(), 'id', 'title'), [
        'prompt' => Yii::t('backend', 'Select from list')
    ]) ?>

    <?php echo $form->field($model, 'parent_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Dictionary::find()
        ->where([
//             'not in', 'category_id', [
//                     \common\models\DictionaryCategory::CATEGORY_GENDER,
//                     \common\models\DictionaryCategory::CATEGORY_TYPE_MEETING,
//                     \common\models\DictionaryCategory::CATEGORY_BUDGET,
//                     \common\models\DictionaryCategory::CATEGORY_TOPICS,
//                     \common\models\DictionaryCategory::CATEGORY_CITY,
// //                    \common\models\DictionaryCategory::CATEGORY_COUNTRY,
//                     \common\models\DictionaryCategory::CATEGORY_LANGUAGE,
//                     \common\models\DictionaryCategory::CATEGORY_LOCALE,
//             ]
        ])
        ->orderBy([
            'title' => SORT_ASC
        ])->asArray()->all(), 'id', 'title'), [
        'prompt' => Yii::t('backend', 'Select from list')
    ]) ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>



<!--    --><?php //echo $form->field($model, 'status')->textInput() ?>

    <?php echo $form->field($model, 'order')->textInput() ?>

    <?php echo $form->field($model, 'image')->widget(
        Upload::className(),
        [
            'url' => ['/file-storage/upload'],
            'sortable' => true,
            'maxFileSize' => 10000000, // 10 MiB
            'maxNumberOfFiles' => 1
        ]);
    ?>

<!--    --><?php //echo $form->field($model, 'author_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\User::find()
//        ->orderBy([
//            'username' => SORT_ASC
//        ])->asArray()->all(), 'id', 'username'), [
//        'prompt' => Yii::t('backend', 'Select from list')
//    ]) ?>

    <h2>Data <?php echo Html::a(Yii::t('backend', '<span class="glyphicon glyphicon-plus"></span>'), '#', [
            'class' => 'create-step btn btn-primary btn-xs'
        ]) ?></h2>
    <div class="steps" style="margin-top: 10px;"></div>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    var counter = 0;
    var counterStep = 0;

    $(document).ready(function () {
        $('body').on('click', '.item-remove', function () {
            $(this).parent().parent().remove();
            return false;
        });

        $('.create-step').click(function () {
            var obj = $('.template-step').first().clone().removeClass('template-step').toggle(true);
            obj.attr('step', counterStep);
            obj.find('input').first().attr('name', 'Items['+counterStep+'][title]');
            obj.find('input').last().attr('name', 'Items['+counterStep+'][value]');

            $('.steps').append(obj);

            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
            counterStep++;

            return false;
        });

        // $('button[type=submit]').click(function () {
        //     $('.dictionary-form form').submit();
        //
        //     return false;
        // });

        <?php if($model->data): ?>
        var list = <?= json_encode($model->data) ?>;


        if (list) {
            for (var index in list) {
                $('.create-step').trigger('click');

                var step = $('.steps > div.row').last();

                step.find('input').first().val(index);
                step.find('input').last().val(list[index]);
            }
        }
        <?php endif; ?>
    })
</script>