<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserLog */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'User Log',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
