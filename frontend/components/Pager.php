<?php
 
namespace frontend\components;
 
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
 
class Pager extends \yii\widgets\LinkPager
{
    public $prevPageLabel = false;
    public $nextPageLabel = false;
    public $firstPageLabel = '<<';
    public $lastPageLabel = '>>';
    public $maxButtonCount = 8;
    
    public function init()
    {
        parent::init();        
        $this->pagination->forcePageParam = false;
        
        if($this->pagination->getPage()){
            Yii::$app->view->registerLinkTag([
                'rel' => 'canonical', 
                //'href' => Url::canonical(),
                'href' => Url::to(['index'], true),
                'id' => 'canonical'
            ], 'canonical');
        }
    }
    
    protected function renderPageButtons(){
        return parent::renderPageButtons();
    }
}