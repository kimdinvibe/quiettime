<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentLog;

/**
 * PaymentLogSearch represents the model behind the search form about `common\models\PaymentLog`.
 */
class PaymentLogSearch extends PaymentLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at'], 'integer'],
            [['type', 'event', 'object_id', 'status', 'currency', 'object'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = PaymentLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'event', $this->event])
            ->andFilterWhere(['like', 'object_id', $this->object_id])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'object', $this->object])
            ->andFilterWhere(['<', 'created_at', strtotime($this->created_at)]);

        return $dataProvider;
    }
}
