<?php

namespace frontend\modules\api\v1\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use common\models\Message;
use yii\web\HttpException;

use common\models\MessageRoom;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use frontend\components\ApiController;

use frontend\filters\auth\HttpBearerAuth;
use frontend\modules\api\v1\resources\Message as MessageResource;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class FileController extends ApiController
{
    public $modelClass = 'frontend\modules\api\v1\resources\Message';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                QueryParamAuth::className()
            ],
            'except' => ['thumb', 'upload-guest']
        ];

        //\yii\helpers\VarDumper::dump($behaviors, 10, true); exit;

        return $behaviors;
    }

    /* Declare methods supported by APIs */
    protected function verbs()
    {
        return [
            'upload-base64' => ['POST'],
            'upload' => ['POST'],
            'thumb' => ['GET']
        ];
    }

    /**
     * @inheritdoc
     */
    /*public function actions()
    {
        return [

        ];
    }*/

    public function actionUploadBase64()
    {
        $params = Yii::$app->request->getBodyParams();

        if ($params['name']) {
            $params['name'] = trim($params['name']);
        }

        //$params['body'] = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../../IqgvB9Cp8Dc.jpg'));

        if ($params['body']) {
            $params['body'] = base64_decode($params['body']);
        }

        if ($params['name'] && $params['body']) {
            try {
                $type = explode('.', $params['name']);

                if (count($type) > 1) {
                    $type = '.' . $type[count($type) - 1];
                } else {
                    $type = '';
                }

                $filename = 'api/' . md5(time() . $params['name']) . $type;
                $path = getenv('STORAGE_URL') . '/source';

                if (file_put_contents($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename, $params['body'])) {
                    \frontend\helpers\Image::imageFixOrientationAndReloadFile($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename);

                    //$size = getimagesize($_SERVER['DOCUMENT_ROOT'].$path.'/'.$filename, $info);
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename);
                    finfo_close($finfo);

                    $size = filesize($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename);

                    $model = new \frontend\modules\api\v1\resources\FileStorageItem([
                        'component' => 'fileStorage',
                        'base_url' => $path,
                        'path' => $filename,
                        'type' => $mime,
                        'size' => $size,
                        'name' => $params['name']
                    ]);

                    if ($model->save()) {
                        return $model;
                    } else {
                        return [
                            "name" => "Error saved model",
                            "message" => Yii::t("api", "Failed to save"),
                            "errors" => $model->errors,
                        ];
                    }
                } else {
                    return [
                        "name" => "Error file copy",
                        "message" => Yii::t("api", "Failed to save file")
                    ];
                }
            } catch (Exception $e) {
                throw new BadRequestHttpException(Yii::t("api", $e->getMessage()));
            }
        } else {
            throw new BadRequestHttpException(Yii::t("api", "There are no required parameters: {:params}", [':params' => 'name, body']));
        }

        throw new NotFoundHttpException;
    }

    /**
     * @param $name
     * @param null $title
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpload($name, $title = null)
    {

        if (isset($_FILES[$name])) {

            if ($_FILES[$name]['error']) {
                throw new BadRequestHttpException(Yii::t("api", "Failed to save file [file empty]"));
            }

            try {
                $type = explode('.', $_FILES[$name]['name']);

                if (count($type) > 1) {
                    $type = '.' . $type[count($type) - 1];
                } else {
                    $type = '';
                }

                $filename = 'api/' . md5(time() . ($title ?: $_FILES[$name]['name'])) . $type;
                $path = getenv('STORAGE_URL') . '/source';

                if (move_uploaded_file($_FILES[$name]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename)) {
                    \frontend\helpers\Image::imageFixOrientationAndReloadFile($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename);

                    $model = new \frontend\modules\api\v1\resources\FileStorageItem([
                        'component' => 'fileStorage',
                        'base_url' => $path,
                        'path' => $filename,
                        'type' => $_FILES[$name]['type'],
                        'size' => $_FILES[$name]['size'],
                        'name' => $title ? $title : $_FILES[$name]['name']
                    ]);

                    if ($model->save()) {
                        return $model;
                    } else {
                        return [
                            "name" => "Error saved model",
                            "message" => Yii::t("api", "Failed to save"),
                            "errors" => $model->errors,
                        ];
                    }
                } else {
                    return [
                        "name" => "Error file copy",
                        "message" => Yii::t("api", "Failed to save file")
                    ];
                }
            } catch (Exception $e) {
                throw new BadRequestHttpException(Yii::t("api", $e->getMessage()));
            }
        }

        throw new NotFoundHttpException;
    }

    /**
     * @param $name
     * @param null $title
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUploadMultiple($name, $title = null)
    {
        if (isset($_FILES[$name])) {
            foreach ($_FILES[$name]['error'] as $isError) {
                if ($isError) {
                    throw new BadRequestHttpException(Yii::t("api", "Failed to save file [file empty]"));
                }
            }

            try {
                $result = [];

                foreach ($_FILES[$name]['name'] as $index => $nameFile) {
                    $type = explode('.', $name);

                    if (count($type) > 1) {
                        $type = '.' . $type[count($type) - 1];
                    } else {
                        $type = '';
                    }

                    $filename = 'api/' . md5(time() . ($title ?: $nameFile)) . $type;
                    $path = getenv('STORAGE_URL') . '/source';

                    // return $_FILES[$name]['tmp_name'][$index];

                    if (move_uploaded_file($_FILES[$name]['tmp_name'][$index], $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename)) {
                        \frontend\helpers\Image::imageFixOrientationAndReloadFile($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename);

                        $model = new \frontend\modules\api\v1\resources\FileStorageItem([
                            'component' => 'fileStorage',
                            'base_url' => $path,
                            'path' => $filename,
                            'type' => $_FILES[$name]['type'][$index],
                            'size' => $_FILES[$name]['size'][$index],
                            'name' => $title ? $title : $name
                        ]);

                        if ($model->save()) {
                            $result[] = $model;
                        } else {
                            $result[] = [
                                "name" => "Error saved model",
                                "message" => Yii::t("api", "Failed to save"),
                                "errors" => $model->errors,
                            ];
                        }
                    } else {
                        $result[] = [
                            "name" => "Error file copy",
                            "message" => Yii::t("api", "Failed to save file")
                        ];
                    }
                }

                return $result;
            } catch (Exception $e) {
                throw new BadRequestHttpException(Yii::t("api", $e->getMessage()));
            }
        }

        throw new NotFoundHttpException;
    }

    public function actionUploadGuest($name, $title = null)
    {
        return $this->actionUpload($name, $title);
    }

    public function actionThumb($source, $width = 100, $height = null, $crop = false)
    {
        if ($path = explode(getenv('STORAGE_URL'), $source)) {
            if (count($path) > 1) {
                //$path = Yii::getAlias('@webroot').'/storage/web'.$path[1];
                $path = $path[1];
                if ($url = \frontend\helpers\Image::thumbFromPath($path, $width, $height, $crop)) {
                    return [
                        'result' => 'success',
                        'source' => $source,
                        'thumb' => Url::to($url, true)
                    ];
                }
            }
        }

        throw new NotFoundHttpException;
    }
}
