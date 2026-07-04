<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bible".
 *
 * @property int $id
 * @property int $chapter
 * @property int $verse
 * @property string $text
 * @property string $translation_id
 * @property string $book_id
 * @property string $book_name
 */
class Bible extends \yii\db\ActiveRecord
{
    /**
     * Translation used when picking verses for new Task entries.
     * Old entries keep pointing at whatever bible_id they already reference
     * (e.g. RST), so this only affects newly created/edited readings.
     */
    const ACTIVE_TRANSLATION_ID = 'JBL';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bible';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord && $this->translation_id === null) {
            $this->translation_id = self::ACTIVE_TRANSLATION_ID;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chapter', 'verse', 'text'], 'required'],
            [['chapter', 'verse'], 'integer'],
            [['text'], 'string'],
            [['translation_id', 'book_id'], 'string', 'max' => 8],
            [['book_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'chapter' => Yii::t('common', 'Chapter'),
            'verse' => Yii::t('common', 'Verse'),
            'text' => Yii::t('common', 'Text'),
            'translation_id' => Yii::t('common', 'Translation ID'),
            'book_id' => Yii::t('common', 'Book ID'),
            'book_name' => Yii::t('common', 'Book Name'),
        ];
    }
}
