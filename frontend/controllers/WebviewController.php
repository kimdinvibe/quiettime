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
class WebviewController extends Controller
{
    /**
     * Displays a single Webview model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($code)
    {
        $this->layout = "_clear";

        return $this->render('view', [
            'model' => $this->findModel($code),
        ]);
    }

    /**
     * Finds the Webview model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Webview the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($code)
    {
        if (($model = Webview::find()->where([
            'code' => $code
            ])->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
