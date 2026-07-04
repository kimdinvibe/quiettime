<?php

namespace frontend\modules\api\v1\controllers;

use common\models\Note;
use common\models\Task;
use common\models\TaskUser;
use Yii;
use yii\web\HttpException;
use frontend\helpers\ApiHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\components\ApiController;
use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\api\v1\resources\Task as TaskResource;
use frontend\modules\api\v1\resources\TaskMiniList;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class TaskController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Task';

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
            // 'except' => ['view']
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
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }

    public function actionView($date)
    {
        $model = TaskResource::find()
            // ->with(['user'])
            ->where(['date' => $date])
            ->one();

        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }

        return $model;
    }

    public function actionFinish($id)
    {
        $model = $this->findModel($id);
        $params = Yii::$app->request->post();

        $where = [
            'task_id' => $id,
            'user_id' => Yii::$app->user->id
        ];

        if (!$taskModel = TaskUser::find()->where($where)->one()) {
            $taskModel = new TaskUser($where);
        }

        if (isset($params['answer_1']) && $params['answer_1']) {
            $taskModel->answer_1 = $params['answer_1'];
        }

        if (isset($params['answer_2']) && $params['answer_2']) {
            $taskModel->answer_2 = $params['answer_2'];
        }

        if ($taskModel->save()) {
            Note::deleteAll([
                "type" => Note::TYPE_RESPONSE,
                "date" => $model->date,
                "user_id" => Yii::$app->user->id,
            ]);

            if (isset($params['answer_1']) && $params['answer_1']) {
                (new Note([
                    "content" => $model->application_1 . "\n\n" . $params['answer_1'],
                    "type" => Note::TYPE_RESPONSE,
                    "user_id" => Yii::$app->user->id,
                    "date" => $model->date,
                ]))->save();
            }

            if (isset($params['answer_2']) && $params['answer_2']) {
                (new Note([
                    "content" => $model->application_2 . "\n\n" . $params['answer_2'],
                    "type" => Note::TYPE_RESPONSE,
                    "user_id" => Yii::$app->user->id,
                    "date" => $model->date,
                ]))->save();
            }

            $model->refresh();
        }



        return $model;
    }

    public function actionItemsCalendar($month = null, $year = null)
    {
        $from = "01-" . ((int)$month < 10 ? "0" . ((int) $month) : $month) . "-" . $year;
        $to = "01-" .  ((int)($month + 1 > 12 ? 1 : $month + 1) < 10 ? "0" . ((int) ($month + 1 > 12 ? 1 : $month + 1)) : $month + 1) . "-" . ($month + 1 > 12 ? $year + 1 : $year);

        // return [
        //     $from,
        //     strtotime($from),
        //     $to,
        //     strtotime($to),
        // ];

        return TaskMiniList::find()
            ->where([
                'and',
                [
                    '>=', 'date_at', strtotime($from),
                ],
                [
                    '<', 'date_at', strtotime($to),
                ],
            ])
            ->groupBy('date')
            ->orderBy(['date' => SORT_ASC])
            ->all();
    }

    public function actionIndex($search_query = null, $without_check_access = null)
    {
        $query = Task::find()->with([
            // 'user',
            // 'user.userProfile',
            // 'typeDelivery',
            // 'statusDelivery'
        ])->where(['user_id' => Yii::$app->user->id]);

        $query->orderBy(['id' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = TaskResource::find()
            // ->with(['user'])
            // ->where(['id' => $id, 'user_id' => Yii::$app->user->id,])
            ->where(['id' => $id])
            // ->active()
            ->one();

        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
        return $model;
    }
}
