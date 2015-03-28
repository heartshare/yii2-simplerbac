<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
            'id' => 'addchild-form',
            'options' => [
                'class' => 'form-horizontal ajaxform',
                'data-errtarget'=>'div#ch-error',
                'data-oktarget'=>'#viewitem',
                'data-scroll'=>1,
            ],
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => \Yii::$app->getUrlManager()->createAbsoluteUrl('/simplerbac/default/addchild'),

        ]
    ); ?>
    <div class="alert alert-danger" id="ch-error" style="display: none;">...</div>
    <input type="hidden" name="RbacModel[type]" value="<?= $model->type; ?>">
    <input type="hidden" name="RbacModel[name]" value="<?= $model->name; ?>">
    <?= $form->field($model, 'new_child')->dropDownList($model->getItemsForAssign()); ?>
    <div class="form-group">
        <?= Html::submitButton(RbacModule::t('simplerbac','Add'), ['class' => 'btn btn-primary addchild']) ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>