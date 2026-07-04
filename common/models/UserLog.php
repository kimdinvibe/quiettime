<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $action
 * @property string $controller
 * @property string $headers
 * @property string $params
 * @property integer $created_at
 * @property integer $device_id
 * @property integer $device_type
 * @property integer $device_name
 * @property integer $device_locale
 */
class UserLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['headers', 'params'], 'string'],
            [['ip'], 'string', 'max' => 16],
            [['action', 'controller', 'device_id', 'device_type'], 'string', 'max' => 255],
            [['device_name'], 'string', 'max' => 1024],
            [['device_locale'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'ip' => Yii::t('common', 'Ip'),
            'device_id' => Yii::t('common', 'Device ID'),
            'device_locale' => Yii::t('common', 'Locale'),
            'device_type' => Yii::t('common', 'Device Type'),
            'device_name' => Yii::t('common', 'Device Name'),
            'action' => Yii::t('common', 'Action'),
            'controller' => Yii::t('common', 'Controller'),
            'headers' => Yii::t('common', 'Headers'),
            'params' => Yii::t('common', 'Params'),
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

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {

            if($insert) {
                $this->ip = Yii::$app->request->getUserIP();
            }

            return true;
        }

        return false;
    }
}
