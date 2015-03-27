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

class RbacAsset extends AssetBundle{

    public $sourcePath = '@insolita/simplerbac/assets/js';
    public $js= ['rbacjs.js'];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}