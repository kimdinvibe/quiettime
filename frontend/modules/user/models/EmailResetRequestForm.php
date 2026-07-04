<?php
namespace frontend\modules\user\models;

use common\commands\command\SendEmailCommand;
use frontend\components\NoExistValidator;
use Yii;
use common\models\User;
use yii\base\Model;

/**
 * Email reset request form
 */
class EmailResetRequestForm extends Model
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
            ['email', NoExistValidator::className(),
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t("frontend", 'This email has already been taken.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the email.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail($params = null)
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'id' => Yii::$app->user->id,
        ]);

        if ($user) {
            $user->generateEmailResetToken($this->email);

            if ($user->update(false, ['email_reset_token', 'email_reset'])) {
                return Yii::$app->commandBus->handle(new SendEmailCommand([
                    'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
                    'to' => $this->email,
                    'subject' => Yii::t('frontend', 'Email reset for {name}', ['name'=>Yii::$app->name]),
                    'view' => $params['view']?$params['view']:'emailResetToken',
                    'body' => $params['body']?$params['body']:null,
                    'params' => ['user' => $user]
                ]));
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
