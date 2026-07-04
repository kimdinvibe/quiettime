<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserPayment;

/**
 * YserPaymentSearch represents the model behind the search form about `common\models\UserPayment`.
 */
class UserPaymentSearch extends UserPayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'period', 'user_id', 'status', 'payment_log_id', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['currency', 'auth_token', 'payment_id', 'confirmation_url'], 'safe'],
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
        $query = UserPayment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'period' => $this->period,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'payment_log_id' => $this->payment_log_id,
        ]);

        $query->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'auth_token', $this->auth_token])
            ->andFilterWhere(['like', 'payment_id', $this->payment_id])
            ->andFilterWhere(['like', 'confirmation_url', $this->confirmation_url]);

        if ($this->created_at) {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->created_at)]);
        }

        if ($this->updated_at) {
            $query->andFilterWhere(['<', 'updated_at', strtotime($this->updated_at)]);
        }




        return $dataProvider;
    }
}
