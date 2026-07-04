<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Webview */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Webview',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Webviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webview-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
