<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webview".
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property resource $content
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Webview extends \yii\db\ActiveRecord
{
    const STATE_PRIVATE = 2;
    const STATE_PUBLIC = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webview';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'code'], 'required'],
            [['code'], 'unique'],
            [['content'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['code'], 'string', 'max' => 255],
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
            'code' => Yii::t('common', 'Code'),
            'content' => Yii::t('common', 'Content'),
            'status' => Yii::t('common', 'Status'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\WebviewQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\WebviewQuery(get_called_class());
    }

    static function getStateLabel($state = null) {
        $list = [
            self::STATE_PUBLIC => Yii::t('backend', 'Published'),
            self::STATE_PRIVATE => Yii::t('backend', 'Privated')
        ];

        if($state) {
            if (isset($list[$state])) {
                return $list[$state];
            }

            return '';
        }

        return $list;
    }
}
