<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "attachment".
 *
 * @property integer $id
 * @property string $class
 * @property integer $item_id
 * @property string $path
 * @property string $base_url
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property integer $created_at
 * @property integer $order
 * @property integer $author_id
 * @property integer $state
 */
class Attachment extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_REMOVED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachment';
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
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'path'], 'required'],
            [['item_id', 'size', 'created_at', 'order', 'author_id', 'state'], 'integer'],
            [['class'], 'string', 'max' => 1024],
            [['path', 'base_url', 'type', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'class' => Yii::t('common', 'Class'),
            'item_id' => Yii::t('common', 'Item ID'),
            'path' => Yii::t('common', 'Path'),
            'base_url' => Yii::t('common', 'Base Url'),
            'type' => Yii::t('common', 'Type'),
            'size' => Yii::t('common', 'Size'),
            'name' => Yii::t('common', 'Name'),
            'created_at' => Yii::t('common', 'Created At'),
            'order' => Yii::t('common', 'Order'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AttachmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AttachmentQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (!$this->author_id && !Yii::$app->user->isGuest) {
                    $this->author_id = Yii::$app->user->id;
                }
            }

            return true;
        }

        return false;
    }

    public function getFullPath(){
        return $this->base_url.'/'.$this->path;
    }

    public function getFullPathThumb($width = 1024, $height = null, $crop = false){
        if ($path = $this->getFullPath()) {
            return Url::to(str_replace("panel/", "",  Yii::$app->urlManagerFrontend->createAbsoluteUrl([
                'file/thumb',
                'source' => $path,
                'width' => $width,
                'height' => $height,
                'crop' => $crop
            ])), true);
        }
    }
}
