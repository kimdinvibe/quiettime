<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Article]].
 *
 * @see \common\models\Article
 */
class ArticleQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]='.\common\models\Article::STATUS_ACTIVE);
    }

    public function draft()
    {
        return $this->andWhere('[[status]]='.\common\models\Article::STATUS_DRAFT);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Article[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Article|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
