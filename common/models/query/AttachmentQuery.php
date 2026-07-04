<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Attachment]].
 *
 * @see \common\models\Attachment
 */
class AttachmentQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            \common\models\Attachment::tableName().'.state' => \common\models\Attachment::STATUS_ACTIVE
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\Attachment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Attachment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
