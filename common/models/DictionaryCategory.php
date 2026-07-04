<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dictionary_category".
 *
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Dictionary[] $dictonaries
 */
class DictionaryCategory extends \yii\db\ActiveRecord
{
    const CATEGORY_ORDER_DELIVERY_STATUS = 1;
    const CATEGORY_ORDER_TYPE = 2;
    const CATEGORY_LANGUAGE = 3;
    
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary_category';
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
            [['created_at', 'updated_at'], 'integer'],
            [['title', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'title' => Yii::t('common', 'Title'),
            'name' => Yii::t('common', 'Name'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictonaries()
    {
        return $this->hasMany(Dictionary::className(), ['category_id' => 'id']);
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

    public static function getNameStatus($code = null)
    {
        $list = [
            self::STATUS_ACTIVE => Yii::t('common', 'Status new')
        ];

        if($code){
            if(isset($list[$code])) {
                return $list[$code];
            }
        } else {
            return $list;
        }
    }

    static function getIdByParentAndName($paent_id, $name) {
        return self::find()->select(['id'])->where(['parent_id' => $paent_id, 'name' => mb_strtolower(trim($name), 'UTF-8')])->scalar();
    }

    static function getByParentAndName($paent_id, $name) {
        return self::find()->where(['parent_id' => $paent_id, 'name' => mb_strtolower(trim($name), 'UTF-8')])->one();
    }
}
