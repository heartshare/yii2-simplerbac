<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 27.03.15
 * Time: 15:55
 */
namespace insolita\simplerbac;

/**
 * Class RbacModule
 *
 * @package insolita\simplerbac
 */
class RbacModule extends \yii\base\Module {

    /**
     * @var string
     */
    public $controllerNamespace = 'insolita\simplerbac\controllers';
    /**
     * @var string
     */
    public $userClass = 'app\models\User';
    /**
     * @var string
     */
    public $userPk='id';

    /**
     * @var string
     */
    public $usernameAttribute='username';
    /**
     * @var bool
     */
    public $allowAssignPermitions=false;
    /**
     * @var array the the internalization configuration for this module
     */
    public $i18n = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@redisrbac', __DIR__);
        $this->registerTranslations();

    }

    /**
     *@inheritdoc
     */
    public function registerTranslations()
    {
        \Yii::setAlias('@simplerbac_messages', __DIR__ . '/messages/');
        \Yii::$app->i18n->translations['insolita/simplerbac/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => \Yii::getAlias('@simplerbac_messages'),
            'sourceLanguage' => 'en',
            'fileMap' => [
                'insolita/simplerbac/simplerbac' => 'simplerbac.php'
            ],
        ];
    }

    /**
     * @param       $message
     * @param array $params
     * @param null  $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('insolita/simplerbac/' . $category, $message, $params, $language);
    }


}