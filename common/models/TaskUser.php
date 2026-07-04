<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "task_user".
 *
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property int $created_at
 *
 * @property User $user
 * @property Task $task
 */
class TaskUser extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_user';
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
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'task_id', 'created_at'], 'integer'],
            [['answer_1', 'answer_2'], 'string'],
            [['task_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'task_id' => Yii::t('common', 'Task ID'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TaskUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TaskUserQuery(get_called_class());
    }


    /*
    * @param bool $insert whether this method called while inserting a record.
    * If `false`, it means the method is called while updating a record.
    * @return bool whether the insertion or updating should continue.
    * If `false`, the insertion or updating will be cancelled.
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        parent::afterDelete();
    }
}
