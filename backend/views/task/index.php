<?php

use common\models\Task;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <!-- <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
            'modelClass' => 'Task',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <div id="calendar-container"></div>

    <div id="main-form" style="display: none;">
        <?php echo $this->render('_form', [
            'model' => new Task(),
        ]) ?>
    </div>
    


    <?php /* echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'date',
            // 'video_url:url',
            // 'meditation',
            // 'prayer',
            [
                'attribute' => 'created_at',
                'filter' => false,
                'value' => function ($model) {
                    return $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : null;
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'contentOptions' => [
                    'style' => 'width: 140px;'
                ]
            ],
            [
                'attribute' => 'updated_at',
                'filter' => false,
                'value' => function ($model) {
                    return $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at) : null;
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'contentOptions' => [
                    'style' => 'width: 140px;'
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); */ ?>

</div>


<?php $this->registerJsFile("@web/js/calendar.js?t=".time(), ['position' => '\yii\web\View::POS_END']); ?>