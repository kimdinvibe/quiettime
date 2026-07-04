<?php

namespace frontend\modules\api\v1\controllers;

use Yii;
use common\models\User;
use yii\web\HttpException;
use common\models\UserDevice;
use common\models\UserLocation;
use frontend\helpers\ApiHelper;

use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\RbacAuthAssignment;
use yii\web\ServerErrorHttpException;
use frontend\components\ApiController;
use frontend\models\PasswordChangeForm;
use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\user\models\LoginForm;
use frontend\modules\api\v1\resources\UserProfile;
use frontend\modules\api\v1\resources\UserMiniListItem;
use frontend\modules\api\v1\resources\UserNotification;
use frontend\modules\user\models\EmailResetRequestForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\api\v1\resources\User as UserResource;
use common\commands\command\SendEmailCommand;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = 'frontend\modules\api\v1\resources\User';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                /*[
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        $user = User::findByLogin($username);
                        return $user->validatePassword($password)
                            ? $user
                            : null;
                    }
                ],*/
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => [
                'sign-up',
                'sign-up-by-phone',
                'verify-phone',
                'get-new-code',
                'auth-social',
                'check-social',
                'password-reset',
                'sign-in',
                // 'index',
                // 'view',
                'test',
                'send-test-email'
            ]
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'sign-up' => ['POST'],
            'update' => ['PUT', 'PATCH', 'POST'],
            'auth' => ['GET'],
            'sign-in' => ['POST'],
            'view' => ['GET'],
            'info' => ['GET'],
            'password-reset' => ['POST'],
            'email-reset' => ['POST'],
            'auth-social' => ['GET'],
            'change-password' => ['POST'],
            'remove' => ['POST'],
            'replace-device-id' => ['POST'],
            'change-status-device-id' => ['POST'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param array $additionlaParams
     * @param int $status
     * @return mixed
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSignUp($additionlaParams = [], $status = User::STATUS_REGISTRATION)
    {
        $params = array_merge_recursive(Yii::$app->request->getBodyParams(), $additionlaParams);

        $model = new \frontend\models\UserForm([
            'status' => $status,
            'roles' => ['user']
        ]);

        $model->setScenario('create');

        $model->load(["UserForm" => $params['user']]);
        $emptyPrefix = '${empty}$';

        //        if (!$model->email) {
        //            $model->email = $emptyPrefix.md5(time().$model->email.Yii::$app->getSecurity()->generateRandomString(64)).'_'.time().'@system.com';
        //        }

        // username required for main sign-up

        // if(!$model->username){
        //     if($model->email){
        //         $model->username = explode('@', $model->email);

        //         if (trim($model->username[0])) {
        //             $model->username = $emptyPrefix.$model->username[0].'$'.Yii::$app->getSecurity()->generateRandomString(8).'$'.time();
        //         }
        //     }

        //     if(!$model->username){
        //         $model->username = $emptyPrefix.md5(time().$model->email);
        //     }
        // }

        // if(!$model->password){
        //     $model->password = $emptyPrefix.substr(md5(time()), 0, 16);
        // }

        if ($model->save()) {
            // add 7 days premium for user;
            $model->model->userProfile->premium_at = strtotime("+7 day");
            $model->model->userProfile->save();

            if ($params['profile']) {
                $model->model->userProfile->load(["UserProfile" => $params['profile']]);

                $this->saveAvatar($params);

                if (isset($params['file']['file_id']) && $params['file']['file_id']) {
                    if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => (int)$params['file']['file_id']])) {
                        $model->model->userProfile->avatar_path = $fileId->path;
                        $model->model->userProfile->avatar_base_url = $fileId->base_url;

                        $model->model->userProfile->update(false, [
                            'avatar_path',
                            'avatar_base_url'
                        ]);
                    }
                }
            }

            // send email
            try {
                // test activation
                //echo Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/activate', 'token' => $model->model->auth_key, 'email' => $model->model->email]); exit;

                if ($model->model->email) {
                    $model->model->refresh();

                    if (Yii::$app->commandBus->handle(new SendEmailCommand([
                        'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
                        'to' => $model->model->email,
                        // 'subject' => Yii::t('frontend', '{name} - Please confirm your registration', ['name' => Yii::$app->name]),
                        'subject' => 'Подтвердите адрес электронной почты для приложения «Тихое время»',
                        'view' => $this->getFileViewForEmail("activateAccount"),
                        'body' => null,
                        'params' => ['user' => $model->model]
                    ]))) {
                        //
                    } else {
                        throw new \Exception("Not sent email");
                    }
                }
            } catch (\Exception $e) {
                $model->model->delete();
                throw new ServerErrorHttpException(Yii::t('api', 'Not sent email'));
            }

            $this->saveDeviceForUser($model->model->id);

            $_GET['expand'] = 'userProfile,access_token,countNewMessages';
            return UserResource::findOne($model->model->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to create the object for unknown reason.'));
        }

        return $model;
    }

    public function actionSignIn()
    {
        $params = Yii::$app->request->getBodyParams();
        $model = new LoginForm();

        if ($model->load(['LoginForm' => $params]) && $model->login()) {
            $model->getUser()->logout_at = null;
            $model->getUser()->update(false, ['logout_at']);

            $_GET['expand'] = 'userProfile,userProfile.manager,access_token,role';
            return $this->findModel($model->getUser()->primaryKey);
        } else {
            if (!$model->hasErrors()) {
                throw new ServerErrorHttpException(Yii::t('api', 'Failed to create the object for unknown reason.'));
            }
        }

        return $model;
    }

    public function actionLogout()
    {
        //Yii::$app->user->logout();
        Yii::$app->user->identity->logout_at = time();

        if (Yii::$app->user->identity->update(false, ['logout_at'])) {
            return ApiHelper::returnSuccess();
        }

        throw new HttpException(404, Yii::t('api', 'Not found'));
    }

    // /**
    //  * Create User model.
    //  * @return mixed
    //  */
    // public function actionAdd()
    // {
    //     if (Yii::$app->user->can('administrator')) {
    //         return $this->_update();
    //     } else {
    //         throw new HttpException(404, Yii::t('api', 'Not found'));
    //     }
    // }

    /**
     * Update User model.
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->_update($id);
    }

    /**
     * Updates an existing User model.
     * @return mixed
     */
    // public function actionUpdateProfile()
    // {
    //     return $this->_update(Yii::$app->user->id);
    // }

    public function actionUpdateProfile()
    {
        $params = Yii::$app->request->getBodyParams();

        $model = new \frontend\models\UserForm();
        $model->setModel($this->findModel(Yii::$app->user->id));
        $model->load(["UserForm" => $params['user']]);

        // return $params; exit;

        if ($model->save()) {

            if ($params['profile']) {
                $model->model->userProfile->load(["UserProfile" => $params['profile']]);
                $model->model->userProfile->save();
            }

            if (isset($params['file']['file_id']) && $params['file']['file_id']) {
                if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => (int)$params['file']['file_id']])) {
                    $model->model->userProfile->avatar_path = $fileId->path;
                    $model->model->userProfile->avatar_base_url = $fileId->base_url;

                    $model->model->userProfile->update(false, ['avatar_path', 'avatar_base_url']);
                }
            } elseif (isset($params['file']['name']) && $params['file']['name'] && $_FILES) {
                // load file
                try {
                    if ($result = Yii::$app->runAction(
                        '/api/v1/file/upload',
                        [
                            'name' => $params['file']['name'],
                            'title' => isset($params['file']['title']) ? $params['file']['title'] : ''
                        ]
                    )) {
                        if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => $result['id']])) {
                            $model->model->userProfile->avatar_path = $fileId->path;
                            $model->model->userProfile->avatar_base_url = $fileId->base_url;

                            $model->model->userProfile->update(false, ['avatar_path', 'avatar_base_url']);
                        }
                    }
                } catch (\Exception $e) {
                    throw new ServerErrorHttpException(Yii::t('api', $e->getMessage()));
                }
            }

            $_GET['expand'] = 'userProfile,userProfile.manager,access_token';

            return UserResource::findOne($model->model->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
        }

        return $model;
    }

    /**
     * Update User model.
     * @return mixed
     */
    public function _update($id = null)
    {
        $params = Yii::$app->request->getBodyParams();

        $model = new \frontend\models\UserCreateForm();

        if ($id) {
            $model->setModel($this->findModel($id));
        }

        $model->load(["UserCreateForm" => $params['user']]);
        $model->load(["UserCreateForm" => $params['profile']]);

        // return $model->attributes;
        // exit;

        if ($model->save()) {
            if (isset($params['file']['file_id']) && $params['file']['file_id']) {
                if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => (int)$params['file']['file_id']])) {
                    $model->model->userProfile->avatar_path = $fileId->path;
                    $model->model->userProfile->avatar_base_url = $fileId->base_url;

                    $model->model->userProfile->update(false, ['avatar_path', 'avatar_base_url']);
                }
            } elseif (isset($params['file']['name']) && $params['file']['name'] && $_FILES) {
                // load file
                try {
                    if ($result = Yii::$app->runAction(
                        '/api/v1/file/upload',
                        [
                            'name' => $params['file']['name'],
                            'title' => isset($params['file']['title']) ? $params['file']['title'] : ''
                        ]
                    )) {
                        if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => $result['id']])) {
                            $model->model->userProfile->avatar_path = $fileId->path;
                            $model->model->userProfile->avatar_base_url = $fileId->base_url;

                            $model->model->userProfile->update(false, ['avatar_path', 'avatar_base_url']);
                        }
                    }
                } catch (\Exception $e) {
                    throw new ServerErrorHttpException(Yii::t('api', $e->getMessage()));
                }
            }

            $_GET['expand'] = 'userProfile,userProfile.manager,access_token';

            return UserResource::findOne($model->model->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
        }

        return $model;
    }

    /**
     * @param $hash
     * @return array|\common\models\Request|UserResource|null
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateAvatar()
    {
        if ($model = UserResource::findOne(Yii::$app->user->id)) {
            $params = Yii::$app->request->getBodyParams();

            if (isset($params['file']['name']) && $params['file']['name']) {

                // load file
                try {
                    // this function only for register user
                    if ($result = Yii::$app->runAction(
                        '/api/v1/file/upload',
                        [
                            'name' => $params['file']['name'],
                            'title' => isset($params['file']['title']) ? $params['file']['title'] : ''
                        ]
                    )) {

                        if ($fileId = \frontend\modules\api\v1\resources\FileStorageItem::findOne(['id' => $result['id']])) {
                            $model->userProfile->avatar_path = $fileId->path;
                            $model->userProfile->avatar_base_url = $fileId->base_url;
                            $model->userProfile->update(false, ['avatar_path', 'avatar_base_url']);
                        }
                    }
                } catch (\Exception $e) {
                    throw new ServerErrorHttpException(Yii::t('api', $e->getMessage()));
                }
            }

            $_GET['expand'] = 'userProfile,userProfile.manager,access_token,countNewMessages';
            return $model;
        } else {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
    }


    /**
     * @return array
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionUpdateNotifications()
    {
        $params = Yii::$app->request->getBodyParams();

        ApiHelper::checkRequiredFields([
            // 'notification_on' => Yii::t('api', 'notification_on'),
            'notification_time' => Yii::t('api', 'notification_time'),
            'notification_days' => Yii::t('api', 'notification_days'),
        ], $params);

        Yii::$app->user->identity->userProfile->notification_on = $params['notification_on'];
        Yii::$app->user->identity->userProfile->notification_time = $params['notification_time'];
        Yii::$app->user->identity->userProfile->notification_days = $params['notification_days'];

        if (Yii::$app->user->identity->userProfile->save()) {
            $_GET['expand'] = 'userProfile,userProfile.manager,access_token';

            return UserResource::findOne(Yii::$app->user->id);
        }

        // if ($params['notifications']) {
        //     $transaction = Yii::$app->db->beginTransaction();

        //     try {
        //         UserNotification::deleteAll([
        //             'user_id' => Yii::$app->user->id
        //         ]);

        //         foreach ([
        //             UserNotification::CATEGORY_INCOMING_MESSAGE,
        //             UserNotification::CATEGORY_REQUEST_UPDATE,
        //             UserNotification::CATEGORY_NEW_REVIEWS_ON_YOU,
        //             UserNotification::CATEGORY_NEW_REQUEST_NEARBY,
        //             UserNotification::CATEGORY_NEW_REQUEST_FROM_FRIENDS
        //         ] as $category) {
        //             foreach ([
        //                 UserNotification::TYPE_EMAIL,
        //                 UserNotification::TYPE_PUSH,
        //                 UserNotification::TYPE_CHAT
        //             ] as $type) {
        //                 (new UserNotification([
        //                     'user_id' => Yii::$app->user->id,
        //                     'category' => $category,
        //                     'type' => $type,
        //                     'status' => isset($params['notifications'][$category][$type])
        //                         && $params['notifications'][$category][$type]
        //                         ? UserNotification::STATUS_ACTIVE
        //                         : UserNotification::STATUS_DISABLE
        //                 ]))->save();
        //             }
        //         }

        //         $transaction->commit();

        //         return [
        //             'result' => 'success'
        //         ];
        //     } catch (\Exception $e) {
        //         $transaction->rollBack();
        //         throw new HttpException(500, Yii::t('api', 'Server error'));
        //     }
        // }

        throw new ServerErrorHttpException(Yii::t('api', 'Server error'));
    }

    public function actionCancelSubscription()
    {
        Yii::$app->user->identity->userProfile->premium_period = null;

        if (Yii::$app->user->identity->userProfile->save()) {
            $_GET['expand'] = 'userProfile,userProfile.manager,access_token';

            return UserResource::findOne(Yii::$app->user->id);
        }

        throw new ServerErrorHttpException(Yii::t('api', 'Server error'));
    }

    public function actionInfo($id = null)
    {
        if (!$id && Yii::$app->user->isGuest) {
            throw new ServerErrorHttpException(Yii::t('api', 'Sorry but only registered user have access to profile'));
        }

        if (!$id) {
            $id = Yii::$app->user->id;
            $_GET['expand'] = 'userProfile,userProfile.manager,access_token,role';
        }


        return $this->findModel($id);
    }

    public function actionStatus()
    {
        $newMessages = 0;
        $currentRequests = 0;
        $countRequestsFriend = 0;
        $countResponses = 0;

        if (!Yii::$app->user->isGuest) {
            // $newMessages = Yii::$app->user->identity->getCountNewMessages();
            // $currentRequests = Yii::$app->user->identity->getCountActiveRequests();
            // $countRequestsFriend = Yii::$app->user->identity->getCountNewRequestsFriend();
            // $countNotifications = Yii::$app->user->identity->getCountNewRequestNotifications();
        }

        return [
            'result' => 'success',
            'messages' => $newMessages,
            'requests' => $currentRequests,
            'friends' => $countRequestsFriend,
            // 'requestNotifications' => $countNotifications,
            // 'isLimitRequests' => Yii::$app->user->identity->getIsLimitRequests($currentRequests)
        ];
    }

    public function actionAuth()
    {
        if (!Yii::$app->request->get('expand')) {
            $_GET['expand'] = 'userProfile,userProfile.manager,access_token,countNewMessages';
        }

        return $this->findModel(Yii::$app->user->id);
    }

    /**
     * @return array|PasswordResetRequestForm
     * @throws HttpException
     * @throws ServerErrorHttpException
     */
    public function actionPasswordReset()
    {
        if ($email = Yii::$app->request->getBodyParam('email')) {
            $model = new PasswordResetRequestForm();
            if ($model->load(['PasswordResetRequestForm' => ['email' => $email]]) && $model->validate()) {
                if ($model->sendEmail([
                    'view' => $this->getFileViewForEmail("passwordResetToken")
                ])) {
                    return ['result' => 'success'];
                } else {
                    throw new ServerErrorHttpException(Yii::t('api', 'Not sent email'));
                }
            }

            return $model;
        } else {
            throw new HttpException(404, Yii::t("api", "Missing required parameters: {0}", ['email']));
        }
    }

    /**
     * @return array|PasswordChangeForm|UserResource
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionChangePassword()
    {
        $params = Yii::$app->request->getBodyParams();
        $model = $this->findModel(Yii::$app->user->id);

        $passwordForm = new PasswordChangeForm();

        if (!$model->validatePassword($params['password_old'])) {
            $model->addError('*', 'Incorrect current password');
            return $model;
        }

        if (
            $passwordForm->load(['PasswordChangeForm' => $params])
            && $passwordForm->validate()
        ) {
            $model->setPassword($params['password']);
        } else {
            return $passwordForm;
            //throw new ServerErrorHttpException(Yii::t('api', 'Password id empty.'));
        }

        if ($model->update(false, ['password_hash'])) {
            return ['result' => 'success'];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
        }

        return $model;
    }

    /**
     * @return array|UserResource
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemove()
    {
        $model = $this->findModel(Yii::$app->user->id);

        $emptyPrefix = '${remove}$';
        $model->username = $emptyPrefix . '$' . $model->id . '$' . Yii::$app->getSecurity()->generateRandomString(8) . '$' . time();
        $model->email = $model->username . "-remove@oodymate.com";

        $model->status = User::STATUS_DELETED;
        $model->userProfile->avatar_base_url = "/storage/web/source";
        $model->userProfile->avatar_path = "user-deleted.png";
        $model->userProfile->firstname = "User deleted";

        if ($model->update(false, ['status', 'username', 'email'])) {
            $model->userProfile->update(false, ['avatar_base_url', 'avatar_path', 'firstname']);

            // $stateDeleteSystem = Dictionary::getItemByCodeAndCategory(
            //     Request::STATUS_SYSTEM_DELETED,
            //     DictionaryCategory::CATEGORY_REQUEST_STATE
            // );
            // $stateFinished = Dictionary::getItemByCodeAndCategory(
            //     Request::STATUS_FINISHED,
            //     DictionaryCategory::CATEGORY_REQUEST_STATE
            // );

            // Request::updateAll(['state_id' => $stateDeleteSystem->id], [
            //     'and',
            //     ['user_id' => $model->id],
            //     ['!=', 'state_id', $stateFinished->id]
            // ]);

            UserDevice::updateAll(['state' => UserDevice::STATE_ARCHIVE], ['user_id' => $model->id]);

            return ['result' => 'success'];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
        }

        return $model;
    }

    // for turn push notifications
    public function actionReplaceDeviceId()
    {
        $params = Yii::$app->request->getBodyParams();

        if (isset($params['old'], $params['new']) && $params['new'] && $params['old']) {
            $new = new UserDevice([
                'user_id' => Yii::$app->user->id,
                'device_id' => $params['new']
            ]);

            if (!UserDevice::find()->where([
                'user_id' => Yii::$app->user->id,
                'device_id' => $params['new']
            ])->exists()) {
                if (!$new->save()) {
                    return $new;
                }
            }


            if ($old = UserDevice::find()->where([
                'user_id' => Yii::$app->user->id,
                'device_id' => $params['old']
            ])->one()) {
                if (!$old->delete()) {
                    if ($new->id) {
                        $new->delete();
                    }

                    return $old;
                }
            }

            return ['result' => 'success'];
        } else {
            throw new ServerErrorHttpException(Yii::t('api', 'Need old and new device ids'));
        }

        throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
    }

    public function actionChangeStatusDeviceId()
    {
        $params = Yii::$app->request->getBodyParams();

        if (
            isset($params['token'], $params['state']) && $params['token'] && $params['state']
            && in_array($params['state'], [UserDevice::STATE_AVAILABLE, UserDevice::STATE_ARCHIVE])
        ) {
            if ($device = UserDevice::find()->where([
                'user_id' => Yii::$app->user->id,
                'device_id' => $params['token']
            ])->one()) {
                $device->state = $params['state'];

                if ($device->update(false, ['state'])) {
                    return ['result' => 'success'];
                }
            } else {
                throw new ServerErrorHttpException(Yii::t('api', 'Device is empty'));
            }
        } else {
            throw new ServerErrorHttpException(Yii::t('api', 'Need token and state fields'));
        }

        throw new ServerErrorHttpException(Yii::t('api', 'Failed to update the object for unknown reason.'));
    }

    public function actionCheck()
    {
        return true;
    }

    /**
     * @param $id
     * @return UserResource
     * @throws HttpException
     */
    public function findModel($id)
    {
        $model = UserResource::findOne($id);
        if (!$model) {
            throw new HttpException(404, Yii::t('api', 'Not found'));
        }
        return $model;
    }

    private function getFileViewForEmail($nameView)
    {
        list($language) = explode('-', Yii::$app->language);

        $view = ($language ? $language . "/" : "") . $nameView;

        if (!file_exists(Yii::$app->basePath . '/mail/' . $view . '.php')) {
            if (!file_exists(Yii::$app->basePath . '/mail/' . $nameView . '.php')) {
                $view = null;
            } else {
                return $nameView;
            }
        }

        return $view;
    }

    public function actionTest()
    {
        $headers = \Yii::$app->request->headers;
        return [$headers, $_SERVER];
    }

    public function actionSendTestEmail($email)
    {
        // if (Yii::$app->commandBus->handle(new SendEmailCommand([
        //     'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
        //     'to' => $email,
        //     // 'subject' => Yii::t('frontend', '{name} - Please confirm your registration', ['name' => Yii::$app->name]),
        //     'subject' => 'Подтвердите адрес электронной почты для приложения «Тихое время»',
        //     'view' => $this->getFileViewForEmail("activateAccount"),
        //     'body' => null,
        //     'params' => ['user' => User::findOne(1)]
        // ]))) {
        if (Yii::$app->commandBus->handle(new SendEmailCommand([
            'from' => [Yii::$app->params['adminEmail'] => Yii::$app->name],
            'to' => $email,
            // 'subject' => Yii::t('frontend', '{name} - Reset your password', ['name'=>Yii::$app->name]),
            'subject' => 'Восстановление пароля от приложения «Тихое время»',
            'view' => $this->getFileViewForEmail("passwordResetToken"),
            'body' => null,
            'params' => ['user' => User::findOne(1)]
        ]))) {
            return ["resul" => "success"];
        } else {
            throw new \Exception("Not sent email");
        }
    }
}
