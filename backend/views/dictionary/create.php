<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Dictionary */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Dictionary',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
