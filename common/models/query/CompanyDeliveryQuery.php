<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CompanyDelivery]].
 *
 * @see \common\models\CompanyDelivery
 */
class CompanyDeliveryQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]='.\common\models\CompanyDelivery::STATUS_ACTIVE);
    }

    public function draft()
    {
        return $this->andWhere('[[status]]='.\common\models\CompanyDelivery::STATUS_DRAFT);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\CompanyDelivery[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\CompanyDelivery|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
