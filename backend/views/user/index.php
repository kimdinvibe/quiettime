<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titlePage;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?php if (!$role) : ?>
        <p>
            <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
                'modelClass' => 'User',
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php try {
        $items = [
            'id',
            // 'phone',
            [
                'attribute' => 'username',
                'value' => function ($model) {
                    return $model->userProfile ? $model->userProfile->fullName : null;
                }
            ],
            [
                // 'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'email',
                'value' => function ($model) {
                    return strpos($model->email, '${new}$') !== false ? '-' : Html::mailto($model->email, $model->email);
                },
                'format' => 'html'
            ],
        ];

        // if (!$role) {
        //     $items[count($items)] =
        //         [
        //             'class' => \common\grid\EnumColumn::className(),
        //             'attribute' => 'group',
        //             'enum' => \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
        //             'filter' => \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
        //             'value' => function ($model) {
        //                 return $model->rbacAuthAssignment ? $model->rbacAuthAssignment->item_name : null;
        //             }
        //         ];
        // }

        $items[] = [
            'attribute' => 'is_premium',
            'label' => 'Premium',
            'filter' => [
                '1' => Yii::t("baclend", "No"),
                '2' => Yii::t("baclend", "Yes"),
            ],
            // 'attribute' => 'userProfile.premium_at',
            'value' => function ($model) {
                return $model->userProfile
                    && $model->userProfile->premium_at
                    && $model->userProfile->premium_at > time()
                    ? \Yii::$app->formatter->asDateTime($model->userProfile->premium_at)
                    : Yii::t("baclend", "No");
            }
        ];

        $items = array_merge_recursive($items, [
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'status',
                'enum' => User::getStatuses(),
                'filter' => User::getStatuses()
            ],

            'created_at:datetime',
            'logged_at:datetime',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:80px;']
            ],
        ]);


        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $items
        ]);
    } catch (Exception $e) {
        var_dump($e->getMessage());
    } ?>

</div>