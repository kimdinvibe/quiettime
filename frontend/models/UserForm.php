<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    public $oauth_client;
    public $oauth_client_user_id;

    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass'=>'\common\models\User', 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                } else {
                    $query->andWhere(['!=', 'status', User::STATUS_REGISTRATION]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=> '\common\models\User', 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                } else {
                    $query->andWhere(['!=', 'status', User::STATUS_REGISTRATION]);
                }
            }],

            [['password'], 'required', 'on'=>'create'],
            ['password', 'string', 'min' => 6],

            [['status'], 'integer'],
            [['roles'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->authManager->getRoles(),
                    'name'
                )]
            ],
            [['oauth_client', 'oauth_client_user_id'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Username'),
            'email' => Yii::t('frontend', 'Email'),
            'password' => Yii::t('frontend', 'Password'),
            'roles' => Yii::t('frontend', 'Roles')
        ];
    }

    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->oauth_client = $model->oauth_client;
        $this->oauth_client_user_id = $model->oauth_client_user_id;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );
        return $this->model;
    }

    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }
        return $this->model;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            $model->username = $this->username;
            $model->email = $this->email;
            $model->status = $this->status;
            $model->oauth_client = $this->oauth_client;
            $model->oauth_client_user_id = $this->oauth_client_user_id;

            if ($this->password) {
                $model->setPassword($this->password);
            }

            if ($model->validate()) {
                if ($model->save() && $isNewRecord) {
                    $model->afterSignup();
                }
                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->getId());

                if ($this->roles && is_array($this->roles)) {
                    foreach ($this->roles as $role) {
                        $auth->assign($auth->getRole($role), $model->getId());
                    }
                }

                return !$model->hasErrors();
            }
        }

        return null;
    }

    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getModel(), 0)) {
                return true;
            }
        }
        return false;
    }
}
