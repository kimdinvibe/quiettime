<?php

namespace frontend\modules\api\v1\resources;

use common\models\DictionaryLocalizable;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Dictionary extends \common\models\Dictionary
{
    public function fields()
    {
        return [
            'id',
            'category_id',
            'title' => function ($model) {
                return $model->localizableTitle ? $model->localizableTitle->title : $model->title;
            },
            'code',
            'parent_id',
            'data' => function ($model) {
                return $model->data ? $model->data : null;
            },
        ];
    }

    public function extraFields()
    {
        return ['category'];
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
    public function getLocalizableTitle()
    {
        return $this->hasOne(DictionaryLocalizable::className(), ['dictionary_id' => 'id'])->andWhere([
            DictionaryLocalizable::tableName() . '.locale' => \Yii::$app->language
        ]);
    }
}
