<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_profile".
 *
 * @property string $logo_path
 * @property string $logo_base_url
 * @property string $profile
 *
 * @property User $user
 */
class StaticInfo extends \yii\db\ActiveRecord
{
    public $logo;

    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'logo',
                'pathAttribute' => 'logo_path',
                'baseUrlAttribute' => 'logo_base_url'
            ],
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%static_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'logo_path',
                'logo_base_url',
            ], 'string', 'max' => 255],
            [['logo_path', 'logo_base_url'], 'string', 'max' => 1024],
            [['logo'], 'safe'],
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
            'logo' => Yii::t('common', 'Логотип'),
            'profile' => Yii::t('common', 'Profile'),
        ];
    }

    public function getLogo($default = null)
    {
        return $this->logo_path
            ? Yii::getAlias($this->logo_base_url . '/' . $this->logo_path)
            : $default;
    }
}
