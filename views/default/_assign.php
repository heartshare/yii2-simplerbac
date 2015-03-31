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
<div class="well well-small">

    <b><?= $user->{Yii::$app->getModule('simplerbac')->usernameAttribute}?></b>

    <div id="ldr" style="display:none"><i class="fa fa-circle-o-notch fa-2x fa-spin"></i>
        <?= RbacModule::t('simplerbac','Loading....') ?>

    </div>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'userassign-form',
            'options' => ['class' => 'form-horizontal ajaxform',
                'data-errtarget'=>'div#ch-error',
                'data-trigger'=>'modalform_submitted',
                'data-bsmodalid'=>'#Assigs',
                'data-scroll'=>1,
            ],
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => \Yii::$app->getUrlManager()->createAbsoluteUrl(
                    ['/simplerbac/default/assign', 'userid' => $user->{Yii::$app->getModule('simplerbac')->userPk}]
                ),

        ]
    ); ?>
    <div class="alert alert-danger" id="ch-error" style="display: none;">...</div>
    <?= $form->field($model, 'forassign')->dropDownList($model->getItemsForAssignUser($user->{Yii::$app->getModule('simplerbac')->userPk})); ?>
    <div class="form-group">
        <?= Html::submitButton(
            RbacModule::t('simplerbac','Update'),
            ['class' => 'btn btn-primary userassign', 'data-pjax' => 1]
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>