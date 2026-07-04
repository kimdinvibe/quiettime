<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_payment".
 *
 * @property int $id
 * @property double $amount
 * @property int $period
 * @property string $currency
 * @property int $user_id
 * @property int $status
 * @property string $auth_token
 * @property int $payment_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class UserPayment extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_CREATED = 2;
    const STATUS_SUCCESSED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_RECURRENT = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_payment';
    }


    /**
    * @inheritdoc
    */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                //'updatedAtAttribute' => null
            ],
    ];
}


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'period', 'user_id'], 'required'],
            [['amount'], 'number'],
            [['period', 'user_id', 'status', 'created_at', 'updated_at', 'payment_log_id'], 'integer'],
            [['currency'], 'string', 'max' => 34],
            [['auth_token', 'confirmation_url', 'payment_id'], 'string', 'max' => 1024],
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
            'amount' => Yii::t('common', 'Amount'),
            'period' => Yii::t('common', 'Period'),
            'currency' => Yii::t('common', 'Currency'),
            'user_id' => Yii::t('common', 'User ID'),
            'status' => Yii::t('common', 'Status'),
            'auth_token' => Yii::t('common', 'Auth Token'),
            'payment_id' => Yii::t('common', 'Payment ID'),
            'created_at' => Yii::t('common', 'Create At'),
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

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserPaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserPaymentQuery(get_called_class());
    }


    /*
    * @param bool $insert whether this method called while inserting a record.
    * If `false`, it means the method is called while updating a record.
    * @return bool whether the insertion or updating should continue.
    * If `false`, the insertion or updating will be cancelled.
    */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            if ($insert) {
                $this->auth_token = Yii::$app->security->generateRandomString(34).'-'.md5(time().'-'.$this->amount);
            }
            
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

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentLog()
    {
        return $this->hasOne(PaymentLog::className(), ['id' => 'payment_log_id']);
    }


    public static function getNameStatus($code = null)
    {
        $list = [
            self::STATUS_NEW => Yii::t('common', 'New'),
            self::STATUS_CREATED => Yii::t('common', 'Created'),
            self::STATUS_SUCCESSED => Yii::t('common', 'Successed'),
            self::STATUS_CANCELED => Yii::t('common', 'Canceled'),
            self::STATUS_RECURRENT => Yii::t('common', 'Recurent')
        ];

        if($code){
            if(isset($list[$code])) {
                return $list[$code];
            }
        }
        else {
            return $list;
        }
    }

}
