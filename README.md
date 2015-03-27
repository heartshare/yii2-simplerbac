Simple rbac manager
===================
for small data. Allow manage roles and operations, and assign to user

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

Once the extension is installed, simply use it in your code by  :
in module section

```php
'simplerbac'=>[
              'class'=>'\insolita\simplerbac\RbacModule',
              'userClass '=>'app\models\User',
              'userPk=>'id',
              'usernameAttribute'=>'username'
          ],
          ```