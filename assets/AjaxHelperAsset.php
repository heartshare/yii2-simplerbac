<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 27.03.15
 * Time: 16:45
 */

namespace insolita\simplerbac\assets;


use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;

class AjaxHelperAsset extends AssetBundle{

    public $sourcePath = '@vendor/bower/ajaxhelper/dist';
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $this->js[] = 'ajhelp' . $postfix . '.js';
        parent::init();
    }
}