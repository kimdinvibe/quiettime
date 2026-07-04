<?php

namespace common\models\query;
use common\models\Webview;

/**
 * This is the ActiveQuery class for [[\common\models\Webview]].
 *
 * @see \common\models\Webview
 */
class WebviewQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'status' => Webview::STATE_PUBLIC
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Webview[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Webview|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
