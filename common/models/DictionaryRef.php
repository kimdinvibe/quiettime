<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dictionary_ref".
 *
 * @property int $id
 * @property int $item_id
 * @property int $parent_id
 * @property int $created_at
 *
 * @property Dictionary $item
 * @property Dictionary $parent
 */
class DictionaryRef extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary_ref';
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
            [['item_id', 'parent_id', 'created_at'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'item_id' => Yii::t('common', 'Item ID'),
            'parent_id' => Yii::t('common', 'Parent ID'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'parent_id']);
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
