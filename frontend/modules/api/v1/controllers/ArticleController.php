<?php

namespace frontend\modules\api\v1\controllers;

use common\models\Article;
use Yii;
use common\models\User;
use yii\web\HttpException;
use common\models\ArticleView;
use common\models\MessageView;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\ServerErrorHttpException;
use frontend\components\ApiController;
use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\api\v1\resources\ArticleListItem;
use frontend\modules\api\v1\resources\MessageListItem;
use frontend\modules\api\v1\resources\Article as ArticleResource;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class ArticleController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Article';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => ['view']
        ];

        return $behaviors;
    }

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

    public function actionView($id)
    {
        try {
            Yii::$app->runAction('/api/v1/user/check');
        } catch (\Exception $e) {
        }

        $model = $this->findModel($id);

        try {
            Yii::$app->runAction('/api/v1/user/check');

            if (!Yii::$app->user->isGuest) {
                if (!ArticleView::find()->where([
                    'user_id' => Yii::$app->user->id,
                    'article_id' => $model->id
                ])->one()) {
                    (new ArticleView([
                        'user_id' => Yii::$app->user->id,
                        'article_id' => $model->id
                    ]))->save();
                }
            }
        } catch (\Exception $e) {
            //
        }

        return $model;
    }

    public function actionRead($id)
    {
        $model = $this->findModel($id);

        if (!ArticleView::find()->where([
            'user_id' => Yii::$app->user->id,
            'article_id' => $model->id
        ])->one()) {
            (new ArticleView([
                'user_id' => Yii::$app->user->id,
                'article_id' => $model->id
            ]))->save();
        }

        return $model;
    }

    public function actionIndex()
    {
        $query = ArticleListItem::find()
            ->with(['viewUser', 'author']);

        if (Yii::$app->user->can('administrator')) {
            $query->orderBy(['id' => SORT_DESC]);
        } else {
            $query->active();
            $query->orderBy(['isread' => SORT_ASC, 'id' => SORT_DESC]);
        }

        $query->select(ArticleListItem::tableName() . '.*,
            (SELECT COUNT(' . ArticleView::tableName() . '.id) FROM ' . ArticleView::tableName() . ' WHERE ' . ArticleView::tableName() . '.article_id = ' . ArticleListItem::tableName() . '.id and ' . ArticleView::tableName() . '.user_id=' . (Yii::$app->user->isGuest ? -1 : Yii::$app->user->id) . ') AS isread 
        ');

        return new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    /**
     * Create User model.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->_update();
    }

    /**
     * Update User model.
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->_update($id);
    }

    /**
     * Update User model.
     * @return mixed
     */
    public function _update($id = null)
    {
        $params = Yii::$app->request->getBodyParams();

        $model = new ArticleResource();

        if ($id) {
            $model = $this->findModel($id);
        }

        $model->load(['Article' => $params]);

        if ($model->save()) {
            if (isset($params['file']['name']) && $params['file']['name'] && $_FILES) {
                // load file
                try {
                    if ($result = Yii::$app->runAction(
                        '/api/v1/file/upload',
                        [
                            'name' => $params['file']['name'],
                            'title' => isset($params['file']['title']) ? $params['file']['title'] : ''
                        ]
                    )) {
                        if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => $result['id']])) {
                            $model->image_path = $fileId->path;
                            $model->image_base_url = $fileId->base_url;

                            $model->update(false, ['image_path', 'image_base_url']);
                        }
                    }
                } catch (\Exception $e) {
                    throw new ServerErrorHttpException(Yii::t('api', $e->getMessage()));
                }
            }

            $model->refresh();
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
        }

        return $model;
    }

    public function actionChangeStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == Article::STATUS_ACTIVE
            ? Article::STATUS_DRAFT
            : Article::STATUS_ACTIVE;

        if ($model->save()) {
            return $model;
        }

        throw new HttpException(404, Yii::t('api', 'Error'));
    }

    public function actionDelete() {
        $params = Yii::$app->request->getBodyParams();
        $model = $this->findModel($params['id']);
 
        if ($model = $this->findModel($params['id'])) {
             if ($model->delete()) {
                //  return ['result' => 'success'];
                 return $model;
             }
 
            throw new HttpException(500, Yii::t('api', 'Server error'));
        } else {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $query = ArticleResource::find()
            ->with(['author'])
            ->where(['id' => $id])
            // ->active()
        ;

        if (!Yii::$app->user->can('administrator')) {
            $query->active();
        }

        $model = $query->one();

        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
        return $model;
    }
}
