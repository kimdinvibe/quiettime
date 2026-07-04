<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "note".
 *
 * @property int $id
 * @property string $date
 * @property int $type
 * @property resource $content
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Note extends \yii\db\ActiveRecord
{   
    const TYPE_COMMON = 1;
    const TYPE_RESPONSE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'note';
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
            [['type', 'user_id', 'created_at', 'updated_at', 'type'], 'integer'],
            [['content'], 'string'],
            [['user_id'], 'required'],
            [['date'], 'string', 'max' => 34],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'date' => Yii::t('common', 'Date'),
            'type' => Yii::t('common', 'Type'),
            'content' => Yii::t('common', 'Content'),
            'user_id' => Yii::t('common', 'User ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
