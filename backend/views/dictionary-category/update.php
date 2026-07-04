<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DictionaryCategory */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Dictionary Category',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionary Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="dictionary-category-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
