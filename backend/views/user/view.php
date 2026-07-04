<?php

use backend\models\search\CardSearch;
use backend\models\search\OrderSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use common\models\UserBlock;
use backend\models\search\UserMarkerSearch;
use backend\models\search\UserInvoiceSearch;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getPublicIdentity();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    table.detail-view td {
        width: 50% !important
    }
</style>

<div class="user-view">

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
        <!-- <div class="col-sm-3">
            <?php echo Html::img($model->userProfile->getAvatar(
                $this->assetManager->getAssetUrl(\backend\assets\BackendAsset::register($this), 'img/anonymous.jpg')
            ), [
                'style' => 'max-width: 100%; background: #f4f4f4; border-radius: 5%;'
            ]); ?>
        </div> -->

        <div class="col-md-12">
            <?php try {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'phone',
                        'username',
                        // [
                        //     'label' => Yii::t('backend', 'City'),
                        //     'value' => function ($model) {
                        //         if ($model->dictionaries) {
                        //             foreach ($model->dictionaries as $dictionary) {
                        //                 if($dictionary->category_id == \common\models\DictionaryCategory::CATEGORY_CITY) {
                        //                     return $dictionary->title;
                        //                     break;
                        //                 }
                        //             }
                        //         }

                        //         return '-';
                        //     }
                        // ],

                        'auth_key',
                        'password_reset_token',
                        'email:email',
                        [
                            'class' => \common\grid\EnumColumn::className(),
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return \common\models\User::getStatuses($model->status);
                            }
                        ],
                        "oauth_client",
                        'created_at:datetime',
                        'updated_at:datetime',
                        'logged_at:datetime',
                    ],
                ]);
            } catch (Exception $e) {
                //
            } ?>

            <h2><?php echo Yii::t('backend', 'Profile') ?></h2>
            <?php try {
                echo DetailView::widget([
                    'model' => $model->userProfile,
                    'attributes' => [
                        // 'firstname',
                        // 'middlename',
                        // 'lastname',
                        'locale',
                        'premium_at:datetime',
                        //            [
                        //                'attribute' => 'gender',
                        //                'value' => $model->userProfile?$model->userProfile->gender == \common\models\UserProfile::GENDER_FEMALE?Yii::t('backend', 'Female'):Yii::t('backend', 'Male'):null
                        //            ]
                    ],
                ]);
            } catch (Exception $e) {
                //
            } ?>
        </div>
    </div>

</div>