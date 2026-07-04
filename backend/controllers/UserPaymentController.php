<?php

namespace backend\controllers;

use Yii;
use common\models\UserPayment;
use backend\models\search\UserPaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserPaymentController implements the CRUD actions for UserPayment model.
 */
class UserPaymentController extends Controller
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
        ];
    }

    /**
     * Lists all UserPayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserPayment model.
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
     * Creates a new UserPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new UserPayment();

        if ($id) {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render($id ? 'update' : 'create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserPayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->actionCreate($id);
    }

    /**
     * Deletes an existing UserPayment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAutocomplete($term, $category_id = null, $max = 20)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = UserPayment::find()
            ->select(['title as value', 'title as label', 'id'])
            ->where(['like', 'title', $term.'%', false])
            ->asArray();

        if ($category_id) {
            $query->andWhere(['category_id' => $category_id]);
        }

        return $query
            ->limit($max)
            ->all();
    }

    /**
     * Finds the UserPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
