<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_device".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $device_id
 * @property string $device_name
 * @property string $device_type
 * @property integer $state
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserDevice extends \yii\db\ActiveRecord
{
    const STATE_AVAILABLE = 1;
    const STATE_ARCHIVE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_device';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'state', 'created_at', 'updated_at'], 'integer'],
            [['device_id', 'device_type'], 'string', 'max' => 255],
            [['device_name'], 'string', 'max' => 1024],
            [['locale'], 'string', 'max' => 32],
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
            'device_id' => Yii::t('common', 'Device ID'),
            'device_type' => Yii::t('common', 'Device Type'),
            'device_name' => Yii::t('common', 'Device Name'),
            'state' => Yii::t('common', 'State'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(!$this->state){
                $this->state = self::STATE_AVAILABLE;
            }

            if($insert) {
                if(!$this->device_id &&  isset(\Yii::$app->request->headers['device-id'])) {
                    $this->device_id = \Yii::$app->request->headers['device-id'];
                }

                if(!$this->device_name &&  isset(\Yii::$app->request->headers['device-name'])) {
                    $this->device_name = \Yii::$app->request->headers['device-name'];
                }

                if(!$this->device_type &&  isset(\Yii::$app->request->headers['device-type'])) {
                    $this->device_type = \Yii::$app->request->headers['device-type'];
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
