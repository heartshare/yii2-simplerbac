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

class CytoscapeAsset extends AssetBundle{

    public $sourcePath = '@vendor/bower/cytoscape/dist';

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $this->js[] = 'cytoscape' . $postfix . '.js';

        parent::init();
    }
}