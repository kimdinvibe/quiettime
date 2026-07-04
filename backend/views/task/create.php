<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Task',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
