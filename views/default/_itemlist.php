<?php
use yii\helpers\Html;
use insolita\simplerbac\models\RbacModel;
use insolita\simplerbac\RbacModule;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var insolita\simplerbac\models\RbacModel $model
 */
?>
<ul class="list-group">
    <?php
    $data = ($model->type == RbacModel::TYPE_PERMISSION) ? $model->getPerms() : $model->getRoles();
    ?>
    <?php foreach ($data as $subdata): ?>
        <li class="list-group-item">

            <div class="pull-right">
                <?= Html::button(
                    '<i class="fa fa-cog"></i>',
                    [
                        'title' => RbacModule::t('simplerbac','Info'),
                        'class' => 'aj',
                        'data-type' => $subdata->type,
                        'data-action' => Url::to(['/simplerbac/default/view']),
                        'data-name' => $subdata->name,
                        'data-oktarget'=>'#viewitem',
                        'data-scroll'=>1
                    ]
                ) ?>
                <?= Html::button(
                    '<i class="fa fa-pencil"></i>',
                    [
                        'title' => RbacModule::t('simplerbac','Edit'),
                        'class' => 'aj',
                        'data-type' => $subdata->type,
                        'data-action' => Url::to(['/simplerbac/default/upditem']),
                        'data-name' => $subdata->name,
                        'data-oktarget'=>'#rbacform'
                    ]
                ) ?>
                <?= Html::button(
                    '<i class="fa fa-trash-o"></i>',
                    [
                        'title' => RbacModule::t('simplerbac','Remove'),
                        'class' => 'aj',
                        'data-type' => $subdata->type,
                        'data-action' => Url::to(['/simplerbac/default/delete']),
                        'data-name' => $subdata->name,
                        'data-trigger'=>'rbacitem_update'
                    ]
                ) ?>
            </div>
            <?= $subdata->name . '(' . $subdata->description . ')' ?>
        </li>
    <?php endforeach; ?>
</ul>
