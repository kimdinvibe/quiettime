<?php

namespace backend\controllers;

use Yii;
use common\models\Task;
use backend\models\search\TaskSearch;
use common\models\TaskVerse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
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
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Task model.
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
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new Task();

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
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->actionCreate($id);
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);

        $model->delete();

        return $model;
    }

    public function actionAutocomplete($term, $category_id = null, $max = 20)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = Task::find()
            ->select(['title as value', 'title as label', 'id'])
            ->where(['like', 'title', $term . '%', false])
            ->asArray();

        if ($category_id) {
            $query->andWhere(['category_id' => $category_id]);
        }

        return $query
            ->limit($max)
            ->all();
    }

    public function actionChapters($book_name)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return \yii\helpers\ArrayHelper::map(\common\models\Bible::find()->where([
            'book_name' => $book_name,
        ])->groupBy('chapter')->orderBy([
            'chapter' => SORT_ASC
        ])->asArray()->all(), 'chapter', 'chapter');
    }

    public function actionVerses($book_name, $chapter)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return \common\models\Bible::find()
            // ->select(['id', 'verse', 'text'])
            ->where([
                'book_name' => $book_name,
                'chapter' => $chapter,
            ])->orderBy([
                'verse' => SORT_ASC
            ])->asArray()->all();
    }

    public function actionFind($date)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = Task::find()->where([
            'date' => $date,
        ])->asArray()->one();

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model["verses"] = TaskVerse::find()
            ->where(["task_id" => $model["id"]])
            ->innerJoinWith(['bible'])
            ->asArray()
            ->all();

        return $model;
    }

    public function actionItems($month, $year)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $date = '01-' . $month . '-' . $year;
        // return strtotime($date);

        $days = Task::find()
            ->select('date')
            ->where([
                'and',
                ['>=', 'created_at', strtotime($date)],
                ['<', 'created_at', strtotime("+1 month", strtotime($date))]

            ])
            ->groupBy('date')
            ->asArray()
            ->orderBy(['date' => SORT_ASC])
            ->column();

        return $days;
    }

    public function actionSave()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $params = Yii::$app->request->post();

        if ($params["id"]) {
            $model = $this->findModel($params["id"]);
        } else {
            $model = new Task();
        }

        $model->date = $params["date"];
        
        $model->meditation_title_1 = $params["meditation_title_1"];
        $model->meditation_1 = $params["meditation_1"];
        $model->meditation_title_2 = $params["meditation_title_2"];
        $model->meditation_2 = $params["meditation_2"];

        $model->essay_title = $params["essay_title"];
        $model->essay = $params["essay"];
        
        $model->prayer = $params["prayer"];
        $model->video_url = $params["video_url"];
        $model->title = $params["title"];
        $model->descr = $params["descr"];
        $model->application_1 = $params["application_1"];
        $model->application_2 = $params["application_2"];
        
        if ($model->date) {
            $_date = explode("-", $model->date);
            $model->date_at = strtotime($_date[1]."-".$_date[0]."-".$_date[2]);
        }

        if ($model->save()) {
            TaskVerse::deleteAll(['task_id' => $model->id]);
            
            if ($params['verses']) {
                foreach ($params['verses'] as $key => $value) {
                    (new TaskVerse([
                        'task_id' => $model->id,
                        'bible_id' => $value
                    ]))->save();
                }
            }
        }

        return $model;
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
