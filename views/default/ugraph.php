<?php
/**
 * @var yii\web\View                                   $this
 * @var insolita\simplerbac\controllers\RbacController $controller
 * @var insolita\simplerbac\models\RbacModel           $model
 * @var string                                         $type  //type of rbac item - role or perm
 * @var array                                          $roles //list all roles
 * @var array                                          $perms //list all perms
 */
use yii\helpers\Html;
use insolita\simplerbac\RbacModule;
use yii\web\View;

$this->registerAssetBundle('\yii\web\JqueryAsset', View::POS_HEAD);
$this->registerAssetBundle('\insolita\simplerbac\assets\CytoscapeAsset', View::POS_HEAD);

$this->title = RbacModule::t('simplerbac', 'Users Graph');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a(RbacModule::t('simplerbac', 'Roles and operations'), ['/simplerbac/default/index'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'Assign roles'), ['/simplerbac/default/users'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'RBAC Graph'), ['/simplerbac/default/all-items'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'Users Graph'), ['/simplerbac/default/all-users'], ['class' => 'btn btn-primary']) ?>

<div id="cy">

</div>
<?php
$this->registerCss('#cy {
  height: 90%;
  width: 90%;
  position: absolute;
  left: 0;
  top: 70;
  bottom:15;
}');

$js = <<<JS
jQuery(function(){
var elems=$elems;
    $('#cy').cytoscape({
      layout: {
          name: 'breadthfirst',
         directed: true,
        padding: 15
      },

      style: cytoscape.stylesheet()
        .selector('node')
          .css({
            'shape': 'data(faveShape)',
            'width': 'mapData(data(width), 1, 10, 1, 10)',
            'content': 'data(id)',
            'text-valign': 'center',
            'text-outline-width': 2,
            'text-outline-color': 'data(faveColor)',
            'background-color': 'data(faveColor)',
            'color': '#fff'
          })
        .selector(':selected')
           .css({
            'border-width': 7,
            'border-color': 'yellow'
          })
        .selector('edge')
          .css({
            'opacity': 0.666,
             'width': 'mapData(30, 70, 100, 2, 6)',
            'target-arrow-shape': 'triangle',
            'source-arrow-shape': 'circle',
            'line-color': 'data(faveColor)',
            'source-arrow-color': 'data(faveColor)',
            'target-arrow-color': 'data(faveColor)'
          }),


      elements: elems,

      ready: function(){
        window.cy = this;
      }
    });
});

JS;

$this->registerJs($js, \yii\web\View::POS_HEAD);
?>