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
class TaskMiniList extends \common\models\Task
{
    public function fields()
    {
        $list = [
            'date',
            'title'
        ];

        $list['is_finished'] = function ($model) {
            if (Yii::$app->user->isGuest){
                return false;
            }
            
            return TaskUser::find()->where([
                'task_id' => $model->id,
                'user_id' => Yii::$app->user->id,
            ])->one() ? true : false;
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
}
