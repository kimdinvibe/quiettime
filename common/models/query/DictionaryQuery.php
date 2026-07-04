<?php

namespace common\models\query;
use common\models\DictionaryCategory;

/**
 * This is the ActiveQuery class for [[\common\models\Dictionary]].
 *
 * @see \common\models\Dictionary
 */
class DictionaryQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]='.DictionaryCategory::STATUS_ACTIVE);
    }

    public function orderStatus()
    {
        return $this->andWhere('[[category_id]]='.DictionaryCategory::CATEGORY_ORDER_DELIVERY_STATUS);
    }

    public function orderType()
    {
        return $this->andWhere('[[category_id]]='.DictionaryCategory::CATEGORY_ORDER_TYPE);
    }

    public function language()
    {
        return $this->andWhere('[[category_id]]='.DictionaryCategory::CATEGORY_LANGUAGE);
    }

    /**
     * @inheritdoc
     * @return \common\models\Dictionary[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Dictionary|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
