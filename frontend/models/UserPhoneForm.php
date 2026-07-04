<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use frontend\helpers\SMSRU;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserPhoneForm extends Model
{
    public $phone;
    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'filter', 'filter' => 'trim'],
            ['phone', 'required'],
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'filter' => function ($query) {
                $query->andWhere(['!=', 'status', User::STATUS_REGISTRATION]);
                $query->andWhere(['!=', 'status', User::STATUS_REGISTRATION_BY_PHONE]);
            }],
            ['phone', 'string', 'max' => 34],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => Yii::t('frontend', 'Phone'),
        ];
    }

    public function setModel($model)
    {
        $this->phone = $model->phone;
        return $this->model;
    }

    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }
        return $this->model;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            $model->phone = $this->phone;

            $emptyPrefix = '${new}$';
            $model->status = User::STATUS_REGISTRATION_BY_PHONE;
            $model->username = $emptyPrefix
                . md5(time() . Yii::$app->getSecurity()->generateRandomString(64))
                . Yii::$app->getSecurity()->generateRandomString(8);
            $model->email =  $model->username . '@system.com';
            $model->phone_code = '';

            for ($i = 0; $i < 4; $i++) {
                $model->phone_code .= rand(0, 9);
                // for test
                // $model->phone_code = '1111';
            }

            $model->setPassword($emptyPrefix . substr(md5(time()), 0, 16));

            if ($model->validate()) {
                if ($model->save() && $isNewRecord) {

                    $model->afterSignup([
                        'firstname' => Yii::t('api', 'New'),
                        'lastname' => Yii::t('api', 'User')
                    ], User::ROLE_CLIENT);

                    $this->sendCodeToUser($model->phone, $model->phone_code);
                }

                return !$model->hasErrors();
            }
        }

        return null;
    }

    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getModel(), 0)) {
                return true;
            }
        }
        return false;
    }

    public function sendCodeToUser($phone, $code)
    {
        return SMSRU::sendSms($phone, $code);
    }
}
