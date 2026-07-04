<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StaticInfo */

$this->title = Yii::t('backend', 'Статичный контент');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Static Infos'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Статичный контент');
?>
<div class="static-info-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>