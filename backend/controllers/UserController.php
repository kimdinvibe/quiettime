<?php

namespace backend\controllers;

use common\models\UserBlock;
use Yii;
use common\models\User;
use common\models\UserProfile;
use backend\models\UserForm;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['user', 'agent']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['administrator'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex($role = null, $titlePage = null)
    {
        $searchModel = new UserSearch();

        $params = Yii::$app->request->queryParams;

        if ($role) {
            $params['UserSearch']['group'] = $role;
        }


        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'titlePage' => $titlePage ? $titlePage : Yii::t('backend', 'Users'),
            'role' => $role,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();
        $modelProfile = new UserProfile();

        $model->setScenario('create');
        if (Yii::$app->request->post()) {
        }
        if (
            $model->load(Yii::$app->request->post()) && $model->validate()
            //&& $modelProfile->load(Yii::$app->request->post()) && $modelProfile->validate() 
            //&& $modelAgent->load(Yii::$app->request->post()) && $modelAgent->validate()
            && $model->save()
        ) {
            $model->model->refresh();

            if ($model->model->userProfile) {
                $model->model->userProfile->load(Yii::$app->request->post());
                if (!$model->model->userProfile->save()) {
                    $error = true;
                }
            }

            if (!$error) {
                return $this->redirect(['index']);
            } else {
                $model->model->delete();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelProfile' => $modelProfile,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new UserForm();
        $modelProfile = new UserProfile();

        $model->setModel($this->findModel($id));

        if ($model->model->userProfile) {
            $modelProfile = $model->model->userProfile;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //            $isBlocked = Yii::$app->request->post('is_blocked');
            //
            //            if ($isBlocked !== null) {
            //                if ($isBlocked) {
            //                    (new UserBlock([
            //                        'author_id' => Yii::$app->params['systemUser'],
            //                        'user_id' => $model->model->id
            //                    ]))->save();
            //                } else {
            //                    UserBlock::deleteAll(['and',
            //                        ['author_id' => Yii::$app->params['systemUser']],
            //                        ['user_id' => $model->model->id],
            //                        ['parent_id' => null]
            //                    ]);
            //                }
            //            }

            $model->model->refresh();

            if ($model->model->userProfile) {
                $model->model->userProfile->load(Yii::$app->request->post());
                if (!$model->model->userProfile->save()) {
                    $error = true;
                }
            }

            if (!$error) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelProfile' => $modelProfile,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->authManager->revokeAll($id);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAutocomplete($term, $category_id = null, $max = 20)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return User::find()
            ->select(['username as value', 'username as label', 'id'])
            ->where(['like', 'username', $term . '%', false])
            ->asArray()
            ->limit($max)
            ->all();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
