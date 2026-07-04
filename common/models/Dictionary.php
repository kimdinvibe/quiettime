<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dictionary".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $parent_id
 * @property string $title
 * @property string $description
 * @property resource $data
 * @property string $slug
 * @property string $code
 * @property integer $status
 * @property integer $order
 * @property integer $author_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $base_url
 * @property string $path
 *
 * @property DictionaryCategory $category
 */
class Dictionary extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;

    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true
                //'immutable' => true
            ],
            'image' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'image',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'parent_id', 'status', 'order', 'author_id', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'required'],
            [['description', 'base_url', 'path'], 'string'],
            [['title', 'slug', 'code'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 1024],
            [['slug'], 'unique'],
            [['data', 'image'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => DictionaryCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'category_id' => Yii::t('common', 'Category ID'),
            'parent_id' => Yii::t('common', 'Parent ID'),
            'title' => Yii::t('common', 'Title'),
            'description' => Yii::t('common', 'Description'),
            'data' => Yii::t('common', 'Data'),
            'slug' => Yii::t('common', 'Slug'),
            'code' => Yii::t('common', 'Code'),
            'status' => Yii::t('common', 'Status'),
            'order' => Yii::t('common', 'Order'),
            'author_id' => Yii::t('common', 'Author ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'image' => Yii::t('common', 'Image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(DictionaryCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocalizableString()
    {
        return $this->hasMany(DictionaryLocalizable::className(), ['dictionary_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocalizableTitle()
    {
        return $this->hasOne(DictionaryLocalizable::className(), [
            'dictionary_id' => 'id',
        ])->andWhere([
            'locale' => \Yii::$app->language
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\query\DictionaryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\DictionaryQuery(get_called_class());
    }

    /*
    * @param bool $insert whether this method called while inserting a record.
    * If `false`, it means the method is called while updating a record.
    * @return bool whether the insertion or updating should continue.
    * If `false`, the insertion or updating will be cancelled.
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!is_a(Yii::$app, 'yii\console\Application')) {
                if (!$this->status && $insert && !Yii::$app->user->isGuest) {
                    $this->status = self::STATUS_NEW;
                }

                if (!$this->author_id && $insert && !Yii::$app->user->isGuest) {
                    $this->author_id = Yii::$app->user->id;
                }
            }

            if ($this->data) {
                $this->data = json_encode($this->data ? $this->data : []);
            }

            return true;
        }

        return false;
    }

    public static function getNameStatus($code = null)
    {
        $list = [
            self::STATUS_NEW => Yii::t('common', 'Status new')
        ];

        if ($code) {
            if (isset($list[$code])) {
                return $list[$code];
            }
        } else {
            return $list;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($changedAttributes) {
            // if ($insert || (isset($changedAttributes['data']) && $changedAttributes['data'])) {
                if (!$insert) {
                    DictionaryData::deleteAll(['dictionary_id' => $this->primaryKey]);
                }


                $data = json_decode($this->data);

                if ($data) {
                    foreach ($data as $key => $item) {
                        if (!(is_array($item) || is_object($item))) {
                            $model = (new DictionaryData([
                                'dictionary_id' => $this->primaryKey,
                                'value' => $item,
                                'name' => $key
                            ]));

                            $model->save();
                        }
                    }
                }
            // }
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->data) {
            $this->data = json_decode($this->data);
        }
    }

    public static function getItemByCode($code, $where = null)
    {
        $query =  self::find();

        if ($where) {
            $query->where($where);
        }

        $query->andWhere(['code' => $code]);

        return $query->one();
    }

    public static function getItemByCodeAndCategory($code, $category_id,  $where = null)
    {
        $query =  self::find();

        if ($where) {
            $query->where($where);
        }

        if ($category_id) {
            $query->andWhere(['category_id' => $category_id]);
        }

        $query->andWhere(['code' => $code]);

        return $query->one();
    }

    public function getFullPath()
    {
        return $this->base_url . '/' . $this->path;
    }

    public function getFullPathThumb($width = 1024, $height = null, $crop = false)
    {
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
