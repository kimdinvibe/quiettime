<?php

namespace frontend\modules\api\v1\resources;

use common\models\ChatAccess;
use common\models\ChatRead;
use common\models\RequestInvitation;
use common\models\RequestMate;
use common\models\UserBlock;
use common\models\UserDictionaryValue;
use common\models\UserLike;
use common\models\UserLocation;
use Yii;
use yii\db\Query;
use yii\helpers\Url;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class User extends \common\models\User
{
    public function fields()
    {
        $list = [
            'id',
            'username',
            'created_at',
            'email',
            'phone',
            'status',
            'status_label' => function ($model) {
                return self::getStatuses($this->status);
            },
            'created_at' => function ($model) {
                return \Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updated_at' => function ($model) {
                return $model->updated_at ? \Yii::$app->formatter->asDatetime($model->updated_at) : null;
            },
            'logged_at' => function ($model) {
                return $model->logged_at ? \Yii::$app->formatter->asDatetime($model->logged_at) : null;
            },
            'firstname' => function ($model) {
                return Yii::t('api', $model->userProfile ? $model->userProfile->firstname : null);
            },
            'middlename' => function ($model) {
                return $model->userProfile ? $model->userProfile->middlename : null;
            },
            'lastname' => function ($model) {
                return $model->userProfile ? $model->userProfile->lastname : null;
            },
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
            'is_deleted' => function ($model) {
                return $model->status == self::STATUS_DELETED;
            },
            'is_premium' => function ($model) {
                return $model->userProfile
                    && (
                        $model->userProfile->premium_unlimited ||
                        ($model->userProfile->premium_at
                        && $model->userProfile->premium_at >= time()
                        )
                    )  ? true : false;
            },
            'premium_at' => function ($model) {
                return $model->userProfile && $model->userProfile->premium_at ? \Yii::$app->formatter->asDate($model->userProfile->premium_at) : null;
            },
            'premium_unlimited' => function ($model) {
                return $model->userProfile && $model->userProfile->premium_unlimited ? true : false;
            },
            'premium_period' => function ($model) {
                return $model->userProfile
                    && $model->userProfile->premium_period
                    ? $model->userProfile->premium_period : null;
            },
            'premium_next_time' => function ($model) {
                if ($model->userProfile && $model->userProfile->premium_at) {
                    if ($model->userProfile->premium_period) {
                        \Yii::$app->formatter->asDate(strtotime($model->userProfile->premium_period, $model->userProfile->premium_period ?? time()));
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
            'notifications',
            'isFullDetails',
            'role'
        ];
    }

    public function getIsFullDetails()
    {
    }

    public function getNotifications()
    {
        $list = UserNotification::find()->where([
            'user_id' => $this->id
        ])->active()->all();

        foreach ([
            UserNotification::CATEGORY_INCOMING_MESSAGE,
            UserNotification::CATEGORY_REQUEST_UPDATE,
            UserNotification::CATEGORY_NEW_REVIEWS_ON_YOU,
            UserNotification::CATEGORY_NEW_REQUEST_NEARBY
        ] as $category) {
            foreach ([
                UserNotification::TYPE_EMAIL,
                UserNotification::TYPE_PUSH,
                UserNotification::TYPE_CHAT,
            ] as $item) {
                $result[$category][$item] = false;
            }
        }

        foreach ($list as $item) {
            $result[$item->category][$item->type] = true;
        }

        return $result;
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
