<?php

namespace frontend\modules\api\v1\resources;

use common\models\ArticleView;
use common\models\Bible;
use common\models\TaskUser;
use common\models\TaskVerse;
use Yii;
use yii\db\Query;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserPayment extends \common\models\UserPayment
{
    public function fields()
    {
        $list = [
            'auth_token',
            'status'
        ];

        return $list;
    }
}
