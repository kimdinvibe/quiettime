<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\UserProfile;
use common\models\RbacAuthAssignment;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    public $firstname;
    public $lastname;
    public $group;
    public $is_premium;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at', 'is_premium'], 'integer'],
            [['username', 'phone', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'firstname', 'lastname', 'group'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()
            ->innerJoinWith(['userProfile', 'rbacAuthAssignment'])
            //->innerJoin('rbac_auth_assignment', 'rbac_auth_assignment.user_id='.User::tableName().'.id')
            ->where(['!=', User::tableName() . '.id', Yii::$app->params['systemUser']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'id' => SORT_DESC
                ],
                // 'attributes' => [
                //     // 'userProfile.premium_at'
                //     // 'username'
                // ],
            ]
        ]);

        $dataProvider->sort->attributes['firstname'] = [
            'asc' => [UserProfile::tableName() . '.firstname' => SORT_ASC],
            'desc' => [UserProfile::tableName() . '.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lastname'] = [
            'asc' => [UserProfile::tableName() . '.lastname' => SORT_ASC],
            'desc' => [UserProfile::tableName() . '.lastname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['group'] = [
            'asc' => [RbacAuthAssignment::tableName() . '.item_name' => SORT_ASC],
            'desc' => [RbacAuthAssignment::tableName() . '.item_name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logged_at' => $this->logged_at
        ]);

        if ($this->is_premium == 2) {
            $query->andWhere(['is not', 'user_profile.premium_at', null]);
            $query->andWhere(['>=', 'user_profile.premium_at', time()]);
        } else if ($this->is_premium == 1) {
            $query->andWhere(['is', 'user_profile.premium_at', null]);
        }

        $query
            //            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', UserProfile::tableName() . '.firstname', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', UserProfile::tableName() . '.firstname', $this->firstname])
            ->andFilterWhere(['like', UserProfile::tableName() . '.lastname', $this->lastname])
            ->andFilterWhere([RbacAuthAssignment::tableName() . '.item_name' => $this->group]);

        return $dataProvider;
    }
}
