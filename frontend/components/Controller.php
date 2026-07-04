<?php
 
namespace frontend\components;
 
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\LombardSeo;
 
class Controller extends \yii\web\Controller
{
    public $seo = null;
    
    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
    
        if (!parent::beforeAction($action)) {
            return false;
        }
    
        return true; // or false to not run the action
    }
}