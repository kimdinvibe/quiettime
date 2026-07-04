<?php

namespace frontend\modules\api\v1\resources;

use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserMiniListItemWithRole extends UserMiniListItem
{
    public function fields()
    {
        $list = parent::fields();
        $list['role'] = function ($model) {
            return $model->role ?? null;
        };

        return $list;
    }
}
