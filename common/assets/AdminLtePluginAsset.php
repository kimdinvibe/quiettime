<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/2/14
 * Time: 11:40 AM
 */

namespace common\assets;

use yii\web\AssetBundle;

class AdminLtePluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $js = [
        //'datatables/dataTables.bootstrap.min.js',
        // more plugin Js here
    ];
    public $css = [
        //'datatables/dataTables.bootstrap.css',
        // more plugin CSS here
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
            'yii\jui\JuiAsset',
            'yii\bootstrap\BootstrapPluginAsset',
            'common\assets\FontAwesome',
            'common\assets\JquerySlimScroll'
    ];
}
