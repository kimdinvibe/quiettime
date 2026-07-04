<?php

namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserCreateForm extends Model
{
    public $email;
    public $password;
    public $phone;

    public $firstname;
    public $lastname;
    public $address;

    public $manager_id;
    public $manager_rate;
    public $mediator_id;
    public $mediator_rate;
    public $user_rate;

    public $status;
    public $roles;

    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],
            // ['email', 'unique', 'targetClass' => '\common\models\User'],

            [['password'], 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],

            ['phone', 'required', 'on' => 'create'],
            ['phone', 'string'],
            // ['phone', 'filter', 'filter' => 'trim'],
            ['phone', 'filter', 'filter' => function ($value) {
                return str_replace(" ", "", $value);
            }],
            ['phone', 'match', 'pattern' => '/^[0-9\-\(\)\/\+\s]*$/'],
            // ['phone', 'unique', 'targetClass' => '\common\models\User'],
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],

            [['firstname', 'lastname', 'address'], 'string'],
            [['firstname', 'lastname', 'address'], 'filter', 'filter' => 'trim'],

            [['manager_id', 'mediator_id'], 'integer'],
            [['manager_rate', 'user_rate', 'mediator_rate'], 'double'],

            [['status'], 'integer'],

            ['roles', 'required'],
            [
                ['roles'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->authManager->getRoles(),
                    'name'
                )],
            ],
            
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
        $this->email = $model->email;
        $this->phone = $model->phone;
        
        $this->firstname = $model->userProfile->firstname;
        $this->lastname = $model->userProfile->lastname;
        $this->address = $model->userProfile->address;
        $this->manager_id = $model->userProfile->manager_id;
        $this->manager_rate = $model->userProfile->manager_rate;
        $this->mediator_id = $model->userProfile->mediator_id;
        $this->mediator_rate = $model->userProfile->mediator_rate;
        $this->user_rate = $model->userProfile->user_rate;
        
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );

        $this->model = $model;

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
        if ($this->getModel()->getIsNewRecord()) {
            if (in_array(User::ROLE_COMPANY, $this->roles)) {
                $this->email = Yii::$app->getSecurity()->generateRandomString(16).'@company.com';
                $this->password = Yii::$app->getSecurity()->generateRandomString(16);
            }
        }

        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            
            if ($isNewRecord) {
                $model->username = Yii::$app->getSecurity()->generateRandomString(16).time();
            }
            
            $model->email = $this->email;
            $model->phone = $this->phone;
            $model->status = $this->status;
            
            if ($this->password) {
                $model->setPassword($this->password);
            }

            if ($model->validate()) {
                if ($model->save() && $isNewRecord) {
                    $model->afterSignup([
                        'firstname' => $this->firstname,
                        'lastname' => $this->lastname,
                        'address' => $this->address,
                        'manager_id' => $this->manager_id,
                        'manager_rate' => $this->manager_rate,
                        'mediator_id' => $this->mediator_id,
                        'mediator_rate' => $this->mediator_rate,
                        'user_rate' => $this->user_rate,
                    ]);
                } else {
                    $model->userProfile->firstname = $this->firstname;
                    $model->userProfile->lastname = $this->lastname;
                    $model->userProfile->address = $this->address;
                    $model->userProfile->manager_id = $this->manager_id;
                    $model->userProfile->manager_rate = $this->manager_rate;
                    $model->userProfile->mediator_id = $this->mediator_id;
                    $model->userProfile->mediator_rate = $this->mediator_rate;
                    $model->userProfile->user_rate = $this->user_rate;
                    $model->userProfile->save();
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
