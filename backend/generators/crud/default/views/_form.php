<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */

$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?php echo "<?php " ?>$form = ActiveForm::begin(); ?>

    <?php echo "<?php echo " ?>$form->errorSummary($model); ?>

    <?php foreach ($generator->getColumnNames() as $attribute) {
        if (in_array($attribute, $safeAttributes)) {
            if (!in_array($attribute, ['created_at', 'updated_at', 'author_id'])) 
            {
                if (count(explode("_id", $attribute)) == 2 && explode("_id", $attribute)[1] == "") {
                    $nameDropDownList = "_Category_";
                    $nameOrderAttribute = 'title';

                    if (in_array($attribute, ['user_id', 'author_id'])) {
                        $nameDropDownList = 'User';
                        $nameOrderAttribute = 'username';
                    }

                    echo "    <?php /*echo \$form->field(\$model, '$attribute')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\\{$nameDropDownList}::find()
            ->orderBy([
                '$nameOrderAttribute' => SORT_ASC
            ])->asArray()->all(), 'id', '$nameOrderAttribute'), [
            'prompt' => Yii::t('backend', 'Select from list')
        ])*/ ?>\n\n";

                    $nameClass = explode("\\", $generator->modelClass);
                    $nameClass = $nameClass[count($nameClass) - 1];
                    $id = strtolower($nameClass . '-' . $attribute);


                    echo "    <div class=\"autoComplete-row\">
        <?= \$form->field(\$model, '{$attribute}')->widget(\yii\jui\AutoComplete::classname(), [
            'clientOptions' => [
                'id' => 'articleTags',
                'source' => \yii\helpers\Url::to(['{$nameClass}/autocomplete', 'category_id' => null]),
                'autoFill'=>true,
                'search' => 'js: function() {
                    console.log(111)
                        $(this).parent().next(\"input[type=hidden]\").val(\"\")
                                var term = this.value.split(/,s*/).pop();
                                if(term.length < 2)
                                    return false;
                             }',

                'select'=>new \yii\web\JsExpression('function(event, ui) {
                    $(this).parent().next(\"input[type=hidden]\").val(ui.item.id)
                    $(this).attr(\"id\", \"\")
            }'),
            ],
            'options'=>[
                'class'=>'form-control autoComplete-field',
                'name' => '',
                'obj' => 'autoComplete-field',
                'value' => \$model->{$attribute}
            ],
        ]) ?>
        <input type=\"hidden\" id=\"$id\" class=\"\" name=\"{$nameClass}[{$attribute}]\">
    </div>\n\n";
                } else {
                    echo "    <?php echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
                }
            }
        }
    } ?>
    <div class="form-group">
        <?php echo "<?php echo " ?>Html::submitButton($model->isNewRecord ? <?php echo $generator->generateString('Create') ?> : <?php echo $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php echo "<?php " ?>ActiveForm::end(); ?>

</div>