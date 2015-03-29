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

    public $sourcePath = '@insolita/simplerbac/assets/js';
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $this->js[] = 'ajhelpjs' . $postfix . '.js';
        parent::init();
    }
}