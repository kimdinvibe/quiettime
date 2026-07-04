<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property integer $locale
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $picture
 * @property string $avatar
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property string $profile
 *
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
    public $picture;
    public $passport;
    public $driver_license;
    public $selfi_passport;

    public function behaviors()
    {
        return [
            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url'
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [[
                'firstname', 'middlename', 'lastname',
                'avatar_path',
                'avatar_base_url',
            ], 'string', 'max' => 255],
            ['locale', 'default', 'value' => Yii::$app->language],
            //['locale', 'in', 'range' => array_keys(Yii::$app->params['availableLocales'])],
            ['locale', 'string'],
            [['picture', 'selfi_passport', 'driver_license', 'passport'], 'safe'],
            
            [['profile'], 'safe'],
            [['premium_at'], 'integer'],
            
            [['notification_time', 'notification_days'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'firstname' => Yii::t('common', 'Firstname'),
            'lastname' => Yii::t('common', 'Lastname'),
            'user_id' => Yii::t('common', 'User ID'),
            'locale' => Yii::t('common', 'Locale'),
            'picture' => Yii::t('common', 'Picture'),
            'profile' => Yii::t('common', 'Profile'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->session->setFlash('forceUpdateLocale');
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
    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }

    public function getFullName()
    {
        return $this->firstname || $this->middlename || $this->lastname ? implode(" ", [
            $this->firstname,
            $this->middlename,
            $this->lastname
        ]) :  $this->user->username;
    }

    public function getAvatar($default = null)
    {
        return $this->avatar_path
            ? Yii::getAlias($this->avatar_base_url . '/' . $this->avatar_path)
            : $default;
    }

    public function calculateEvaluationAndSave()
    {
        //
    }
}
