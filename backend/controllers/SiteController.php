<?php
namespace backend\controllers;

use common\components\keyStorage\FormModel;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->layout = Yii::$app->user->isGuest || !Yii::$app->user->can('loginToBackend') ? 'base' : 'common';
        return parent::beforeAction($action);
    }

    public function actionSettings()
    {
        $model = new FormModel([
            'keys' => [
                'frontend.maintenance' => [
                    'label' => Yii::t('backend', 'Frontend maintenance mode'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'disabled' => Yii::t('backend', 'Disabled'),
                        'enabled' => Yii::t('backend', 'Enabled')
                    ]
                ],
                'backend.theme-skin' => [
                    'label' => Yii::t('backend', 'Backend theme'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'skin-black' => 'skin-black',
                        'skin-blue' => 'skin-blue',
                        'skin-green' => 'skin-green',
                        'skin-purple' => 'skin-purple',
                        'skin-red' => 'skin-red',
                        'skin-yellow' => 'skin-yellow'
                    ]
                ],
                'backend.layout-fixed' => [
                    'label' => Yii::t('backend', 'Fixed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-boxed' => [
                    'label' => Yii::t('backend', 'Boxed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-collapsed-sidebar' => [
                    'label' => Yii::t('backend', 'Backend sidebar collapsed'),
                    'type' => FormModel::TYPE_CHECKBOX
                ]
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'body' => Yii::t('backend', 'Settings was successfully saved'),
                'options' => ['class' => 'alert alert-success']
            ]);
            return $this->refresh();
        }

        return $this->render('settings', ['model' => $model]);
    }

    public function actionIndex()
    {
        return $this->render('index', []);
    }

    public function actionRefreshStatistics($from, $to) {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($locations = \common\models\Location::find()->active()->all()){
                $from = str_replace(".", "-", $from.' 00:00:00');
                $to = str_replace(".", "-", $to.' 00:00:00');

//                if($from == $to) {
//                    $to = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($from)));
//                }

                foreach ($locations as $key => $item) {
                    $statisticsLocal = \backend\components\Statistic::getMainStatisticsForLocation($item, strtotime($from), strtotime($to));

                    $statistics[$item->id] = [
                        'graph' => $statisticsLocal,
                        'eventView' => Yii::$app->controller->view->render('/site/_event-item', [
                            'item' => &$item,
                            'statistics' => &$statisticsLocal
                        ])
                    ];
                }
            }

            return [
                'from' => $from,
//                'fromUnix' => strtotime($from.' 00:00:00'),
                'to' => $to,
//                'toUnix' => strtotime($to.' 00:00:00'),
                'statistics' => $statistics
            ];
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }

    public function actionRefreshStatisticsOneLine($from, $to, $model, $itemtitle) {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $from = str_replace(".", "-", $from.' 00:00:00');
            $to = str_replace(".", "-", $to.' 00:00:00');

            $statistics = \backend\components\Statistic::getStatisticForTemplateModelByDays($model, $itemtitle, strtotime($from), strtotime($to));

            return [
                'from' => $from,
//                'fromUnix' => strtotime($from.' 00:00:00'),
                'to' => $to,
//                'toUnix' => strtotime($to.' 00:00:00'),
                'model' => $model,
                'itemtitle' => $itemtitle,
                'statistics' => $statistics
            ];
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }
}
