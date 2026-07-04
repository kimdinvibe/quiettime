<?php
/**
 * Created by IntelliJ IDEA.
 * User: admin
 * Date: 26.12.17
 * Time: 14:29
 */

namespace frontend\components;


use common\models\UserDevice;
use common\models\UserLog;
use Yii;
use yii\rest\ActiveController;
use yii\web\ServerErrorHttpException;

class ApiController extends ActiveController
{
    public $ignoreApiCheckKey = [];

    public function beforeAction($action)
    {
        if(parent::beforeAction($action))
        {
            if (!($this->ignoreApiCheckKey && in_array($action->id, $this->ignoreApiCheckKey))) {
                if(isset(Yii::$app->params['apiCheckKey']) && !isset(\Yii::$app->request->headers[\Yii::$app->params['apiCheckKey']])) {
                    throw new ServerErrorHttpException(Yii::t('api', 'Error with api key'));
                }
            }

            $params = [
                'queryParams' => \Yii::$app->request->queryParams,
//                'headers' => (array) \Yii::$app->request->headers,
//                'bodyParams' => \Yii::$app->request->bodyParams,
//                'fileParams' => $_FILES
            ];

            $headers = \Yii::$app->request->headers;

            Yii::$app->device->id = isset($headers['device-id'])?$headers['device-id']:null;
            Yii::$app->device->name = isset($headers['device-name'])?$headers['device-name']:null;
            Yii::$app->device->timezone = isset($headers['device-timezone'])?$headers['device-timezone']:null;
            Yii::$app->device->locale = isset($headers['device-locale'])?$headers['device-locale']:null;
            Yii::$app->device->datetime = isset($headers['device-datetime'])?$headers['device-datetime']:null;

            (new UserLog([
                'user_id' => !\Yii::$app->user->isGuest?\Yii::$app->user->id:null,
                'action' => \Yii::$app->controller->action->id,
                'controller' => \Yii::$app->controller->id,
                'headers' => serialize($headers),
                'params' => serialize($params),
                'device_id' => isset($headers['device-id'])?$headers['device-id']:null,
                'device_name' => isset($headers['device-name'])?$headers['device-name']:null,
                'device_type' => isset($headers['device-type'])?$headers['device-type']:null,
                'device_locale' => isset($headers['device-locale'])?$headers['device-locale']:null,
            ]))->save();

            if (isset($headers['device-locale']) && $headers['device-locale']) {
                Yii::$app->language = $headers['device-locale'];
            }

            if(!\Yii::$app->user->isGuest) {
                $this->saveDeviceForUser(Yii::$app->user->id);
            }

//            if(\Yii::$app->request->method == 'OPTIONS'){
//                var_dump(\Yii::$app->request); exit;
//            }



            return true;
        }

        return false;
    }

    function saveDeviceForUser($user_id) {
        $headers = \Yii::$app->request->headers;

        if(isset($headers['device-id']) && trim($headers['device-id'])) {
            $device_id = trim($headers['device-id']);

            if(!$model = UserDevice::find()->where([
                'user_id' => $user_id,
                'device_id' => $device_id
            ])->one()){
                $model = new UserDevice([
                    'user_id' => $user_id,
                    'device_id' => $device_id,
                    'locale' => Yii::$app->device->locale ?? 'en',
                ]);

                if ($model->save()) {
                    //
                }
            }

            // shit .... remove for other api
            $type = null;

            if(isset($headers['device-type']) && trim($headers['device-type'])) {
                if (strpos($headers['device-type'], 'Android') !== false) {
                    $type = "Android";
                } elseif (strpos($headers['device-type'], 'iOS') !== false) {
                    $type = "iOS";
                }
            }

            if ($type) {
                UserDevice::updateAll(['state' => UserDevice::STATE_ARCHIVE], ['and',
                    ['!=', 'device_id', $device_id],
                    ['like', 'device_type', $type.'%', false],
                    ['user_id' => $user_id]
                    ]);
            }
        }
    }
}