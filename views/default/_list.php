<?php
use yii\helpers\Html;
use insolita\simplerbac\models\RbacModel;
use insolita\simplerbac\RbacModule;
use yii\helpers\Url;
/**
 * @var yii\web\View                         $this
 * @var insolita\simplerbac\models\RbacModel $model
 */
?>
<ul class="list-group">
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $subdata): ?>
            <li class="list-group-item">

                <div class="pull-right">
                    <?=
                    Html::button('<i class="fa fa-cog"></i>',
                        [
                            'title' => RbacModule::t('simplerbac','Info'),
                            'class' => 'aj',
                            'data-type' => $subdata->type,
                            'data-action' => Url::to(['/simplerbac/default/view']),
                            'data-name' => $subdata->name,
                            'data-oktarget'=>'#viewitem',
                            'data-scroll'=>1
                        ]
                    ); ?>

                    <?=
                    ($ct == 'childs')
                        ?
                        Html::button(
                            '<i class="fa fa-chain-broken"></i>',
                            [
                                'title' => RbacModule::t('simplerbac','Unlink'),
                                'class' => 'unchild',
                                'data-type' => $model->type,
                                'data-action' => Url::to(['/simplerbac/default/unchild']),
                                'data-name' => $model->name,
                                'data-new_child' => $subdata->name . '_t' . $subdata->type,
                                'data-oktarget'=>'#viewitem',
                                'data-errtarget'=>'#suberr',
                                'data-scroll'=>1
                            ]
                        )
                        : Html::button(
                        '<i class="fa fa-chain-broken"></i>',
                        [
                            'title' => RbacModule::t('simplerbac','Unlink'),
                            'class' => 'unchild',
                            'data-type' => $subdata->type,
                            'data-action' => Url::to(['/simplerbac/default/unchild']),
                            'data-name' => $subdata->name,
                            'data-new_child' => $model->name . '_t' . $model->type,
                            'data-oktarget'=>'#viewitem',
                            'data-errtarget'=>'#suberr',
                            'data-scroll'=>1
                        ]
                    );
                    ?>
                </div>
                <?= $subdata->name . '(' . $subdata->description . ') ' ?> <span
                    class="label label-info"><?= $model->typenames[$subdata->type] ?></span>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="list-group-item"><?= RbacModule::t('simplerbac','No items') ?></li>
    <?php endif; ?>
</ul>
