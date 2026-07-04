<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bible */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Bible',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Bibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="bible-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
