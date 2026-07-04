<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Note */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Note',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
