<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 27.03.15
 * Time: 16:04
 */

namespace insolita\simplerbac\controllers;

use insolita\simplerbac\models\RbacModel;
use insolita\simplerbac\RbacModule;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{

    public $defaultAction = 'index';
    public $enableCsrfValidation = false;

    public function behaviors()
    {

        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'except' => ['index', 'convert', 'users', 'all-items', 'all-users'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'ru',
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'additem' => ['post'],
                    'addchild' => ['post'],
                    'unchild' => ['post'],
                    'loadroles' => ['post'],
                    'loadperms' => ['post'],
                    'rename' => ['post'],
                    'delete' => ['post'],
                    'assign' => ['post'],
                    'assign-form' => ['post']
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $model = new RbacModel();
        return $this->render('index', ['model' => $model]);
    }

    public function actionAdditem()
    {
        $model = new RbacModel();
        $model->scenario = 'additem';
        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->saveItem()) {
            $result = ['state' => 'success'];
            return $result;
        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' => $error];
            return $result;
        }

    }

    public function actionAddchild()
    {
        $model = new RbacModel();
        $model->scenario = 'addchild';
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->addChild() === true) {
                $item = $model->getItem($model->name, $model->type);
                $model->description = $item->description;
                $upd = $this->renderAjax('_view', ['model' => $model]);
                $result = ['state' => 'success', 'result' => $upd];
                return $result;
            } else {
                $result = [
                    'state' => 'error',
                    'error' => 'Нельзя унаследовать от этого элемента, нарушение иерархии'
                ];
                return $result;
            }

        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' => $error];
            return $result;
        }
    }

    public function actionUnchild()
    {
        $model = new RbacModel();
        $model->scenario = 'unchild';
        if ($model->load(\Yii::$app->request->post(), '') && $model->validate()) {
            if ($model->unChild()) {
                $item = $model->getItem($model->name, $model->type);
                $model->description = $item->description;
                $upd = $this->renderAjax('_view', ['model' => $model]);
                $result = ['state' => 'success', 'result' => $upd];
                return $result;
            } else {
                $result = [
                    'state' => 'error',
                    'error' => \insolita\simplerbac\RbacModule::t('simplerbac', 'Cant inherit from this item')
                ];
                return $result;
            }

        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' => $error];
            return $result;
        }
    }

    public function actionLoadroles()
    {
        $model = new RbacModel();
        $model->type = RbacModel::TYPE_ROLE;
        return ['state' => 'success', 'result' => $this->renderAjax('_itemlist', ['model' => $model])];
    }

    public function actionLoadperms()
    {
        $model = new RbacModel();
        $model->type = RbacModel::TYPE_PERMISSION;
        return ['state' => 'success', 'result' => $this->renderAjax('_itemlist', ['model' => $model])];
    }

    /**
     * Просмотр роли или правила с возможностью назначить дочерние правила/роли
     * */
    public function actionView()
    {
        $model = new RbacModel();
        $model->scenario = 'view';
        if ($model->load(\Yii::$app->request->post(), '') && $model->validate()) {
            $item = $model->getItem($model->name, $model->type);
            $model->description = $item->description;
            return ['state' => 'success', 'result' => $this->renderAjax('_view', ['model' => $model])];
        }
    }

    public function actionDelete()
    {
        $model = new RbacModel();
        $model->scenario = 'delete';
        if ($model->load(\Yii::$app->request->post(), '') && $model->validate()) {
            $model->deleteItem();
            $result = ['state' => 'success'];
            return $result;
        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' => $error];
            return $result;
        }
    }

    public function actionUpditem()
    {
        $model = new RbacModel();
        $model->scenario = 'edititem';
        $item=$model->getItem(\Yii::$app->request->post('name'), \Yii::$app->request->post('type'));
        if ($item) {
            $model->populateItem($item);
             $result = [
                'state' => 'success',
                'result' => $this->renderAjax('_editform', ['model' => $model])
            ];
            return $result;
        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' => $error];
            return $result;
        }
    }

    public function actionRename()
    {
        $model = new RbacModel();
        $model->scenario = 'edititem';
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->updateItem();
            $nmodel = new RbacModel();
            $result = [
                'state' => 'success',
                'result' => $this->renderAjax('_itemform', ['model' => $nmodel])
            ];
            return $result;
        } else {
            $error = Html::errorSummary($model);
            $result = ['state' => 'error', 'error' =>  $model->scenario.$error];
            return $result;
        }
    }

    public function actionUsers()
    {

        $model = new RbacModel();
        $dataProvider = $model->getUsersDp(\Yii::$app->request->getQueryParams());
        if (!\Yii::$app->request->isPjax) {
            return $this->render(
                'users',
                [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                ]
            );
        } else {
            return $this->renderAjax(
                '_usergrid',
                [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                ]
            );
        }

    }


    public function actionAssign($userid)
    {
        $model = new RbacModel();
        $user = $this->findUser($userid);
        $model->scenario = 'userassign';
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
                $assig = $model->userAssign($userid);
                if (!$assig) {
                    $result = ['state' => 'false', 'error' => $assig];
                    return $result;
                } else {
                    $result = ['state' => 'success', 'error' => ''];
                    return $result;
                }
            } else {
                $result = ['state' => 'false', 'error' => Html::errorSummary($model)];
                return $result;
            }
        }
        return ['state' => 'success', 'result' => $this->renderAjax('_assign', ['model' => $model, 'user' => $user])];
    }
    public function actionAssignForm($userid)
    {
        $model = new RbacModel();
        $user = $this->findUser($userid);
        $model->scenario = 'userassign';
        return ['state' => 'success', 'result' => $this->renderAjax('_assign', ['model' => $model, 'user' => $user])];
    }

    public function actionConvert()
    {
        \Yii::$app->authManager->removeAll();
        \Yii::$app->authManager->loadItemsFromFile(\Yii::getAlias(\Yii::$app->authManager->itemFile), 'items');
        \Yii::$app->authManager->loadItemsFromFile(\Yii::getAlias(\Yii::$app->authManager->ruleFile), 'rules');
        \Yii::$app->authManager->loadItemsFromFile(
            \Yii::getAlias(\Yii::$app->authManager->assignmentFile), 'assignments'
        );
        return $this->redirect(['index']);
    }

    public function actionAllItems()
    {
        $nodes = $edges = [];
        $roles = \Yii::$app->authManager->getRoles();
        foreach ($roles as $rol) {
            $nodes[] = [
                'data' => [
                    'id' => $rol->name, 'type' => $rol->type, 'faveColor' => '#5F40B8', 'faveShape' => 'triangle',
                    'width' => mb_strlen($rol->name)
                ]
            ];
            $childs = \Yii::$app->authManager->getChildren($rol->name);
            if (!empty($childs)) {
                foreach ($childs as $childName => $child) {
                    $edges[] = [
                        'data' => [
                            'source' => $childName,
                            'target' => $rol->name,
                            'faveColor' => '#5F40B8',
                            'faveShape' => 'ellipse'
                        ]
                    ];
                }
            }

        }
        $permissions = \Yii::$app->authManager->getPermissions();
        foreach ($permissions as $perm) {
            $nodes[] = [
                'data' => [
                    'id' => $perm->name, 'type' => $perm->type, 'faveColor' => '#3AB5E1', 'faveShape' => 'pentagon',
                    'width' => mb_strlen($perm->name)
                ]
            ];
            $childs = \Yii::$app->authManager->getChildren($perm->name);
            if (!empty($childs)) {
                foreach ($childs as $childName => $child) {
                    $edges[] = [
                        'data' => [
                            'source' => $childName,
                            'target' => $perm->name,
                            'faveColor' => '#E13A69',
                            'faveShape' => 'ellipse'
                        ]
                    ];
                }
            }
        }
        return $this->render('graph', ['elems' => Json::encode(['nodes' => $nodes, 'edges' => $edges])]);
    }

    public function actionAllUsers()
    {
        $nodes = $edges = [];
        $assignments = RbacModel::getAllAssignments();
        $uids = array_keys($assignments);
        $uClass = $this->module->userClass;
        if(count($uids)>50){
            \Yii::$app->session->setFlash('error', RbacModule::t('simplerbac', 'Too many users for show'));
            return $this->redirect(['index']);
        }
        $users = $uClass::find()
            // ->select([$this->module->userPk, $this->module->usernameAttribute])  //in redis Ar not supported
            ->where([$this->module->userPk => $uids])
            ->indexBy($this->module->userPk)->asArray()->all();
        foreach ($assignments as $uid => $data) {
            $nodes[] = [
                'data' => [
                    'id' => "u$uid", 'username' => $users[$uid][$this->module->usernameAttribute],
                    'faveColor' => '#E13A69', 'faveShape' => 'ellipse',
                    'width' => mb_strlen($users[$uid][$this->module->usernameAttribute])
                ]
            ];
            $roles = \Yii::$app->authManager->getRolesByUser($uid);
            if (!empty($roles)) {
                foreach ($roles as $rol) {
                    $nodes[] = [
                        'data' => [
                            'id' => $rol->name, 'faveColor' => '#5F40B8', 'faveShape' => 'star',
                            'width' => mb_strlen($rol->name)
                        ]
                    ];
                    $edges[] = [
                        'data' => [
                            "id" => $rol->name . '_' . "$uid",
                            'source' => $rol->name,
                            'target' => "u$uid",
                            'faveColor' => '#5F40B8'
                        ]
                    ];
                    $perms = \Yii::$app->authManager->getPermissionsByRole($rol->name);
                    if (!empty($perms)) {
                        foreach ($perms as $p) {
                            $nodes[] = [
                                'data' => [
                                    'id' => $p->name, 'faveColor' => '#3AB5E1',
                                    'faveShape' => 'rectangle', 'width' => mb_strlen($p->name)
                                ]
                            ];
                            $edges[] = [
                                'data' => [
                                    "id" => $p->name . '_' . $rol->name,
                                    'source' => $p->name,
                                    'target' => $rol->name,
                                    'faveColor' => '#3AB5E1'
                                ]
                            ];
                        }
                    }

                }
            }

        }
        return $this->render('ugraph', ['elems' => Json::encode(['nodes' => $nodes, 'edges' => $edges])]);
    }

    /**
     * Finds the user model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUser($id)
    {
        $userclass = $this->module->userClass;
        if (($model = $userclass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}