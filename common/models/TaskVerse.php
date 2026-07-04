<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "task_verse".
 *
 * @property int $id
 * @property int $task_id
 * @property int $bible_id
 * @property int $created_at
 *
 * @property Task $task
 * @property Bible $bible
 */
class TaskVerse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_verse';
    }

       /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'bible_id'], 'required'],
            [['task_id', 'bible_id', 'created_at'], 'integer'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['bible_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bible::className(), 'targetAttribute' => ['bible_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'task_id' => Yii::t('common', 'Task ID'),
            'bible_id' => Yii::t('common', 'Bible ID'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBible()
    {
        return $this->hasOne(Bible::className(), ['id' => 'bible_id']);
    }
}
