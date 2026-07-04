<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StaticInfo;

/**
 * StaticInfoSearh represents the model behind the search form about `common\models\StaticInfo`.
 */
class StaticInfoSearh extends StaticInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['logo_path', 'logo_base_url'], 'safe'],
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
        $query = StaticInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'logo_path', $this->logo_path])
            ->andFilterWhere(['like', 'logo_base_url', $this->logo_base_url])
            ->andFilterWhere(['<', 'created_at', strtotime($this->created_at)])
            ->andFilterWhere(['<', 'updated_at', strtotime($this->updated_at)]);

        return $dataProvider;
    }
}
