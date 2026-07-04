<?php

namespace common\models\query;
use common\models\Dictionary;
use common\models\Request;
use common\models\User;

/**
 * This is the ActiveQuery class for [[\common\models\Request]].
 *
 * @see \common\models\Request
 */
class UserQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(User::tableName().'.status='.User::STATUS_ACTIVE);
    }

    public function noDeleted() {
        return $this->andWhere(User::tableName().'.status!='.User::STATUS_DELETED);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Request[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Request|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
