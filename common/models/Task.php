<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $date
 * @property string $video_url
 * @property string $essay_title
 * @property resource $essay
 * @property resource $meditation
 * @property resource $prayer
 * @property resource $application_1
 * @property resource $application_2
 * @property int $created_at
 * @property int $updated_at
 *
 * @property TaskApplication[] $taskApplications
 * @property TaskVerse[] $taskVerses
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

       /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['meditation_title_1', 'meditation_1', 'meditation_title_2', 'meditation_2', 'prayer', 'application_1', "application_2", "title", "descr", "essay_title", "essay"], 'string'],
            [['created_at', 'updated_at', 'date_at'], 'integer'],
            [['date'], 'string', 'max' => 34],
            [['video_url'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'date' => Yii::t('common', 'Дата'),
            'title' => Yii::t('common', 'Заголовок'),
            'descr' => Yii::t('common', 'Описание'),
            'video_url' => Yii::t('common', 'Ссылка на Youtube'),
            'meditation_1' => Yii::t('common', 'Размышление абзац 1'),
            'meditation_title_1' => Yii::t('common', 'Размышлени заголовок 1'),
            'meditation_2' => Yii::t('common', 'Размышление абзац 2'),
            'meditation_title_2' => Yii::t('common', 'Размышлени заголовок 2'),
            'essay_title' => Yii::t('common', 'Эссе заголовок'),
            'essay' => Yii::t('common', 'Эссе'),
            'prayer' => Yii::t('common', 'Молитва'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            // if($date != null) {
            //     $this->date_at = str
            // }
            
            
            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskApplications()
    {
        return $this->hasMany(TaskApplication::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskVerses()
    {
        return $this->hasMany(TaskVerse::className(), ['task_id' => 'id']);
    }
}
