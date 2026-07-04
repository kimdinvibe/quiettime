<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DictionaryCategory */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Dictionary Category',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionary Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-category-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
