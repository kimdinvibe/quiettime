<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Note */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Note',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="note-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
