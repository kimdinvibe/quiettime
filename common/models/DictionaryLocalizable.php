<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dictionary_localizable".
 *
 * @property int $id
 * @property int $dictionary_id
 * @property string $title
 * @property string $locale
 * @property int $created_at
 */
class DictionaryLocalizable extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary_localizable';
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
            [['dictionary_id'], 'required'],
            [['dictionary_id', 'created_at'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['locale'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'dictionary_id' => Yii::t('common', 'Dictionary ID'),
            'title' => Yii::t('common', 'Title'),
            'locale' => Yii::t('common', 'Locale'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
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

    public static function getNameStatus($code = null)
    {
        $list = [
            self::STATUS_ACTIVE => Yii::t('common', 'Status new')
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
