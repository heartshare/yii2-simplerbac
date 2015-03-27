<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use insolita\simplerbac\models\RbacModel;
use insolita\simplerbac\RbacModule;

/**
 * @var yii\web\View                         $this
 * @var yii\widgets\ActiveForm               $form
 * @var yii\widgets\ActiveField              $field
 * @var insolita\simplerbac\models\RbacModel $model
 */
?>
<div class="col-lg-10">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'editrbac-form',
            'options' => [
                'class' => 'form-horizontal ajaxform',
                'data-scroll'=>1,
                'data-oktarget'=>'#rbacform',
                'data-errtarget'=>'div#add-error',
                'data-trigger'=>'rbacitem_update'
            ],
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'action' => \Yii::$app->getUrlManager()->createAbsoluteUrl('/simplerbac/default/rename'),

        ]
    ); ?>
    <div class="alert alert-danger" id="add-error" style="display: none;">...</div>
    <li class="list-group-item"><?= $model->name; ?></li>
    <?= $form->field($model, 'description') ?>
    <input type="hidden" name="RbacModel[type]" value="<?= $model->type; ?>">
    <input type="hidden" name="RbacModel[name]" value="<?= $model->name; ?>">

    <div class="form-group">
        <?= Html::submitButton(
            '<span id="ldr" style="display: none;"><i class="fa fa-circle-o-notch fa-2x fa-spin"></i></span>'
            . RbacModule::t('simplerbac','Update'),
            ['class' => 'btn btn-primary editbtn']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
