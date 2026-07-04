<?php

namespace frontend\modules\api\v1\controllers;

use common\models\ArticleView;
use common\models\MessageView;
use common\models\User;
use frontend\modules\api\v1\resources\Article as ArticleResource;
use frontend\components\ApiController;
use frontend\modules\api\v1\resources\MessageListItem;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\api\v1\resources\ArticleListItem;
use frontend\modules\api\v1\resources\User as ResourcesUser;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class TestController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\User';



    protected function verbs()
    {
        return [
            'index' => ['GET'],
            'view' => ['GET'],
            'count-new' => ['GET'],
            'user-view' => ['POST']
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            //            'index' => [
            //                'class' => 'yii\rest\IndexAction',
            //                'modelClass' => $this->modelClass
            //            ],
            //            'view' => [
            //                'class' => 'yii\rest\ViewAction',
            //                'modelClass' => $this->modelClass,
            //                'findModel' => [$this, 'findModel']
            //            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }

    public function actionIndex()
    {
        // try {
        //     Yii::$app->runAction('/api/v1/user/check');
        // } catch (\Exception $e) {
        // }

        $query = ResourcesUser::find()
            ->active();



        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);


        // $activeDataProvider->pagination = false;

        $_GET['expand'] = 'userProfile,access_token,role';

        return $activeDataProvider;
    }
}
