<?php

namespace frontend\modules\api\v1\resources;

use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class ArticleListItem extends Article
{
    public function fields()
    {
        $list = [
            'id',
            'title',
            'text',
            'author',
            'status',
        ];

        $list['status_label'] = function ($model) {
            return \common\models\Article::getNameStatus($model->status);
        };

        $list['created_at'] = function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);
        };

        $list['isread'] = function ($model) {
            return !Yii::$app->user->isGuest && ($model->created_at <= Yii::$app->user->identity->created_at || $model->viewUser)?true:false;
        };

        $list['image'] = function ($model) {
            if ($path = $model->getThumb()) {
                $path = Url::to([
                    '/file/thumb', 'source' => $path,
                    'width' => 1024,
                    'height' => 580,
                    'crop' => true
                ], true);

                return Url::to($path, true);
            }

            return null;
        };

        return $list;
    }
}
