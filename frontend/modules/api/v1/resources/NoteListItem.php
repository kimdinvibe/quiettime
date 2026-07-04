<?php

namespace frontend\modules\api\v1\resources;

use common\models\ArticleView;
use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class NoteListItem extends \common\models\Note
{
    public function fields()
    {
        $list = parent::fields();

        $list['created_at'] = function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);
        };

        $list['content'] = function ($model) {
            return mb_substr($model->content, 0, 255);
        };

        $list['updated_at'] = function ($model) {
            return $model->updated_at ? \Yii::$app->formatter->asDatetime($model->updated_at) : null;
        };

        return $list;
    }
}
