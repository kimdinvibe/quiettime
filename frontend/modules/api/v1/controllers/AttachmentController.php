<?php

namespace frontend\modules\api\v1\controllers;

use common\models\AttachmentReport;
use frontend\components\ApiController;
use frontend\modules\api\v1\resources\Attachment;
use Yii;
use frontend\modules\api\v1\resources\ChatGroup as ChatGroupResource;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AttachmentController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Attachment';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                /*[
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        $user = User::findByLogin($username);
                        return $user->validatePassword($password)
                            ? $user
                            : null;
                    }
                ],*/
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => []
        ];

        return $behaviors;
    }
    
    protected function verbs(){
        return [
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH','POST'],
            'index' => ['GET'],
            'view' => ['GET'],
            'delete'=>['POST'],
            'create-invite-chat'=>['POST']
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
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel']
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }

   public function actionDelete() {
       $params = Yii::$app->request->getBodyParams();

       if ($model = Attachment::find()->where([
           'id' => $params['id'],
           'author_id' => Yii::$app->user->id,
           //'class' => \common\models\Request::className(),
       ])->one()) {
            if ($model->delete()) {
                return ['result' => 'success'];
            }

           throw new HttpException(500, Yii::t('api', 'Server error'));
       } else {
           throw new HttpException(404, Yii::t('api', 'Not found'));
       }
   }

    // public function actionReport() {
    //     $params = Yii::$app->request->getBodyParams();

    //     if ($model = Attachment::find()->where([
    //         'id' => $params['id'],
    //         //'class' => \common\models\Request::className(),
    //     ])->one()) {
    //         if ($params['cause']) {
    //             if ((new AttachmentReport([
    //                 'attachment_id' => $model->id,
    //                 'cause' => $params['cause'],
    //                 'detail' => $params['detail']
    //             ]))->save()) {
    //                 return ['result' => 'success'];
    //             }

    //             throw new HttpException(500, Yii::t('api', 'Server error'));
    //         } else {
    //             throw new HttpException(500, Yii::t('api', 'Need require params'));
    //         }
    //     } else {
    //         throw new HttpException(404, Yii::t('api', 'Not found'));
    //     }
    // }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Attachment::find()->where([
            Attachment::tableName().'.id' => $id
        ])->one();
        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
        return $model;
    }
}
