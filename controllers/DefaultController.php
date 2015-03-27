<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 27.03.15
 * Time: 16:04
 */

namespace insolita\simplerbac\controllers;

use insolita\simplerbac\models\RbacModel;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
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
                'except' => ['index'],
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
                    'delete' => ['post']
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
        $model->load(\Yii::$app->request->post());
        if ($model->validate()) {
            $model->saveItem();
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
        $model->load(\Yii::$app->request->post());
        if ($model->validate()) {
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
            $result = ['state' => 'error',  'error' => $error];
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
                    'error' => \insolita\simplerbac\Module::t('Нельзя унаследовать от этого элемента')
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
        return ['state' => 'success','result'=>$this->renderAjax('_itemlist', ['model' => $model])];
    }

    public function actionLoadperms()
    {
        $model = new RbacModel();
        $model->type = RbacModel::TYPE_PERMISSION;
        return ['state' => 'success','result'=>$this->renderAjax('_itemlist', ['model' => $model])];
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
            return ['state' => 'success','result'=>$this->renderAjax('_view', ['model' => $model])];
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
            $result = ['state' => 'error',  'error' => $error];
            return $result;
        }
    }

    public function actionUpditem(){
        $model = new RbacModel();
        $model->scenario = 'edititem';
        if ($model->load(\Yii::$app->request->post(), '')) {
            $result = [
                'state' => 'success',
                'result' => $this->renderAjax('_itemform', ['model' => $model])
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
            $result = ['state' => 'error', 'error' => $error];
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
                    $result = ['state' => 'novalid', 'error' => $assig];
                    \Yii::$app->response->format = 'json';
                    return $result;
                } else {
                    $result = ['state' => 'success', 'error' => ''];
                    \Yii::$app->response->format = 'json';
                    return $result;
                }
            } else {
                $result = ['state' => 'novalid', 'error' => Html::errorSummary($model)];
                \Yii::$app->response->format = 'json';
                return $result;
            }
        }
        return $this->renderAjax('_assign', ['model' => $model, 'user' => $user]);
    }

    public function actionConvert(){
        $items='@app/rbac/rbac.php';
        $rules='@app/rbac/rules.php';
        $assignments='@app/rbac/assignments.php';
        \Yii::$app->authManager->loadItemsFromFile(\Yii::getAlias($items),'items');
        \Yii::$app->authManager->loadItemsFromFile(\Yii::getAlias($rules),'rules');
        \Yii::$app->authManager->loadItemsFromFile(\Yii::getAlias($assignments),'assignments');
        return $this->redirect(['index']);
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