<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "article_view".
 *
 * @property int $id
 * @property int $article_id
 * @property int $user_id
 * @property int $created_at
 *
 * @property Article $article
 * @property User $user
 */
class ArticleView extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_view';
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
            [['article_id', 'user_id', 'created_at'], 'integer'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
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
            'article_id' => Yii::t('common', 'Article ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
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
     * @return \common\models\query\ArticleViewQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ArticleViewQuery(get_called_class());
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
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DRAFT => Yii::t('common', 'Draft'),
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
