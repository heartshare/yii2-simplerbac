Simple rbac manager
===================
Intuitive, cute, ajax, for not large volumes of data and end-user oriented.
Allow manage roles and operations, and assign roles to user
Can show data as graph.
Compatible with yii\rbac\phpManager yii\rbac\DbManager insolita\redisedrbac\components\RedisedPhpManager

[See Demo](http://yii2redis-insolita1.c9.io/ru/simplerbac/default/index.html)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist insolita/yii2-simplerbac "*"
```

or add

```
"insolita/yii2-simplerbac": "*"
```

to the require section of your `composer.json` file.


Usage
-----
Require any FontAwesomeAsset

Once the extension is installed, simply use it in your code by  :
in module section

```php
'simplerbac'=>[
              'class'=>'\insolita\simplerbac\RbacModule',
              'userClass '=>'app\models\User',
              'userPk'=>'id',
              'usernameAttribute'=>'username'
          ],
          ```
