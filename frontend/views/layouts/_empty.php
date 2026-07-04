<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

\frontend\assets\FrontendAsset::register($this);
?>
<?php $this->beginPage() ?>
<?php $this->beginBody(); echo $content; $this->endBody() ?>
<?php $this->endPage() ?>
