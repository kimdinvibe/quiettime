<?php

namespace frontend\modules\api\v1\resources;

use frontend\modules\api\v1\resources\UserProfile;
use frontend\modules\api\v1\resources\UserAgent;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Attachment extends \common\models\Attachment
{
    public $is_read_only = false;
    public $defaultSize = [
        'width' => '1024',
        //'height' => '745'
    ];

    public function fields() {
        //$list = parent::fields();
        $list = [
            'id',
            'name',
            'is_read_only'
            //'author',
        ];

        $list['created_at'] = function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);
        };

        $list['full_path'] = function ($model) {
            if ($path = $model->getFullPath()) {
                $path = Url::to([
                    '/file/thumb', 'source' => $path,
                    'width' => $this->defaultSize['width'],
                    'height' => $this->defaultSize['height'],
                    'crop' => true
                ], true);
            }

            return Url::to($path, true);
        };

        $list['is_author'] = function ($model) {
            return !\Yii::$app->user->isGuest && $model->author_id == \Yii::$app->user->id ? true : false;
        };

        if (in_array('author', explode(",", $_GET['expand']))) {
            $list[] = 'author';
        }

        return $list;
    }

    public function extraFields()
    {
        return ['author'];
    }

    public function getAuthor() {
        return $this->hasOne(UserMiniListItem::className(), ['id'=>'author_id'])->with(['userProfile']);
    }
}
