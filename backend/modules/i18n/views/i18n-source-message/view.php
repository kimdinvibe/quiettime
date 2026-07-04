<?php

use backend\modules\i18n\models\I18nMessage;
use backend\modules\i18n\models\I18nSourceMessage;
use backend\modules\i18n\models\search\I18nMessageSearch;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\i18n\models\I18nSourceMessage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'I18n Source Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="i18n-source-message-view">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category',
            'message:ntext',
        ],
    ]) ?>

</div>


<?php

$searchModel = new I18nMessageSearch();

$params = Yii::$app->request->queryParams;
$params['I18nMessageSearch']['id'] = $model->id;

Yii::$app->request->setQueryParams($params);

$dataProvider = $searchModel->search($params);
Url::remember(\Yii::$app->request->getUrl(), 'i18n-messages-filter');

$languages = ArrayHelper::map(
    I18nMessage::find()->select('language')->distinct()->all(),
    'language',
    'language'
);
$categories = ArrayHelper::map(
    I18nSourceMessage::find()->select('category')->distinct()->all(),
    'category',
    'category'
);
?>

<div class="i18n-message-index">

    <h2>Messages: </h2>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
            'modelClass' => 'I18n Message',
        ]), ['i18n-message/create', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'language',
                'filter'=> $languages
            ],
            [
                'attribute'=>'category',
                'filter'=> $categories
            ],
            'translation:ntext',
            [
                    'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'controller' => 'i18n-message'
            ],
        ],
    ]); ?>

</div>
