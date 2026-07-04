<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'date')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <div class="row">
        <div class="col-sm-6">
            <?php echo $form->field($model, 'title') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'descr') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $form->field($model, 'video_url')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?php echo $form->field($model, 'meditation_title_1') ?>
            <?php echo $form->field($model, 'meditation_1')->textarea(['rows' => '6']) ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'meditation_title_2') ?>
            <?php echo $form->field($model, 'meditation_2')->textarea(['rows' => '6']) ?>
        </div>
    </div>

    <!-- <div style="height: 1px; background: #CBD1D2; width: 100%; margin-bottom: 10px;"></div> -->

    <div class="row">
        <div class="col-sm-6">
            <?php echo $form->field($model, 'essay_title') ?>
            <?php echo $form->field($model, 'essay')->textarea(['rows' => '6']) ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'prayer')->textarea(['rows' => '6']) ?>
        </div>
    </div>

    <h3>Reading</h3>

    <div class="row">
        <div class="col-sm-6">
            <div id="save-list">

            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Book</label>
                <?php echo Html::dropDownList('book', $selection = null, \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->where([
                    'translation_id' => \common\models\Bible::ACTIVE_TRANSLATION_ID,
                ])->groupBy('book_name')->orderBy([
                    'book_name' => SORT_ASC
                ])->asArray()->all(), 'book_name', 'book_name'), $options = [
                    'class' => 'form-control',
                    'prompt' => 'Select from list'
                ]) ?>
            </div>
            <div class="form-group">
                <label class="control-label">Сhapter</label>
                <?php echo Html::dropDownList('chapter', $selection = null, [], $options = [
                    'class' => 'form-control',
                    'prompt' => 'Select from list'
                ]) ?>
            </div>

            <div class="form-group">
                <label class="control-label">Verses</label>
                <div class="items">

                </div>
            </div>



        </div>
    </div>

    <h3>Application</h3>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">1.0</label>
                <div>
                    <?php echo Html::textarea("application_1", "", [
                        'class' => 'form-control',
                        'rows' => 6
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">2.0</label>
                <div>
                    <?php echo Html::textarea("application_2", "", [
                        'class' => 'form-control',
                        'rows' => 6
                    ]) ?>
                </div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Save') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Delete') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-danger' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>