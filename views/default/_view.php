<?php
use insolita\simplerbac\RbacModule;

/**
 * @var yii\web\View $this
 * @var insolita\simplerbac\models\RbacModel $model
 */
?>
<hr/>
<h3><span class="label label-info"><?= $model->typenames[$model->type] ?></span>
    <?= $model->name . '(' . $model->description . ') ' ?>
</h3>
<?php if ($model->type == \insolita\simplerbac\models\RbacModel::TYPE_ROLE): ?>
    <div class="well well-transparent well-small">
        <h4><?=\insolita\simplerbac\RbacModule::t('simplerbac','Rights available for this role')?>:</h4>
        <span class="label label-success"><?= implode(
                '</span><br/><span class="label label-success">',
                \insolita\simplerbac\models\RbacModel::getRoleperms($model->name)
            ) ?></span>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><?= RbacModule::t('simplerbac','Inherits the rights of:') ?></div>
            <div class="panel-body" style="overflow: auto; max-height: 650px;" id="parentlist">
                <?= $this->render('_childview', ['model' => $model, 'ct' => 'childs', 'model' => $model]); ?>

            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><?= RbacModule::t('simplerbac','Grants rights to:'); ?></div>
            <div class="panel-body" style="overflow: auto; max-height: 650px;" id="childlist">
                <?= $this->render('_list', ['data' => $model->getParents(), 'ct' => 'parents', 'model' => $model]); ?>
            </div>
        </div>
    </div>
</div>