<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserNotification]].
 *
 * @see \common\models\UserNotification
 */
class UserNotificationQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]='.\common\models\UserNotification::STATUS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\UserNotification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\UserNotification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
