<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StaticInfo */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Static Info',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Static Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-info-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
