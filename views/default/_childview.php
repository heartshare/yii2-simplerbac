<?php

/**
 * @var yii\web\View                         $this
 * @var insolita\simplerbac\models\RbacModel $model
 */
?>
<?= $this->render('_list', ['data' => $model->getChildrens(), 'model' => $model, 'ct' => $ct]); ?>
<br/>
<?= $this->render('_childform', ['model' => $model]); ?>
