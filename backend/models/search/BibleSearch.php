<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Bible;

/**
 * BibleSearch represents the model behind the search form about `common\models\Bible`.
 */
class BibleSearch extends Bible
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'chapter', 'verse'], 'integer'],
            [['text', 'translation_id', 'book_id', 'book_name'], 'safe'],
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
        $query = Bible::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $loaded = $this->load($params) && $this->validate();

        // Default to the active translation so the grid isn't a mix of old
        // and new data; an admin can still explicitly search a different
        // translation_id (e.g. RST) to look up old entries.
        $translationId = ($loaded && $this->translation_id !== null && $this->translation_id !== '')
            ? $this->translation_id
            : Bible::ACTIVE_TRANSLATION_ID;
        $query->andFilterWhere(['like', 'translation_id', $translationId]);

        if (!$loaded) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'chapter' => $this->chapter,
            'verse' => $this->verse,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'book_id', $this->book_id])
            ->andFilterWhere(['like', 'book_name', $this->book_name]);

        return $dataProvider;
    }
}
