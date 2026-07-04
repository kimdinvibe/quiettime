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
class ResetEmailForm extends Model
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
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Email reset token cannot be blank.');
        }
        $this->user = User::findByEmailResetToken($token);
        if (!$this->user) {
            throw new InvalidParamException('Wrong email reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', NoExistValidator::className(),
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t("frontend", 'This email has already been taken.')
            ],
        ];
    }

    /**
     * Resets email.
     *
     * @return boolean if email was reset.
     */
    public function resetEmail()
    {
        $user = $this->user;
        $user->email = $this->email;
        $user->removeEmailResetToken();

        return $user->save();
    }

    public function attributeLabels()
    {
        return [
            'email'=>Yii::t('frontend', 'Email')
        ];
    }

    public function getUser()
    {
        return $this->user;
    }
}
