<?php

namespace frontend\modules\user\controllers;

use common\commands\command\SendEmailCommand;
use common\models\User;
use frontend\modules\user\models\ActivateForm;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResetEmaildForm;
use frontend\modules\user\models\ResetEmailForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SignInController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'oauth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successOAuthCallback']
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'signup',
                            'login',
                            'request-password-reset',
                            'reset-password',
                            'reset-password-success',
                            'reset-email',
                            'reset-email-success',
                            'oauth',
                            'twitter-callback',
                            'activate',
                            'activate-success',
                        ],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => [
                            'signup',
                            'login',
                            'request-password-reset',
                            'reset-password',
                            'reset-password-success',
                            'reset-email',
                            'reset-email-success',
                            'oauth'
                        ],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/user/default/index']);
                        }
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            /*'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]*/
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSignup()
    {
        $this->redirect(['login']);

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user && Yii::$app->getUser()->login($user)) {
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=>Yii::t('frontend', 'Check your email for further instructions.'),
                    'options'=>['class'=>'alert-success']
                ]);

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=>Yii::t('frontend', 'Sorry, we are unable to reset password for email provided.'),
                    'options'=>['class'=>'alert-danger']
                ]);
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('alert', [
                'body'=> Yii::t('frontend', 'New password was saved.'),
                'options'=>['class'=>'alert-success']
            ]);

            //return $this->goHome();
            /*Yii::$app->user->login($model->getUser(), 3600 * 24 * 30);
            return $this->redirect(Url::to(['/user/sign-in/login']));*/
            return $this->redirect(Url::to(['/user/sign-in/reset-password-success']));
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionResetEmail($token)
    {
        try {
            $model = new ResetEmailForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->getUser()->email_reset) {
//            Yii::$app->request->setBodyParams(['ResetEmailForm' => [
//                'email' => $model->getUser()->email_reset
//            ]]);
            $model->email = $model->getUser()->email_reset;
        } else {
            throw new InvalidParamException('Wrong email reset token.');
        }

//        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetEmail()) {
        if (Yii::$app->request->isPost && $model->validate() && $model->resetEmail()) {
            Yii::$app->getSession()->setFlash('alert', [
                'body'=> Yii::t('frontend', 'New email was saved.'),
                'options'=>['class'=>'alert-success']
            ]);

            return $this->redirect(Url::to(['/user/sign-in/reset-email-success']));
        }

        return $this->render('resetEmail', [
            'model' => $model,
        ]);
    }
    
    public function actionResetPasswordSuccess()
    {
        return $this->render('resetPasswordSuccess', []);
    }

    public function actionResetEmailSuccess()
    {
        return $this->render('resetEmailSuccess', []);
    }

    public function actionActivate($token, $email) {

        try {
            $model = new ActivateForm($token, $email);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $isSuccess = false;


        if (!$model->getUser() || $model->getUser()->status == User::STATUS_ACTIVE) {
            $isSuccess = true;
        } else {
            if ($model->activate()) {
               $isSuccess = true;

               // delete other accounts
                User::deleteAll([
                    'status' => User::STATUS_REGISTRATION,
                    'email' => $model->getUser()->email,
//                    'oauth_client_user_id' => null,
//                    'oauth_client' => null
                ]);
            }
        }

        if ($isSuccess) {
            Yii::$app->getSession()->setFlash('alert', [
                'body'=> Yii::t('frontend', 'Your email has been verified'),
                'options'=>['class'=>'alert-success']
            ]);

            return $this->redirect(Url::to(['/user/sign-in/activate-success', 'token' => $token, 'email' => $email]));
        } else {
            throw new BadRequestHttpException(Yii::t("frontend", "Bad request"));
        }
    }

    public function actionActivateSuccess($token, $email)
    {
        try {
            $model = new ActivateForm($token, $email);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $this->render('activateSuccess', [
            'model' => $model->getUser()
        ]);
    }

    /**
     * @param $client \yii\authclient\BaseClient
     * @return bool
     * @throws Exception
     */
    public function successOAuthCallback($client)
    {
        // use BaseClient::normalizeUserAttributeMap to provide consistency for user attribute`s names
        $attributes = $client->getUserAttributes();
        $user = User::find()->where([
                'oauth_client'=>$client->getName(),
                'oauth_client_user_id'=>ArrayHelper::getValue($attributes, 'id')
            ])
            ->one();
        if (!$user) {
            $user = new User();
            $user->scenario = 'oauth_create';
            $user->username = ArrayHelper::getValue($attributes, 'login');
            $user->email = ArrayHelper::getValue($attributes, 'email');
            $user->oauth_client = $client->getName();
            $user->oauth_client_user_id = ArrayHelper::getValue($attributes, 'id');
            $password = Yii::$app->security->generateRandomString(8);
            $user->setPassword($password);
            if ($user->save()) {
                $profileData = [];
                if ($client->getName() === 'facebook') {
                    $profileData['firstname'] = ArrayHelper::getValue($attributes, 'first_name');
                    $profileData['lastname'] = ArrayHelper::getValue($attributes, 'last_name');
                }
                $user->afterSignup($profileData);
                $sentSuccess = Yii::$app->commandBus->handle(new SendEmailCommand([
                    'view' => 'oauth_welcome',
                    'params' => ['user'=>$user, 'password'=>$password],
                    'subject' => Yii::t('frontend', '{app-name} | Your login information', ['app-name'=>Yii::$app->name]),
                    'to' => $user->email
                ]));
                if ($sentSuccess) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-success'],
                            'body'=>Yii::t('frontend', 'Welcome to {app-name}. Email with your login information was sent to your email.', [
                                'app-name'=>Yii::$app->name
                            ])
                        ]
                    );
                }

            } else {
                // We already have a user with this email. Do what you want in such case
                if ($user->email && User::find()->where(['email'=>$user->email])->count()) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-danger'],
                            'body'=>Yii::t('frontend', 'We already have a user with email {email}', [
                                'email'=>$user->email
                            ])
                        ]
                    );
                } else {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-danger'],
                            'body'=>Yii::t('frontend', 'Error while oauth process.')
                        ]
                    );
                }

            };
        }
        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return true;
        } else {
            throw new Exception('OAuth error');
        }
    }

    public function actionTwitterCallback()
    {
        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && Yii::$app->session['oauth_token'] !== $_REQUEST['oauth_token']) {
            Yii::$app->session['oauth_status'] = 'oldtoken';
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $twitter = Yii::$app->twitter->getTwitterTokened(Yii::$app->session['oauth_token'], Yii::$app->session['oauth_token_secret']);

        /* Request access tokens from twitter */
        $access_token = $twitter->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        Yii::$app->session['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset(Yii::$app->session['oauth_token']);
        unset(Yii::$app->session['oauth_token_secret']);

        if (200 == $twitter->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            /*Yii::$app->session['status'] = 'verified';

            //get an access twitter object
            $twitter = Yii::$app->twitter->getTwitterTokened($access_token['oauth_token'],$access_token['oauth_token_secret']);

            //get user details
            $twuser= $twitter->get("account/verify_credentials");
            //get friends ids
            $friends= $twitter->get("friends/ids");
            //get followers ids
            $followers= $twitter->get("followers/ids");
            //tweet
            $result=$twitter->post('statuses/update', ['status' => "Tweet message"]);*/

        } else {
            /* Save HTTP status for error dialog on connnect page.*/
            //header('Location: /clearsessions.php');
            /*return $this->redirect(Url::home());*/
        }
    }
}
