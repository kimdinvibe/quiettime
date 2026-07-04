<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?php echo $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?php echo $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <p>
        <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('Update') ?>, ['update', <?php echo $urlParams ?>], ['class' => 'btn btn-primary']) ?>
        <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('Delete') ?>, ['delete', <?php echo $urlParams ?>], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => <?php echo $generator->generateString('Are you sure you want to delete this item?') ?>,
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <style>
        .content table th:first-child{width: 40%}
    </style>

    <?php echo "<?php echo " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);

        if (in_array($column->name, ['state'])) {
            $relationName = explode("_id", $column->name)[0];

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => '$column->name',
                'value' => function(\$model){
                    return \$model->{$column->name}?{$generator->modelClass}::getStateLabel(\$model->{$column->name}):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],\n";
        }
        if (in_array($column->name, ['status'])) {
            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => '$column->name',
                'value' => function(\$model){
                    return \$model->{$column->name}?{$generator->modelClass}::getNameStatus(\$model->{$column->name}):null;
                },
                'contentOptions' => [
                    'style'=>'width: 90px;'
                ]
            ],\n";
        }
        elseif (in_array($column->name, ['author_id', 'user_id'])) {
            $relationName = explode("_id", $column->name)[0];

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'author_id',
                'value' => function(\$model){
                    return \$model->{$relationName}?Html::a(\$model->{$relationName}->username, ['user/view', 'id' => \$model->{$relationName}->id]):null;
                },
                'format' => 'html'
            ],\n";
        } elseif (in_array($column->name, ['created_at', 'updated_at'])) {
            echo  "            "."[
                'attribute' => '{$column->name}',
                'filter' => false,
                'value' => function(\$model){
                    return \$model->{$column->name}?Yii::\$app->formatter->asDatetime(\$model->{$column->name}):null;
                },
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

            echo  "            "."[
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => '{$column->name}',
                'value' => function(\$model){
                    return \$model->{$relationName}?Html::a(\$model->{$relationName}->title, ['{$relationNameAction}/view', 'id' => \$model->{$relationName}->id]):null;
                },
                'format' => 'html'
            ],\n";
        } else {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
        ],
    ]) ?>

</div>
