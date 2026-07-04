<?php

namespace frontend\modules\api\v1\resources;

use Yii;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserMiniListItem extends User
{
    public function fields()
    {
        $list = [
            'id',
            // 'username', 
            'created_at' => function ($model) {
                return \Yii::$app->formatter->asDatetime($model->created_at);
            },
            // 'email',
            'status',
            'fio' => function ($model) {
                if ($model->userProfile) {
                    return $model->userProfile->firstname . ($model->userProfile->middlename ? ' ' . $model->userProfile->middlename : '') . ($model->userProfile->lastname ? ' ' . $model->userProfile->lastname : '');
                }

                return null;
            },
            'avatar' => function ($model) {
                if ($model->userProfile) {
                    if ($path = $model->userProfile->getAvatar()) {
                        $path = Url::to([
                            '/file/thumb', 'source' => $path,
                            'width' => 1024,
                            'height' => 1024,
                            'crop' => true
                        ], true);

                        return Url::to($path, true);
                    }
                }

                return null;
            },
            'avatar_thumb' => function ($model) {
                if ($model->userProfile) {
                    if ($path = $model->userProfile->getAvatar()) {
                        $path = Url::to([
                            '/file/thumb', 'source' => $path,
                            'width' => 150,
                            'height' => 150,
                            'crop' => true
                        ], true);

                        return Url::to($path, true);
                    }
                }

                return null;
            },

        ];

        return $list;
    }

    public function extraFields()
    {
        return [
            'userProfile',
            'access_token',
            'role',
        ];
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    public function getRole()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->getId());
        return $roles[array_keys($roles)[0]]->name;
    }
}
