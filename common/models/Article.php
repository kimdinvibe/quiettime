<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use trntv\filekit\behaviors\UploadBehavior;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $title
 * @property string $image_path
 * @property string $image_base_url
 * @property string $text
 * @property resource $content
 * @property int $status
 * @property int $author_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $picture
 *
 * @property User $author
 */
class Article extends \yii\db\ActiveRecord
{
    public $picture;
    
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
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
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'image_path',
                'baseUrlAttribute' => 'image_base_url'
            ],
    ];
}


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'content'], 'string'],
            [['status', 'author_id', 'created_at', 'updated_at', 'sent_to_phone_at'], 'integer'],
            [['title', 'image_path', 'image_base_url'], 'string', 'max' => 1024],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['picture'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'title' => Yii::t('common', 'Title'),
            'image_path' => Yii::t('common', 'Image Path'),
            'image_base_url' => Yii::t('common', 'Image Base Url'),
            'text' => Yii::t('common', 'Text'),
            'content' => Yii::t('common', 'Content'),
            'status' => Yii::t('common', 'Status'),
            'author_id' => Yii::t('common', 'Author ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'picture' => Yii::t('common', 'Picture'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ArticleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ArticleQuery(get_called_class());
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

    public function getThumb($default = null)
    {
        return $this->image_path
            ? Yii::getAlias($this->image_base_url . '/' . $this->image_path)
            : $default;
    }

}
