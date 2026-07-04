<?php
namespace frontend\modules\user\models;

use common\models\User;
use frontend\components\NoExistValidator;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ActivateForm extends Model
{
    public $email;

    /**
     * @var \common\models\User
     */
    private $user;

    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $email, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Token cannot be blank.');
        }
        $this->user = User::find()->where([
            'auth_key' => $token,
            'email' => $email
        ])->one();
        if (!$this->user) {
            throw new InvalidParamException('Wrong activate account.');
        }
        parent::__construct($config);
    }

    /**
     * Resets email.
     *
     * @return boolean if email was reset.
     */
    public function activate()
    {
        $user = $this->user;
        $user->status = User::STATUS_ACTIVE;

        return $user->save();
    }

    public function getUser()
    {
        return $this->user;
    }
}
