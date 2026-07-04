<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\ArticleView]].
 *
 * @see \common\models\ArticleView
 */
class ArticleViewQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]='.\common\models\ArticleView::STATUS_ACTIVE);
    }

    public function draft()
    {
        return $this->andWhere('[[status]]='.\common\models\ArticleView::STATUS_DRAFT);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\ArticleView[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\ArticleView|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
