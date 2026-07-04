<?php

namespace frontend\modules\api\v1\controllers;

use Yii;
use yii\di\Instance;
use common\models\User;
use trntv\filekit\Storage;
use yii\web\HttpException;
use frontend\helpers\ApiHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\RbacAuthAssignment;
use frontend\components\ApiController;
use common\models\Order as ModelsOrder;
use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\api\v1\resources\Order;
use frontend\modules\api\v1\resources\Attachment;
use frontend\modules\api\v1\resources\NoteListItem;
use frontend\modules\api\v1\resources\Note as NoteResource;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class NoteController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Note';

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

    public function actionCreate()
    {
        $params = Yii::$app->request->getBodyParams();

        $model = new NoteResource([]);

        $model->load(["Note" => $params]);
        $model["user_id"] = Yii::$app->user->id;

        ApiHelper::checkRequiredFields([
            'content' => Yii::t('api', 'Content'),
            'date' => Yii::t('api', 'Date'),
        ], $params);

        if ($model->save()) {
            $model->refresh();
        }


        return $model;
    }

    public function actionUpdate($id)
    {
        $params = Yii::$app->request->getBodyParams();
        $model = $this->findModel($id);

        $model->load(["Note" => $params]);
        $model["user_id"] = Yii::$app->user->id;

        // ApiHelper::checkRequiredFields([
        //     'content' => Yii::t('api', 'Content'),
        //     'date' => Yii::t('api', 'Date'),
        // ], $params);

        if ($model->save()) {
            $model->refresh();
        }


        return $model;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    public function actionViewByDate($date)
    {
        return NoteResource::find()->where([
            'user_id' => Yii::$app->user->id,
            'date' => $date,
        ])->all();
    }

    public function actionDeleteByDate($date)
    {
        NoteResource::deleteAll([
            'user_id' => Yii::$app->user->id,
            'date' => $date,
        ]);

        return ['result' => 'success'];
    }

    public function actionIndex($date = null, $isAll = null, $isGroupByDate = true, $month = null, $year = null)
    {
        $query = NoteListItem::find()->with([
            // 'user',
            // 'user.userProfile',
            // 'typeDelivery',
            // 'statusDelivery'
        ])->where(['user_id' => Yii::$app->user->id]);

        if ($date) {
            $query->andWhere([
                'date' => $date,
            ]);
        } else if ($month && $year) {
            $from = "01-" . ((int)$month < 10 ? "0" . ((int) $month) : $month) . "-" . $year;
            $to = "01-" .  ((int)($month + 1 > 12 ? 1 : $month + 1) < 10 ? "0" . ((int) ($month + 1 > 12 ? 1 : $month + 1)) : $month + 1) . "-" . ($month + 1 > 12 ? $year + 1 : $year);

            // return [
            //     $from,
            //     strtotime($from),
            //     $to,
            //     strtotime($to),
            // ];


            $query->andWhere([
                'and',
                [
                    '>=', 'created_at', strtotime($from),
                ],
                [
                    '<', 'created_at', strtotime($to),
                ],
            ]);
        }

        $query->orderBy(['id' => SORT_DESC]);

        if ($isGroupByDate) {
            $query->groupBy('date');
        }

        $dataProvider = [
            'query' => $query,
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ];

        if ($isAll) {
            $dataProvider['pagination'] = array(
                'pageSize' => 100,
            );
        }

        return new ActiveDataProvider($dataProvider);
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = NoteResource::find()
            ->with(['user'])
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id,])
            // ->active()
            ->one();

        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
        return $model;
    }
}
