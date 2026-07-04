<?php

namespace frontend\modules\api\v1\controllers;

use common\models\DictionaryLocalizable;
use frontend\components\ApiController;
use frontend\modules\api\v1\resources\Dictionary;
use frontend\modules\api\v1\resources\DictionaryCategory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class DictionaryController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\Dictionary';

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
            'except' => [
                // 'index',
            ]
        ];

        return $behaviors;
    }
    
    protected function verbs(){
        return [
            'index' => ['GET'],
            'cities' => ['GET'],
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

    public function actionLanguages($str = null)
    {
        return $this->actionIndex($str, DictionaryCategory::CATEGORY_LANGUAGE, ["order" => SORT_ASC, "title" => SORT_ASC], 100);
    }

    public function actionIndex($str = null, $category_id = null, $orderBy = "order", $limit = 20) {
        $locale = Yii::$app->language ? Yii::$app->language : 'en';

        $query =  Dictionary::find()->active()
            ->select([
                Dictionary::tableName().'.id',
                Dictionary::tableName().'.category_id',
                Dictionary::tableName().'.parent_id',
                Dictionary::tableName().'.data',
                Dictionary::tableName().'.code',
                Dictionary::tableName().'.base_url',
                Dictionary::tableName().'.path',
                Dictionary::tableName().'.author_id',
            ])
            ->addSelect(['(CASE WHEN '.DictionaryLocalizable::tableName().'.title IS NOT NULL THEN '.DictionaryLocalizable::tableName().'.title ELSE '.Dictionary::tableName().'.title END) AS title'])
            ->leftJoin(DictionaryLocalizable::tableName(),
                DictionaryLocalizable::tableName().'.dictionary_id='.Dictionary::tableName().'.id '
                .' AND '.DictionaryLocalizable::tableName().'.locale=:locale', [
                    ':locale' => $locale
                ]
            );

        if ($limit > 100) {
            $limit = 100;
        }

        if ($category_id) {
            $query->andWhere([
                'category_id' => $category_id
            ]);
        }

        if ($str) {
            $query->andWhere(['or',
                [
                    'and',
                    ['like', Dictionary::tableName().'.title', $str.'%', false],
                    ['is', DictionaryLocalizable::tableName().'.id', null]
                ],
                [
                    'and',
                    ['like', DictionaryLocalizable::tableName().'.title', $str.'%', false],
                    ['is not', DictionaryLocalizable::tableName().'.id', null]
                ]
            ]);
        }

        $query->orderBy('order');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => is_string($orderBy) ? ["order" => SORT_ASC, "title" => SORT_ASC] : $orderBy],
            'pagination'=>array(
                'pageSize' => $limit,
            ),
        ]);
    }

    public function actionOrderType($str = null, $orderBy = "order", $limit = 20) {
        return $this->actionIndex($str, DictionaryCategory::CATEGORY_ORDER_TYPE, $orderBy, $limit);
    }

    public function actionOrderDeliveryStatus($str = null, $orderBy = "order", $limit = 20) {
        return $this->actionIndex($str, DictionaryCategory::CATEGORY_ORDER_DELIVERY_STATUS, $orderBy, $limit);
    }
}
