<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Webview */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Webview',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Webviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="webview-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
