<?php

/**
 * @var yii\web\View                         $this
 * @var yii\data\ActiveDataProvider          $dataProvider
 * @var insolita\simplerbac\models\RbacModel $model
 */

use yii\grid\GridView;
use insolita\simplerbac\RbacModule;

?>

<?=
GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            Yii::$app->getModule('simplerbac')->userPk,
            Yii::$app->getModule('simplerbac')->usernameAttribute,
            'role'=>[ 'format' => 'html',
                'value' => function ($data) {
                    return '<span class="label label-success">'
                    . implode(
                        '</span><br/><br/><span class="label label-primary">',
                        insolita\simplerbac\models\RbacModel::getUserRoles(
                            $data->{Yii::$app->getModule('simplerbac')->userPk}
                        )
                    )
                    . '</span>';
                }],
            'userperms' => [
                'format' => 'html',
                'value' => function ($data) {
                        return '<span class="label label-primary">'
                        . implode(
                            '</span><br/><br/><span class="label label-primary">',
                            insolita\simplerbac\models\RbacModel::getUserperms(
                                $data->{Yii::$app->getModule('simplerbac')->userPk}
                            )
                        )
                        . '</span>';
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Grant',
                'buttons' => [
                    'view' => function ($url, $model) {
                            $url = Yii::$app->getUrlManager()->createAbsoluteUrl(
                                [
                                    '/simplerbac/default/assign',
                                    'userid' => $model->{Yii::$app->getModule('simplerbac')->userPk}
                                ]
                            );
                            return \yii\helpers\Html::a(
                               '<i class="fa fa-link"></i>',
                                '#Assigs',
                                [
                                    'class'=>'btn btn-default',
                                    'title' => RbacModule::t('simplerbac','Grant'),
                                    'data-toggle' => 'modal',
                                    'data-backdrop' => false,
                                    'data-remote' => $url
                                ]
                            );
                        },
                ],
                'template' => '{view}'
            ],
        ],
    ]
); ?>