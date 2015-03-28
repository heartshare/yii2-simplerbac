<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 27.03.15
 * Time: 16:05
 */

namespace insolita\simplerbac\models;


use insolita\simplerbac\RbacModule;
use yii\base\Event;
use yii\base\Model;
use Yii;
use yii\base\ModelEvent;
use yii\data\ActiveDataProvider;

class RbacModel extends Model{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    const EVENT_BEFORE_ASSIGN='event_before_assgin';
    const EVENT_AFTER_ASSIGN='event_after_assgin';

    /**
     * @var integer $type the type of the item. This should be either [[TYPE_ROLE]] or [[TYPE_PERMISSION]].
     */
    public $type;
    /**
     * @var string $name the name of the item. This must be globally unique.
     */
    public $name;
    /**
     * @var string $description the item description
     */
    public $description;
    /**
     * @var string $ruleName name of the rule associated with this item
     */
    public $ruleName;
    /**
     * @var mixed $data the additional data associated with this item
     */
    public $data;
    /**
     * @var integer $createdAt UNIX timestamp representing the item creation time
     */
    public $createdAt;
    /**
     * @var integer $updatedAt UNIX timestamp representing the item updating time
     */
    public $updatedAt;

    /**
     * @var string $new_child the name of the role which we want add as child 2 current
     */
    public $new_child;
    /**
     * @var string $forassign the name of the role which we want assign 2 user
     */
    public $forassign;

    /**
     * @var array $_childrens list of item childrens
     */
    private $_childrens;
    /**
     * @var array $_childrens_names list of  childrens names only
     */
    private $_childrens_names;
    /**
     * @var array $_parents list of item parents
     */
    private $_parents;
    /**
     * @var array $_parents_names list of  parents names only
     */
    private $_parents_names;
    /**
     * @var array $_roles list of roles
     */
    private $_roles;
    /**
     * @var array $_perms list of permissions
     */
    private $_perms;
    /**
     * @var array $can_assign2user - list of rbac items which we can assign to current user
     */
    private $can_assign2user;

    /**
     * @var array $userperms - list of rbac items which assigned to current user
     */
    public $userperms;


    /**
     * @var \yii\rbac\PhpManager $_authMan
     **/
    private $_authMan;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->_authMan = Yii::$app->authManager;
        $this->_authMan->init();

    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'description', 'type'], 'required', 'on' => ['additem', 'edititem']],
            ['type', 'in', 'range' => [1, 2]],
            ['description', 'string', 'min' => 3, 'max' => 200],
            ['name', 'string', 'min' => 3, 'max' => 30],
            [
                'name',
                'match',
                'pattern' => '/[^A-Za-z0-9\-_\*\/]/us',
                'not' => true,
                'message' => RbacModule::t('simplerbac',
                    'allowed pattern '
                ).'[A-Za-z0-9\-_\*\/]'
            ],
            [
                'name',
                'existValidate',
                'on' => ['edititem', 'delete', 'view'],
                'message' => RbacModule::t('simplerbac','Item not exists')
            ],
            [
                'name',
                'noexistValidate',
                'on' => ['additem'],
                'message' => RbacModule::t('simplerbac','Item also exists')
            ],
            [
                'description',
                'match',
                'pattern' => '/[^A-Za-zА-Яа-яЁё0-9\s\-]/us',
                'not' => true,
                'message' => RbacModule::t('simplerbac',
                    'allowed pattern '
                ).'[A-Za-zА-Яа-яЁё0-9- ]'
            ],
            [
                ['new_child'],
                'childValidate',
                'on' => ['addchild', 'unchild'],
                'message' => RbacModule::t('simplerbac','Item not exists')
            ],
            [
                ['forassign'],
                'existValidate',
                'on' => ['userassign'],
                'message' => RbacModule::t('simplerbac','Item not exists')
            ],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'additem' => ['type', 'name', 'description'],
            'edititem' => ['description', 'name', 'type'],
            'delete' => ['type', 'name'],
            'view' => ['type', 'name'],
            'addchild' => ['type', 'name', 'new_child'],
            'unchild' => ['type', 'name', 'new_child'],
            'userassign' => ['forassign']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => RbacModule::t('simplerbac','Item name'),
            'newname' => RbacModule::t('simplerbac','New name'),
            'description' => RbacModule::t('simplerbac','Description'),
            'ruleName' => RbacModule::t('simplerbac','rule'),
            'createdAt' => RbacModule::t('simplerbac','Created'),
            'updatedAt' => RbacModule::t('simplerbac','Updated'),
            'new_child' => RbacModule::t('simplerbac','Inherit from '),
            'userperms' => RbacModule::t('simplerbac','Operations'),
            'forassign' => RbacModule::t('simplerbac','Assign role'),
        ];
    }

    /**
     * @return array
     */
    public function getTypenames()
    {
        return [
            self::TYPE_ROLE => RbacModule::t('simplerbac','Role'),
            self::TYPE_PERMISSION => RbacModule::t('simplerbac','Operation')
        ];
    }

    /**
     * @var boolean $force - flag for force reload list
     * @return array of objects \yii\rbac\Role
     * */
    public function getRoles($force = false)
    {
        if (!$this->_roles or $force) {
            $this->_roles = $this->_authMan->getRoles();
        }
        return $this->_roles;
    }

    /**
     * @var boolean $force - flag for force reload list
     * * @return array of objects \yii\rbac\Permission
     * */
    public function getPerms($force = false)
    {
        if (!$this->_perms or $force) {
            $this->_perms = $this->_authMan->getPermissions();
        }
        return $this->_perms;
    }

    /**
     * @param $name
     * @param $type
     *
     * @return mixed
     */
    public function getItem($name, $type)
    {
        $item = ($type == self::TYPE_ROLE) ? $this->_authMan->getRole($name) : $this->_authMan->getPermission($name);
        return $item;
    }

    /**
     * @return array
     */
    public function getChildrens()
    {
        if (!$this->_childrens) {
            $this->_childrens = $this->_authMan->getChildren($this->name);
        }
        return $this->_childrens;
    }

    /**
     * @return array
     */
    public function getChildrensNames()
    {
        if (!$this->_childrens_names) {
            $this->_childrens_names = [];
            $ch = $this->getChildrens();
            if (!empty($ch)) {
                foreach ($ch as $item) {
                    $this->_childrens_names[] = $item->name;
                }
            }
        }
        return $this->_childrens_names;
    }

    /**
     * @return array
     */
    public function getParentNames()
    {
        return $this->_parents_names;
    }

    /**
     * @return array
     */
    public function getParents()
    {
        if (!$this->_parents) {
            $item = $this->getItem($this->name, $this->type);
            $roles = $this->getRoles();
            $perms = $this->getPerms();
            $childrens = $this->getChildrensNames();
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    if ($role->name != $this->name && !in_array($role->name, $childrens)) {
                        if ($this->_authMan->hasChild($role, $item)) {
                            $this->_parents[] = $role;
                            $this->_parents_names[] = $role->name;
                        }
                    }
                }
            }
            if (!empty($perms)) {
                foreach ($perms as $perm) {
                    if ($perm->name != $this->name && !in_array($perm->name, $childrens)) {
                        if ($this->_authMan->hasChild($perm, $item)) {
                            $this->_parents[] = $perm;
                            $this->_parents_names[] = $perm->name;
                        }
                    }
                }
            }

        }
        return $this->_parents;
    }

    /**
     * @return bool
     */
    public function existValidate()
    {
        if (!$this->getItem($this->name, self::TYPE_ROLE) or !$this->getItem($this->name, self::TYPE_PERMISSION)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function noexistValidate()
    {
        if (!$this->getItem($this->name, self::TYPE_ROLE) and !$this->getItem($this->name, self::TYPE_PERMISSION)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function childValidate()
    {
        if (!$this->new_child) {
            return false;
        }
        list($name, $type) = explode('_t', $this->new_child);
        if (!$this->getItem($name, $type)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     */
    public function saveItem()
    {
        $item = ($this->type == self::TYPE_ROLE)
            ? $this->_authMan->createRole($this->name)
            :
            $this->_authMan->createPermission($this->name);
        $item->name = $this->name;
        $item->type = $this->type;
        $item->description = $this->description;
        $item->createdAt = time();
        $item->updatedAt = time();
        if ($this->_authMan->add($item)) {
            //sleep(3);
        }  
    }

    /**
     *
     */
    public function updateItem()
    {
        $item = $this->getItem($this->name, $this->type);
        $item->description = $this->description;
        $item->updatedAt = time();
        $this->_authMan->update($item->name, $item);
       // sleep(3); //necessary sleep time  - wait while update auth file
    }

    /**
     *
     */
    public function deleteItem()
    {
        $item = $this->getItem($this->name, $this->type);
        if($item){
            $this->_authMan->remove($item);
        }
    }

    /**
     * @return bool|string
     */
    public function addChild()
    {
        $item = $this->getItem($this->name, $this->type);
        list($childname, $childtype) = explode('_t', $this->new_child);
        $child = $this->getItem($childname, $childtype);
        try {
            $this->_authMan->addChild($item, $child);
          //  sleep(3); //necessary sleep time  - wait while update auth file
            return true;
        } catch (\yii\base\InvalidCallException $e) {
            return RbacModule::t('simplerbac','Can`t inherit from this item');
        }
    }

    /**
     * @return bool
     */
    public function unChild()
    {
        $item = $this->getItem($this->name, $this->type);
        list($childname, $childtype) = explode('_t', $this->new_child);
        $child = $this->getItem($childname, $childtype);
        if ($child && $this->_authMan->removeChild($item, $child)) {
           // sleep(3); //necessary sleep time  - wait while update auth file
            return true;
        } else {
            return false;
        }
    }

    /**
     * Непосредственно привязка роли к юзеру
     *
     * @param $userid
     *
     * @return bool|string
     */
    public function userAssign($userid)
    {

        $role = $this->getItem($this->forassign, self::TYPE_ROLE);
        $oldroles = $this->_authMan->getRolesByUser($userid);
        $this->_authMan->revokeAll($userid);
        try {
               if($this->beforeAssign($userid, $role, $oldroles)){
                   $this->_authMan->assign($role, $userid);
                   $this->afterAssign($userid, $role, $oldroles);
                   return true;
               }else{
                   return false;
               }

        } catch (\yii\base\Exception $e) {
            foreach ($oldroles as $orole) {
                $this->_authMan->assign($orole, $userid);
            }
            return RbacModule::t('simplerbac','Can`t assign this item for this user');
        }
    }

    /**
     * @method array Itemmap() Генерирует массив объектов для дропдауна с учётом типа
     *
     * @return array
     * */
    public static function Itemmap($items)
    {
        $map = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $map[$item->name . '_t' . $item->type] = $item->name . ' [' . $item->description . ']';
            }
        }
        return $map;
    }

    /**
     * @method array getItemsForAssign() Генерирует массив ролей и прав для наследования
     * @return array
     * */
    public function getItemsForAssign()
    {
        $items = [];
        if ($this->type == self::TYPE_PERMISSION) {
            $items = self::Itemmap(
                $this->_authMan->getPermissions()
            ); //ИМХО Операции могут наследовать только операции но не роли
            unset($items[$this->name]);
        } else {
            $items = [
                'Roles' => self::Itemmap($this->_authMan->getRoles()),
                'Permissions' => self::Itemmap($this->_authMan->getPermissions())
            ];
            unset($items['Roles'][$this->name]);
        }

        return $items;
    }

    /**
     * @method array getItemsForAssignUser() Генерирует массив ролей и прав для выбора
     * @param  integer $userid
     *
     * @return array
     * */
    public function getItemsForAssignUser($userid)
    {
        $assig_items = [];
        $allroles = $this->getRoles();
        foreach ($allroles as $ap) {
            if (!$this->_authMan->getAssignment($ap->name, $userid)) {
                $assig_items[$ap->name] = $ap->description.' '.$ap->name;
            }

        }
        return $assig_items;
    }

    public static function getAllAssignments(){
        $man=Yii::$app->authManager;
        $reflector=new \ReflectionClass($man::className());
        $ass=$reflector->getProperty('assignments');
        $ass->setAccessible(true);
        $ass=$ass->getValue($man);
        return $ass;
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     **/
    public function getUsersDp($params)
    {
        $userClass = Yii::$app->getModule('simplerbac')->userClass;
        $pk = Yii::$app->getModule('simplerbac')->userPk;
        $username=Yii::$app->getModule('simplerbac')->usernameAttribute;
        $usermodel = new $userClass;
        $query = $userClass::find();

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

        if (!($usermodel->load($params) && $usermodel->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id' => $usermodel->{$pk},
                'username' => $usermodel->{$username}
            ]
        );


        return $dataProvider;
    }

    /**
     * @param integer $userid
     *
     * @return array
     */
    public static function getUserRoles($userid)
    {
        $up = Yii::$app->authManager->getRolesByUser($userid);
        $userperms = [];
        if ($up) {
            $userperms = self::Itemmap($up);
        }

        return $userperms;
    }
    /**
     * @param string $name
     *
     * @return array
     */
    public static function getRoleperms($name)
    {
        $up = Yii::$app->authManager->getPermissionsByRole($name);
        $perms = [];
        if ($up) {
            $perms = self::Itemmap($up);
        }

        return $perms;
    }

    /**
     * @param integer $userid
     *
     * @return array
     */
    public static function getUserperms($userid)
    {
        $up = Yii::$app->authManager->getPermissionsByUser($userid);
        $userperms = [];
        if ($up) {
            $userperms = self::Itemmap($up);
        }

        return $userperms;
    }

    protected function beforeAssign($userid, $role, $oldroles){
        $event=new ModelEvent();
        $event->data=['userid'=>$userid, 'role'=>$role, 'oldroles'=>$oldroles];
        $this->trigger(self::EVENT_BEFORE_ASSIGN,$event);
        return $event->isValid;
    }

    protected function afterAssign($userid, $role, $oldroles){
        $event=new Event();
        $event->data=['userid'=>$userid, 'role'=>$role, 'oldroles'=>$oldroles];
        $this->trigger(self::EVENT_BEFORE_ASSIGN,$event);
    }
}