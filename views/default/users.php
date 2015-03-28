<?php

use insolita\simplerbac\RbacModule;
use yii\helpers\Html;

/**
 * @var yii\web\View                         $this
 * @var yii\data\ActiveDataProvider          $dataProvider
 * @var insolita\simplerbac\models\RbacModel $model
 */

$this->title = RbacModule::t('simplerbac', 'Assign Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a(
    RbacModule::t('simplerbac', 'Roles and operations'), ['/simplerbac/default/index'], ['class' => 'btn btn-primary']
) ?> &nbsp;
<?= Html::a(
    RbacModule::t('simplerbac', 'Assign roles'), ['/simplerbac/default/users'], ['class' => 'btn btn-primary']
) ?> &nbsp;
<?= Html::a(
    RbacModule::t('simplerbac', 'RBAC Graph'), ['/simplerbac/default/all-items'], ['class' => 'btn btn-primary']
) ?> &nbsp;
<?= Html::a(
    RbacModule::t('simplerbac', 'Users Graph'), ['/simplerbac/default/all-users'], ['class' => 'btn btn-primary']
) ?>

    <div class="rbacuser-index">
        <div class="page-header"><h1><?= Html::encode($this->title) ?></h1></div>
        <?php \yii\widgets\Pjax::begin(['id' => 'userpjax', 'timeout' => 5000]); ?>
        <?= $this->render('_usergrid', ['model' => $model, 'dataProvider' => $dataProvider]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>

<?php \yii\bootstrap\Modal::begin(['header' => RbacModule::t('simplerbac', 'Assign to user'), 'id' => 'Assigs']) ?>
<?php \yii\bootstrap\Modal::end() ?>

<?php
$js
    = <<<JS
$(document).on("click","[data-remote]",function(e) {
e.preventDefault();
$.ajax({url: $(this).data('remote'), type: "GET",async:true,success: function(response){if(response.result){jQuery("div#Assigs .modal-body").html(response.result);}}});
});
$('#Assigs').on('hidden.bs.modal', function (e) {
  $("div#Assigs .modal-body").html('');
});

$('[rel="popover"]').popover();

 $(document).on('submit',"form#userassign-form",function(e){
          if($(this).find('.has-error').length) {
                        return false;
                }
                $('#ldr').show();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    dataType: "json",
                    async:true,
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.state=='success'){
                         $("#ch-error").hide();
                          $("div#Assigs .modal-body").html('');
                          $("#Assigs").modal('hide');
                          $.pjax.reload({container:'#userpjax'});
                        }else{
                           $("div#ch-error").html(response.error);
                           $("div#ch-error").show();
                           $('#ldr').hide();
                        }
                    },
                    error: function(response) {
                            if(typeof(response) == 'object' && response.responseJSON){
                            err='Server Error '+response.responseJSON.code+ response.responseJSON.message;
                        }else{
                            err=response;
                        }
                        alert(err);
                    }
                });

                return false;
     });
JS;

$this->registerJs($js);
?>