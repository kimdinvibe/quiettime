<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_log".
 *
 * @property int $id
 * @property string $type
 * @property string $event
 * @property string $object_id
 * @property string $status
 * @property double $amount
 * @property string $currency
 * @property resource $object
 */
class PaymentLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_log';
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
            [['amount'], 'number'],
            [['object'], 'string'],
            [['type'], 'string', 'max' => 34],
            [['event', 'object_id', 'status'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'type' => Yii::t('common', 'Type'),
            'event' => Yii::t('common', 'Event'),
            'object_id' => Yii::t('common', 'Object ID'),
            'status' => Yii::t('common', 'Status'),
            'amount' => Yii::t('common', 'Amount'),
            'currency' => Yii::t('common', 'Currency'),
            'object' => Yii::t('common', 'Object'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\PaymentLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PaymentLogQuery(get_called_class());
    }
}
