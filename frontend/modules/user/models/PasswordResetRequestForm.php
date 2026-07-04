<?php
namespace frontend\modules\user\models;

use common\commands\command\SendEmailCommand;
use Yii;
use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t("frontend", 'There is no user with such email.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail($params = null)
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $user->generatePasswordResetToken();

            if ($user->update(false, ['password_reset_token'])) {
                return Yii::$app->commandBus->handle(new SendEmailCommand([
                    'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
                    'to' => $this->email,
                    // 'subject' => Yii::t('frontend', '{name} - Reset your password', ['name'=>Yii::$app->name]),
                    'subject' => 'Восстановление пароля от приложения «Тихое время»',
                    'view' => $params['view']?$params['view']:'passwordResetToken',
                    'body' => $params['body']?$params['body']:null,
                    'params' => ['user' => $user]
                ]));
            } else {
                return false;
            }
        }

        return false;
    }

    public function attributeLabels()
    {
        return [
            'email'=>Yii::t('frontend', 'E-mail')
        ];
    }
}
