<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Dictionary */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Dictionaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .table-detail table th:first-child{width: 30%;}
</style>

<div class="dictionary-view">

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
                        [
                            'class' => \common\grid\EnumColumn::className(),
                            'attribute' => 'parent_id',
                            'filter' => \yii\helpers\ArrayHelper::map(\common\models\Dictionary::find()->where([
                                // '!=', 'category_id', \common\models\DictionaryCategory::CATEGORY_CITY
                            ])->orderBy([
                                'title' => SORT_ASC
                            ])->asArray()->all(), 'id', 'title'),
                            'value' => function($model){
                                return $model->parent?Html::a($model->parent->title, ['dictionary/view', 'id' => $model->parent->id]):null;
                            },
                            'format' => 'html'
                        ],
                        'title',
                        'description:ntext',
                        [
                            'attribute' => 'data',
                            'format' => 'html',
                            'value' => function ($model) {
                                $out = "";

                                if ($model->data) {
                                    foreach ($model->data as $key => $value) {
                                        $out .= "<b>$key:</b> $value<br>";
                                    }
                                }

                                return $model->data ? $out : '';
                            }
                        ],
                        'slug',
                        'code',
                        'status',
                        'order',
                        'author_id',
                        'created_at',
                        'updated_at',
                        [
                            'label' => 'Additional ref',
                            'filter' => false,
                            'value' => function($model){
                                if ($list = \common\models\DictionaryRef::find()
                                    ->where(['item_id' => $model->id])
                                    ->with(['parent', 'item'])
                                    ->all()
                                ){
                                    $out = "";

                                    foreach ($list as $item) {
                                        $out .= $item->parent->category->title.': '.Html::a($item->parent->title, ['dictionary/view', $item->parent_id]).'<br>';
                                    }

                                    return $out;
                                }

                                return null;
                            },
                            'format' => 'html'
                        ],
                    ],
                ]);
            } catch (Exception $e) {
                //
            } ?>
        </div>
    </div>

</div>

<?php echo Html::a(Yii::t('backend', 'Update additional ref'), ['update-ref', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

<h3><?= Yii::t('backend', 'Synonyms') ?></h3>

<?php \yii\widgets\Pjax::begin() ?>
<?php if($list = \common\models\DictionarySynonym::find()->where([
        'dictionary_id' => $model->id
])->orderBy(['title' => SORT_ASC])->all()): ?>
    <table id="w0" class="table table-striped table-bordered detail-view">
        <tbody>
        <tr>
            <th><?= Yii::t("backend", "Synonym") ?></th>
            <th style="width: 100px;"><?= Yii::t("backend", "Actions") ?></th>
        </tr>
        <?php foreach ($list as $item) { ?>
            <tr>
                <th><?php echo $item->title ?></th>
                <th><?= Html::a("<span class=\"glyphicon glyphicon-trash\"></span>", ['synonym-remove', 'id' => $item->id]) ?></th>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- <div style="height: 10px;"></div>
<?php $form = ActiveForm::begin(); ?>

<div class="form-group">
    <?php echo Html::input("text", "synonym", "", [
            'placeholder' => Yii::t('backend', 'Synonym'),
        'class' => 'form-control'
    ]) ?>
</div>

<div class="form-group">
    <?php echo Html::submitButton( Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
</div> -->

<?php ActiveForm::end(); ?>
<div style="height: 30px;"></div>
<?php \yii\widgets\Pjax::end() ?>


<?php //if ($model->category_id != \common\models\DictionaryCategory::CATEGORY_LANGUAGE): ?>
    <h3><?= Yii::t('backend', 'Localizable titles') ?></h3>
    <?php if($list = \common\models\Dictionary::find()->where(['and',
            ['category_id' => \common\models\DictionaryCategory::CATEGORY_LANGUAGE],
            ['is not', 'code', null]
    ])->active()->orderBy(['title' => SORT_ASC])->all()): ?>
        <?php $form = ActiveForm::begin(); ?>
            <table id="w0" class="table table-striped table-bordered detail-view">
                <tbody>
                <tr>
                    <th><?= Yii::t("backend", "Language") ?></th>
                    <th><?= Yii::t("backend", "Locale") ?></th>
                    <th><?= Yii::t("backend", "Value") ?></th>
                </tr>
                <?php foreach ($list as $item) { ?>
                    <tr>
                        <th><?php echo $item->title ?></th>
                        <th><?php echo $item->code ?></th>
                        <th>
                            <div class="from-group">
                                <?php $value = null; ?>
                                <?php if ($model->localizableString) {
                                    foreach ($model->localizableString as $localizableString) {
                                        if ($item->code == $localizableString->locale) {
                                            $value = $localizableString->title;
                                            break;
                                        }
                                    }
                                } ?>
                                <?php echo Html::input("text", "Localizable[$item->code]", $value, [
                                    'style' => 'width: 100%',
                                    'class' => 'form-control'
                                ]) ?>
                            </div>
                        </th>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        <div class="form-group">
            <?php echo Html::submitButton( Yii::t('backend', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php //endif; ?>
<?php endif; ?>
