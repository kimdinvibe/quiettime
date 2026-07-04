<?php

namespace frontend\modules\api\v1\controllers;


use frontend\components\ApiController;
use frontend\modules\api\v1\resources\StaticInfo;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class StaticController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\StaticInfo';

    protected function verbs()
    {
        return [
            // 'index' => ['GET'],
            // 'view' => ['GET'],
            // 'count-new' => ['GET'],
            // 'user-view' => ['POST']
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
            // 'options' => [
            //     'class' => 'yii\rest\OptionsAction'
            // ]
        ];
    }

    public function actionIndex()
    {
        try {
            return StaticInfo::findOne(1);
        } catch (\Exception $e) {
        }
    }
}
