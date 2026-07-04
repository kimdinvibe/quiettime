<?php

namespace frontend\modules\api\v1\resources;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class DictionaryCategory extends \common\models\DictionaryCategory
{
    public function fields()
    {
        return [
            "id",
            "title",
            "name",
            "parent_id",
            "code",
        ];
    }

    static function getLabel($code)
    {
        switch ($code) {
            case DictionaryCategory::CATEGORY_ORDER_DELIVERY_STATUS:
                $label = "Order Delivery Status";
                break;

            case DictionaryCategory::CATEGORY_ORDER_TYPE:
                $label = "Order Type";
                break;

            case DictionaryCategory::CATEGORY_LANGUAGE:
                $label = "Language";
                break;
        }

        if ($label) {
            return \Yii::t('api', $label);
        }

        return null;
    }
}
