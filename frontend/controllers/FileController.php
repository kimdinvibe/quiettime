<?php

namespace frontend\controllers;

use Yii;
use common\models\Webview;
use backend\models\search\WebviewSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WebviewController implements the CRUD actions for Webview model.
 */
class FileController extends Controller
{
    public function actionThumb($source, $width = 100, $height = null, $crop = false)
    {
        $file = null;

        if($path = explode(getenv('STORAGE_URL'), $source)){
            if(count($path) > 1){
                //$path = Yii::getAlias('@webroot').'/storage/web'.$path[1];
                $path = $path[1];
                $file = $_SERVER['DOCUMENT_ROOT'].$path;

                if ($crop === 0) {
                    $crop = false;
                }

                if($url = \frontend\helpers\Image::thumbFromPath($path, $width, $height, $crop)){
                    $file = $_SERVER['DOCUMENT_ROOT'].$url;

                    if (file_exists($file)) {
                        $source = $file;
                    }
                }
            }
        }

        if ($file) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            ob_clean();
            flush();
            readfile($file);
            exit;
        } else {
            throw new NotFoundHttpException;
        }
    }
}
