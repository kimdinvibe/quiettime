<?php

use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Dictionary */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'View'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .table-detail table th:first-child{width: 30%;}
</style>

<div class="dictionary-view">

    <div class="row">
        <div class="col-md-2">
            <?php if($model->image): ?>
                <?= Html::a(Html::img($model->getFullPath(), [
                    'style' => 'width: 100%; margin-right: 1%; margin-bottom: 1%;',
                ]), $model->getFullPath(), ['rel' => 'fancybox']); ?>
            <?php else: ?>
                <?= Yii::t('backend', 'No image') ?>
            <?php endif; ?>
        </div>
        <div class="col-md-10 table-detail">
            <?php try {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'category_id',
                        'title',
                    ],
                ]);
            } catch (Exception $e) {
                //
            } ?>
        </div>
    </div>

</div>

<?php \yii\widgets\Pjax::begin([
        'id' => 'remove-list',
    'enablePushState' => false
]) ?>

<h3><?= Yii::t('backend', 'Refs') ?></h3>
<table id="w0" class="table table-striped table-bordered detail-view">
    <tbody>
    <tr>
        <th><?= Yii::t("backend", "Name") ?></th>
        <th><?= Yii::t("backend", "Value") ?></th>
        <th width="90px" style="text-align: center"><?= Yii::t("backend", "Actions") ?></th>
    </tr>
    <?php if($list = \common\models\DictionaryRef::find()
        ->where(['item_id' => $model->id])
        ->with(['parent', 'item'])
        ->all()): ?>
        <?php foreach ($list as $item) { ?>
            <tr>
                <th><?= $item->parent->category->title ?></th>
                <th><?= Html::a($item->parent->title, ['dictionary/view', $item->parent_id]) ?></th>
                <th style="text-align: center"><?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['dictionary/update-ref', 'id' => $model->id, 'delete_id' => $item->id]) ?></th>
            </tr>
        <?php } ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="form-group">
    <?php echo Html::submitButton( Yii::t('backend', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php \yii\widgets\Pjax::end() ?>


<h3><?= Yii::t('backend', 'Dictionaries') ?></h3>

<?php \yii\widgets\Pjax::begin([
    'id' => 'main-list',
    'enablePushState' => false
]) ?>

<?php try {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'category_id',
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\DictionaryCategory::find()->orderBy([
                    'title' => SORT_ASC
                ])->asArray()->all(), 'id', 'title'),
                'value' => function ($model) {
                    return $model->category ? Html::a($model->category->title, ['dictionary-category/view', 'id' => $model->category->id]) : null;
                },
                'format' => 'html'
            ],
            'title',
            'code',
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'parent_id',
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Dictionary::find()->where([
                    // '!=', 'category_id', \common\models\DictionaryCategory::CATEGORY_CITY
                ])->orderBy([
                    'title' => SORT_ASC
                ])->asArray()->all(), 'id', 'title'),
                'value' => function ($model) {
                    return $model->parent ? $model->parent->title : null;
                },
                'format' => 'html'
            ],

            [
                'label' => Yii::t('backend', 'Actions'),
                'value' => function ($item) use ($model)  {
                    return Html::a('<span class="glyphicon glyphicon-plus"></span>', [
                            'dictionary/update-ref', 'id' => $model->id, 'add_id' => $item->id], [
                                    'class' => 'add-btn'
                    ]);
                },
                'format' => 'html'
            ]
        ],
    ]);
} catch (Exception $e) {
    //
} ?>
<?php \yii\widgets\Pjax::end() ?>


<script type="text/javascript">
    var isAdd = false;

    $(document).on('click', '.add-btn', function(){
        isAdd = true;
    });

    $(document).on('ready pjax:success', function(){
        if (isAdd) {
            isAdd = false;
            $.pjax.reload('#remove-list');
        }
    })
</script>