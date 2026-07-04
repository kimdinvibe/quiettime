<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Bible */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Bible',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Bibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bible-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
