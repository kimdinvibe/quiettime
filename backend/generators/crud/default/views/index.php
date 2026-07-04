<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?php echo $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?php echo !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?php echo $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if(!empty($generator->searchModelClass)): ?>
<?php echo "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?php echo "<?php echo " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?php echo !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            //['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        //if (++$count < 6) {
        if (in_array($column->name, ['state'])) {
            $relationName = explode("_", $column->name)[0];

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'state',
                'enum' => $generator->modelClass:getStateLabel(),
                'filter' => $generator->modelClass::getStateLabel(),
                'value' => function(\$model){
                    return \$model->{$column->name}?{$generator->modelClass}::getStateLabel(\$model->{$column->name}):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],\n";
        } else if (in_array($column->name, ['status'])) {
            $relationName = explode("_", $column->name)[0];

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'enum' => $generator->modelClass::getNameStatus(),
                'filter' => $generator->modelClass::getNameStatus(),
                'value' => function(\$model){
                    return \$model->{$column->name}?{$generator->modelClass}::getNameStatus(\$model->{$column->name}):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],\n";
        } elseif (in_array($column->name, ['author_id', 'user_id', 'response_id'])) {
            $relationName = explode("_", $column->name)[0];

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
//                'enum' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
//                    'username' => SORT_ASC
//                ])->asArray()->all(), 'id', 'username'),
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy([
                    'username' => SORT_ASC
                ])->asArray()->all(), 'id', 'username'),
                'attribute' => '{$column->name}',
                'value' => function(\$model){
                    return \$model->{$relationName}?Html::a(\$model->{$relationName}->username, ['user/view', 'id' => \$model->{$relationName}->id]):null;
                },
                'format' => 'html'
            ],\n";
        } elseif (in_array($column->name, ['created_at', 'updated_at'])) {
            // echo  "            "."[
            //     'attribute' => '{$column->name}',
            //     'filter' => false,
            //     'value' => function(\$model){
            //         return \$model->{$column->name}?Yii::\$app->formatter->asDatetime(\$model->{$column->name}):null;
            //     },
            //     'contentOptions' => [
            //         'style'=>'width: 140px;'
            //     ]
            // ],\n";
            echo  "            "."[
                'attribute' => '{$column->name}',
                'filter' => false,
                'value' => function(\$model){
                    return \$model->{$column->name}?Yii::\$app->formatter->asDatetime(\$model->{$column->name}):null;
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>\$searchModel,
                    'attribute'=>'{$column->name}',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'contentOptions' => [
                    'style'=>'width: 140px;'
                ]
            ],\n";
        } elseif (count(explode("_id", $column->name)) == 2 && explode("_id", $column->name)[1] == "") {
            $relationName = explode("_id", $column->name)[0];
            $relationNameAction = str_replace("_", "", $relationName);
            $relationNames = explode("_", $relationName);

            $relationName = [];

            for ($i = 0; $i < count($relationNames); $i++) {
                $relationName[] = $i ? ucfirst($relationNames[$i]) : $relationNames[$i];
            }

            $relationName = implode("", $relationName);
            $relationClassName = ucfirst($relationName);

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                 'filter' => \yii\helpers\ArrayHelper::map(\common\models\\$relationClassName::find()->orderBy([
                    'title' => SORT_ASC
                ])->limit(1000)->asArray()->all(), 'id', 'title'),
                'attribute' => '{$column->name}',
                'value' => function(\$model){
                    return \$model->{$relationName}?Html::a(\$model->{$relationName}->title, ['{$relationNameAction}/view', 'id' => \$model->{$relationName}->id]):null;
                },
                'format' => 'html'
            ],\n";
        } else {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }

//        } else {
//            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
//        }
    }
}
?>

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php else: ?>
    <?php echo "<?php echo " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?php echo $nameAttribute ?>), ['view', <?php echo $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>

</div>
