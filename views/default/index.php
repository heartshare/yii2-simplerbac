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
\insolita\simplerbac\assets\AjaxHelperAsset::register($this);
$this->title = RbacModule::t('simplerbac', 'Roles and operations');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a(RbacModule::t('simplerbac', 'Roles and operations'), ['/simplerbac/default/index'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'Assign roles'), ['/simplerbac/default/users'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'RBAC Graph'), ['/simplerbac/default/all-items'], ['class' => 'btn btn-primary']) ?> &nbsp;
<?= Html::a(RbacModule::t('simplerbac', 'Users Graph'), ['/simplerbac/default/all-users'], ['class' => 'btn btn-primary']) ?>

    <div class="col-lg-12" id="pagetop">
    <div class="page-header"><h1><?= RbacModule::t('simplerbac', 'Roles and operations')?></h1></div>

    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-plus-circle"></i> <?= RbacModule::t('simplerbac','Add Item') ?>
                </div>
                <div class="panel-body" id="rbacform">
                    <?= $this->render('_itemform', ['model' => $model]); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-info">
                <div class="panel-heading"><?= RbacModule::t('simplerbac','All Roles') ?></div>
                <?php $model->type = \insolita\simplerbac\models\RbacModel::TYPE_ROLE; ?>
                <div class="panel-body rbaclist" style="overflow: auto; max-height: 650px;" id="rolelist">
                    <?= $this->render('_itemlist', ['model' => $model]); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-warning">
                <div class="panel-heading"><?= RbacModule::t('simplerbac','All operations') ?></div>
                <?php $model->type = \insolita\simplerbac\models\RbacModel::TYPE_PERMISSION; ?>
                <div class="panel-body rbaclist" style="overflow: auto; max-height: 650px;" id="permlist">
                    <?= $this->render('_itemlist', ['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div id="suberr" style="display: none"></div>
    <div id="vldr" style="display:none"><i class="fa fa-circle-o-notch fa-2x fa-spin"></i>
        <?= RbacModule::t('simplerbac','Loading....') ?>
    </div>
    <div class="col-lg-12" id="viewitem">

    </div>
</div>
<div class="pull-right offset-1" id="totop"><?= Html::button('<i class="fa fa-4x fa-arrow-circle-o-up"></i>',[]) ?></div>
<?php
$loadroles=\yii\helpers\Url::to(['/simplerbac/default/loadroles'],true);
$loadperms=\yii\helpers\Url::to(['/simplerbac/default/loadperms'],true);
$js = <<<JS
$.ajaxHelper('button.aj','click','send',[{loader:'#ldr'}]);
$.ajaxHelper('#totop','click','scroll','#pagetop');
$.ajaxHelper('button.unchild','click','send',[{loader:'#vldr'}]);
$.ajaxHelper('.ajaxform','submit');
$(document).on('rbacitem_update',function(e){
   $('#rolelist').ajaxHelper('send',{loader:false, oktarget:'#rolelist', action:"$loadroles"});
   $('#permlist').ajaxHelper('send',{loader:false, oktarget:'#permlist', action:"$loadperms"});
});
JS;

$this->registerJS($js);
?>