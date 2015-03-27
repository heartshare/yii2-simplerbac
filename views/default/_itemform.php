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
            'id' => 'addrbac-form',
            'options' => [
                'class' => 'form-horizontal ajaxform',
                'data-errtarget'=>'div#add-error',
                'data-trigger'=>'rbacitem_update',
                'data-scroll'=>1

            ],
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'action' => \Yii::$app->getUrlManager()->createAbsoluteUrl('/simplerbac/default/additem'),

        ]
    ); ?>
    <div class="alert alert-danger" id="add-error" style="display: none;">...</div>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'description') ?>
    <?= $form->field($model, 'type')->dropDownList(
        [
            RbacModel::TYPE_ROLE => Yii::t('app', 'Роль', [], 'ru'),
            RbacModel::TYPE_PERMISSION => RbacModule::t('simplerbac','Операция')
        ]
    ) ?>
    <div class="form-group">
        <?= Html::submitButton(
            '<span id="ldr" style="display: none;"><i class="fa fa-circle-o-notch fa-2x fa-spin"></i></span>'
            . RbacModule::t('simplerbac','Добавить'),
            ['class' => 'btn btn-primary addbtn']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
