<?php

namespace frontend\modules\api\v1\resources;

use common\models\ArticleView;
use common\models\Bible;
use common\models\TaskUser;
use common\models\TaskVerse;
use Yii;
use yii\db\Query;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Task extends \common\models\Task
{
    public function fields()
    {
        $list = parent::fields();

        $list['created_at'] = function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);
        };

        $list['video_url'] = function ($model) {
            if ($model->video_url) {
                $video_url = explode("?v=", $model->video_url);

                if (count($video_url) == 2) {
                    $video_url = $video_url[1];

                    return explode("&", $video_url)[0];
                }
            }

            return null;
        };

        $list['updated_at'] = function ($model) {
            return $model->updated_at ? \Yii::$app->formatter->asDatetime($model->updated_at) : null;
        };

        $list['verses'] = function ($model) {
            return $model->verses;
        };

        $list['is_finished'] = function ($model) {
            if (Yii::$app->user->isGuest) {
                return false;
            }

            return $this->getUser(Yii::$app->user->id) ? true : false;
        };

        $list['answer_1'] = function ($model) {
            if (Yii::$app->user->isGuest) {
                return "";
            }

            $model = $this->getUser(Yii::$app->user->id);
            
            return $model ? $model->answer_1 : "";
        };

        $list['answer_2'] = function ($model) {
            if (Yii::$app->user->isGuest) {
                return "";
            }

            $model = $this->getUser(Yii::$app->user->id);
            
            return $model ? $model->answer_2 : "";
        };
        

        $list['notes'] = function ($model) {
            return Note::find()->where([
                'date' => $model->date,
                'user_id' => Yii::$app->user->id,
                'type' => Note::TYPE_COMMON,
            ])->all();
        };

        return $list;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerses()
    {
        return (new Query())
            ->select([
                'tv.id as task_verse_id',
                'b.id as bible_id',
                'b.chapter',
                'b.text',
                'b.verse',
                'b.book_id',
                'b.book_name',
            ])
            ->from(TaskVerse::tableName() . ' tv')
            ->where([
                'tv.task_id' => $this->id,
            ])
            ->innerJoin(Bible::tableName() . ' b', 'b.id=tv.bible_id')
            ->orderBy(['tv.id' => SORT_ASC])
            ->all();

        // return $this->hasMany(Bible::className(), ['id' => 'bible_id'])
        // // ->via('taskVerses', ['task_id' => 'id'])
        // ->via('taskVerses')
        // ->orderBy(['taskVerses.id' => SORT_ASC])
        // ;
    }

    public function getUser($user_id)
    {
        return  TaskUser::find()->where([
            'task_id' => $this->id,
            'user_id' => $user_id,
        ])->one();
    }
}
