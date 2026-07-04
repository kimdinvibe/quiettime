<?php

namespace frontend\modules\api\v1\resources;

use common\models\ArticleView;
use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class StaticInfo extends \common\models\StaticInfo
{
    public function fields()
    {
        // $list = parent::fields();
        $list = [];

        $list['logo_full'] = function ($model) {
            if ($path = $model->getLogo()) {
                // $path = Url::to([
                //     '/file/thumb', 'source' => $path,
                //     'width' => 1024,
                //     'height' => 1024,
                //     'crop' => true
                // ], true);
                $path = Url::to([
                    $path,

                ], true);

                return Url::to($path, true);
            }

            return null;
        };

        return $list;
    }
}
