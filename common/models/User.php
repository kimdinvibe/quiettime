<?php
namespace common\models;

use Yii;
use Exception;
use cheatsheet\Time;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use common\commands\command\AddToTimelineCommand;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_reset_token
 * @property string $email_reset
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property integer $logout_at
 * @property string $password write-only password
 * @property integer $is_show_request
 * @property integer $is_show_friend
 *
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REGISTRATION = 2;
    const STATUS_REGISTRATION_BY_PHONE = 3;

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    const ROLE_USER = 'user';
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_PREMIUM = 'premium';

    public $group;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],
            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString(40)
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create'=>[
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'unique', 'targetClass'=> '\common\models\User', 'filter' => function ($query) {
                $query->andWhere(['!=', 'status', self::STATUS_REGISTRATION]);
                $query->andWhere(['!=', 'status', self::STATUS_REGISTRATION_BY_PHONE]);

                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
            ['email', 'unique', 'targetClass'=> '\common\models\User', 'filter' => function ($query) {
                $query->andWhere(['!=', 'status', self::STATUS_REGISTRATION]);
                $query->andWhere(['!=', 'status', self::STATUS_REGISTRATION_BY_PHONE]);

                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
            //[['email'], 'uniqueAttributOAuth', 'oauth_client', 'oauth_client_user_id'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_REGISTRATION, self::STATUS_REGISTRATION_BY_PHONE]],
            [['username'],'filter','filter'=>'\yii\helpers\Html::encode'],
            [['email'], 'string', 'max' => 255],
            [['oauth_client', 'oauth_client_user_id', 'phone', 'phone_code'], 'string'],
        ];
    }

    public function uniqueAttribute($attribute_name, $params)
    {
        if(!empty($this->$attribute_name)) {
            $query = self::find()->where([
                $attribute_name => $this->{$attribute_name}
            ]);



            if (!(empty($this->oauth_client) && empty($this->oauth_client_user_id))) {
                $query->andWhere([
                    'oauth_client' => $this->oauth_client,
                    'oauth_client_user_id' => $this->oauth_client_user_id
                ]);
            }

            if ($query->one()) {
                $this->addError($attribute_name, Yii::t('user', 'Need unique field'));
                return false;
            }
        }


        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'E-mail'),
            'email_reset' => Yii::t('common', 'E-mail Reset'),
            'status' => Yii::t('common', 'Status'),
            'phone' => Yii::t('common', 'Phone'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => Yii::t('common', 'Last login'),
            'group' => Yii::t('common', 'Group'),
            'auth_key' => Yii::t('common', 'Auth Key'),
            'oauth_client' => Yii::t('common', 'Oauth Client'),
            'password_reset_token' => Yii::t('common', 'Password Reset Token'),
            'email_reset_token' => Yii::t('common', 'Email Reset Token'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */        
    public function getRbacAuthAssignment()
    {
        return $this->hasOne(RbacAuthAssignment::className(), ['user_id'=>'id']);        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(UserDevice::className(), ['user_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function afterSave($insert, $attributes)
    {
        parent::afterSave($insert, $attributes);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\RequestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserQuery(get_called_class());
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        /*return static::findOne([
            'and',
            ['or', ['username' => $login], ['email' => $login]],
            'status' => self::STATUS_ACTIVE
        ]);*/
        
        //не ищет иначе, конструкция не работает для findOne с and и or!!!!!        
        return static::find()->where([
            'and',
            ['or', ['username' => $login], ['email' => $login]],
            'status' => self::STATUS_ACTIVE
        ])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Time::SECONDS_IN_A_DAY;
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    public static function findByEmailResetToken($token)
    {
        $expire = Time::SECONDS_IN_A_DAY;
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'email_reset_token' => $token,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Generates new password reset token
     * @param null $email
     * @throws \yii\base\Exception
     */
    public function generateEmailResetToken($email = null)
    {
        $this->email_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();

        if ($email) {
            $this->email_reset = $email;
        }
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Removes password reset token
     */
    public function removeEmailResetToken()
    {
        $this->email_reset_token = null;
        $this->email_reset = null;
    }

    public function generateAccessToken() {
        $this->access_token = Yii::$app->getSecurity()->generateRandomString(40);
    }

    /**
     * Returns user statuses list
     * @param mixed $status
     * @return array|mixed
     */
    public static function getStatuses($status = false)
    {
        $statuses = [
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETED => Yii::t('common', 'Deleted'),
            self::STATUS_REGISTRATION_BY_PHONE => Yii::t('common', 'SignUp By Phone'),
            self::STATUS_REGISTRATION => Yii::t('common', 'SignUp')
        ];
        return $status !== false ? ArrayHelper::getValue($statuses, $status) : $statuses;
    }

    /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [], $role = User::ROLE_USER)
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));
        
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);

//         UserNotification::createAllNotifications($this->id, [
//             UserNotification::TYPE_PUSH,
// //            UserNotification::TYPE_EMAIL
//         ]);

        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
        $auth =  Yii::$app->authManager;
        $auth->assign($auth->getRole($role), $this->getId());
    }
    
    public function afterFind()
    {
        parent::afterFind();
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }
    
    static function getFbAccessToken()
    {
        try {
            if($result = file_get_contents('https://graph.facebook.com/oauth/access_token?'.http_build_query([
                    'client_id' => Yii::$app->keyStorage->get('common.fb-client-id'),
                    'client_secret' => Yii::$app->keyStorage->get('common.fb-client-secret'),
                    'grant_type' => Yii::$app->keyStorage->get('common.fb-grant-type')
                ]))){

                if ($result = json_decode($result, true)){
                    if (isset($result['access_token']) && $result['access_token']){
                        return $result['access_token'];
                    }
                }
            }
        } catch (Exception $e) {
            return null;
        }
        
        return null;
    }
    
    static function debugFbToken($input_token)
    {
        try {
            if($result = file_get_contents('https://graph.facebook.com/debug_token?'.http_build_query([
                    'input_token' => $input_token,
                    'access_token' => self::getFbAccessToken()
                ]))){
                return json_decode($result, true);
            }
        } catch (Exception $e) {
            return null;
        }
        
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByFbUserId($user_id)
    {
        return static::find()->joinWith('userProfile')->where([UserProfile::tableName().'.fb_user_id' => $user_id])->one();
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByFbToken($token)
    {
        return static::find()->joinWith('userProfile')->where([UserProfile::tableName().'.fb_token' => $token])->one();
    }

    // helpers
    public function getDictionriesByCodeCategory($category_id) {
        if ($this->dictionaries) {
            $items = [];

            foreach ($this->dictionaries as $dictioanry) {
                if ($dictioanry->category_id == $category_id) {
                    $items[] = $dictioanry;
                }
            }

            return $items;
        }
    }

    public function getListOfActiveNotification($category) {
        return UserNotification::find()->where([
            'user_id' => $this->id,
            'category' => $category
        ])->active()->all();
    }
}
