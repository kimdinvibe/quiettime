<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dictionary */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Dictionary',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="dictionary-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
