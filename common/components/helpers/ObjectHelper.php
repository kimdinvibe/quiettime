<?php
namespace common\components\helpers;

class ObjectHelper
{    
    static function getListLombardsWithSettings($query, $listFileds = array(
        'city',
        'district',
        'metro',
        'type'
    ), $model = null)
    {
        if($listFileds)
        {
            if($model){
                $list = $model;
            }else{
                $list = $query->asArray()->all();
            }
            
            if($list){
                foreach($listFileds as $el){
                    $listOut[$el] = self::getListParamsFromModelForColumn($list, $el);
                }
                
                return $listOut;
            }
        }
        
        return array();
    }
    
    static function convertStringToAssocArray($str, $split = ';')
    {
        $str = trim($str);
        
        if($list = explode($split, $str)){
            foreach($list as $el){
                if(trim($el)){
                    $listOut[$el] = trim($el);
                }
            }
            
            return $listOut;
        }
        
        return null;
    }
    
    static function getListParamsFromModelForColumn(&$list, $nameField)
    {
        if($list){
            $listOut = array();
            
            foreach($list as $key => $val){
                if($listAdd = self::convertStringToAssocArray($el->$nameField)){
                    $listOut = array_merge($listOut, $listAdd);
                }
            }
            
            asort($listOut);
            
            return $listOut;
        }
        
        return null;
    }
    
    static function randIdModels($model, $count = 7, $query = null)
    {
        if(!$query){
            $query = $model::find();
        }        
        
        $query->asArray();
        
        if($list = $query->all())
        {
            $listId = array();
            
            while (count($listId)<$count && count($list)>0) {
                $cur = rand(0,count($list)-1);
                $listId[] = $list[$cur]['id'];
                array_splice($list, $cur, 1);
            }
            
            return $listId;
        }
        
        return null;
    }
}
?>