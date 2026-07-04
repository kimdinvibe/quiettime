<?php

namespace frontend\modules\api\v1\resources;

use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserProfile extends \common\models\UserProfile
{
    public function fields()
    {
        $list = parent::fields();

        unset($list['user_id']);
        unset($list['avatar_path']);
        unset($list['avatar_base_url']);

        $list = array_merge_recursive($list, [
            // 'user_id',
            // 'firstname',
            // 'middlename',
            // 'lastname',
            // 'locale',
            // 'avatar' => function ($model) {
            //     return $model->getAvatar()?\yii\helpers\Url::to($model->getAvatar(), true):null;
            // },

            'avatar' => function ($model) {
                if ($path = $model->getAvatar()) {
                    $path = Url::to([
                        '/file/thumb', 'source' => $path,
                        'width' => 1024,
                        'height' => 1024,
                        'crop' => true
                    ], true);

                    return Url::to($path, true);
                }

                return null;
            },

            'avatar_thumb' => function ($model) {
                if ($path = $model->getAvatar()) {
                    $path = Url::to([
                        '/file/thumb', 'source' => $path,
                        'width' => 150,
                        'height' => 150,
                        'crop' => true
                    ], true);

                    return Url::to($path, true);
                }

                return null;
            },
        ]);

        // $list['last_transaction_at'] = function ($model) {
        //     return $model->last_transaction_at != null ? \Yii::$app->formatter->asDate($model->last_transaction_at) : null;
        // };

        return $list;
    }
}
