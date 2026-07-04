<?php

namespace frontend\modules\api\v1\resources;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class FileStorageItem extends \common\models\FileStorageItem
{
    public function fields()
    {
        return ['id',
            'type', 
            'size', 
            'name',
            'created_at',
            'url' => function ($model) {
                return \yii\helpers\Url::to($model->base_url.'/'.$model->path, true);
            },
        ];
    }
}
