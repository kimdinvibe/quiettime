<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $css = [
        'css/style.css',
        'css/backend.css',
        'css/morris.css',
        '//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css',
        'js/bootstrap-daterangepicker/daterangepicker.css',
        'css/simple-calendar.css',
    ];
    public $js = [
        'js/app.js',
        //'/frontend/media/js/bootstrap-3-typeahead/bootstrap3-typeahead.min.js',
        'js/jquery.md5.js',
        'js/raphael.min.js',
        'js/morris.min.js',
        'js/graph.js',
        'js/moment/min/moment.min.js',
        'js/bootstrap-daterangepicker/daterangepicker.js',
        'js/backend.js',
        'js/jquery.simple-calendar.js',
        'js/calendar.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'common\assets\AdminLte',
        'common\assets\AdminLtePluginAsset',
        'common\assets\Html5shiv'
    ];
}
