<?php

namespace frontend\modules\api\v1\resources;

use common\models\ArticleView;
use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Article extends \common\models\Article
{
    public function fields()
    {
        $list = parent::fields();

        $list['content'] = function ($model) {
            return  strip_tags(str_replace("<br>", "\n", preg_replace('#<p(.*?)>(.*?)</p>#is', "$2\n", $model->content)));
        };

        $list['status'] = function ($model) {
            return $model->status;
        };

        $list['status_label'] = function ($model) {
            return \common\models\Article::getNameStatus($model->status);
        };

        $list['created_at'] = function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);
        };

        $list['updated_at'] = function ($model) {
            return $model->updated_at ? \Yii::$app->formatter->asDatetime($model->updated_at) : null;
        };

        $list['isread'] = function ($model) {
            return !Yii::$app->user->isGuest && ($model->created_at <= Yii::$app->user->identity->created_at || $model->viewUser) ? true : false;
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

        $list['author'] = function ($model) {
            return $model->author ?? null;
        };


        $list['views'] = function ($model) {
            return 0;
        };

        // $list['link'] = function ($model) {
        //     return Url::to(['/message/view', 'id' => $model->id], true);
        // };


        return $list;
    }

    public function getAuthor()
    {
        return $this->hasOne(UserMiniListItem::className(), ['id' => 'author_id']);
    }

    public function getViewUser()
    {
        return $this->hasOne(ArticleView::className(), ['article_id' => 'id'])->andWhere([
            'user_id' => Yii::$app->user->isGuest ? -1 : Yii::$app->user->id
        ]);
    }
}
