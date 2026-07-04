<?php

namespace backend\controllers;

use common\models\DictionaryLocalizable;
use common\models\DictionaryRef;
use common\models\DictionarySynonym;
use Yii;
use common\models\Dictionary;
use backend\models\search\DictionarySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DictionaryController implements the CRUD actions for Dictionary model.
 */
class DictionaryController extends Controller
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
     * Lists all Dictionary models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DictionarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dictionary model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($list = Yii::$app->request->post('Localizable')){
                DictionaryLocalizable::deleteAll([
                    'dictionary_id' => $model->id
                ]);

                foreach ($list as $locale => $value) {
                    if ($value = trim($value)) {
                        (new DictionaryLocalizable([
                            'dictionary_id' => $model->id,
                            'locale' => $locale,
                            'title' => $value
                        ]))->save();

                    }
                }

                Yii::$app->session->setFlash('alert', [
                    'body'=>\Yii::t('backend', 'Localizable titles saved'),
                    'options'=>['class'=>'alert-success']
                ]);

                return $this->redirect(['view', 'id' => $id]);
            }

            if ($synonym = Yii::$app->request->post('synonym')){
                (new DictionarySynonym([
                    'dictionary_id' => $model->id,
                    'title' => $synonym
                ]))->save();

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionSynonymRemove($id)
    {
        if (!$model = DictionarySynonym::findOne($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }



    /**
     * Creates a new Dictionary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new Dictionary();

        if ($id) {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($items = Yii::$app->request->bodyParams['Items']) {
                $list = [];

                foreach ($items as $item) {
                    $list[$item['title']] = $item['value'];

                }

                $model->data = $list;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

        } else {
            return $this->render($id ? 'update' : 'create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Dictionary model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->actionCreate($id);
    }

    public function actionUpdateRef($id, $delete_id = null, $add_id = null)
    {
        $model = $this->findModel($id);

        if ($delete_id) {
            DictionaryRef::findOne($delete_id)->delete();
        }

        if ($add_id) {
            (new DictionaryRef([
                'item_id' => $model->id,
                'parent_id' => $add_id
            ]))->save();
        }

        $searchModel = new DictionarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('update-ref', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Dictionary model.
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

        return Dictionary::find()
            ->select(['title as value', 'title as label', 'id'])
            ->where(['like', 'title', $term.'%', false])
            ->asArray()
            ->limit($max)
            ->all();
    }

    public function actionAutocompleteFilter($term, $category_id = null, $max = 20)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Dictionary::find()
            ->select(['id as value', 'title as label', 'id'])
            ->where(['like', 'title', $term.'%', false])
            ->asArray()
            ->limit($max)
            ->all();
    }

    /**
     * Finds the Dictionary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dictionary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dictionary::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
