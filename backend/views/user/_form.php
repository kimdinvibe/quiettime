<?php

use common\models\User;
use common\models\UserProfile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use trntv\filekit\widget\Upload;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->field($model, 'username') ?>
    <!-- <?php echo $form->field($model, 'phone') ?> -->
    <?php echo $form->field($model, 'email') ?>
    <?php echo $form->field($model, 'password')->passwordInput() ?>
    <?php //echo $form->field($model, 'status')->label(Yii::t('backend', 'Active'))->checkbox() 
    ?>
    <?php echo $form->field($model, 'status')->dropDownList(User::getStatuses()) ?>
    <?php
    if ($roles) {
        $rolesNew = [];

        foreach ($roles as $key => $value) {
            $rolesNew[$key] = Yii::t('backend', $value);
        }

        $roles = $rolesNew;
    }

    echo $form->field($model, 'roles')->checkboxList($roles) ?>

    <h2><?php echo Yii::t('backend', 'Profile') ?></h2>

    <div class="row">
        <div class="col-sm-3">
            <?php echo $form->field($modelProfile, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
                'url' => ['sign-in/avatar-upload']
            ]) ?></div>
    </div>


    <!-- <?php echo $form->field($modelProfile, 'firstname')->textInput(['maxlength' => 255]) ?> -->
    <?php
    //        echo $form->field($modelProfile, 'middlename')->textInput(['maxlength' => 255])
    ?>
    <!-- <?php echo $form->field($modelProfile, 'lastname')->textInput(['maxlength' => 255]) ?> -->
    <?php echo $form->field($modelProfile, 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?>

    <?php /* echo $form->field($modelProfile, 'premium_at')->widget(DatePicker::className(), [
        'options' => [
            'value' => Yii::$app->formatter->asDate($modelProfile->premium_at),
        ],
        // 'pluginOptions' => [
        //     'autoclose' => TRUE,
        //     'format'    => 'dd-mm-yyyy',
        //     'startDate' => 'd',
        // ]
    ]); */ ?>


    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>