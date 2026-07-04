<?php

namespace frontend\controllers;

use common\models\Message;
use Yii;
use common\models\Webview;
use backend\models\search\WebviewSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WebviewController implements the CRUD actions for Webview model.
 */
class MessageController extends Controller
{
    /**
     * Displays a single Webview model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = "_clear";

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Webview model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Webview the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::find()->where([
            'id' => $id
            ])->sent()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
