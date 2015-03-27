Redised PhpManager and simple rbac manager
===================
Store PhpManager data in redis,
full-feature RedisManager in the development

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist insolita/yii2-redisedrbac "*"
```

or add

```
"insolita/yii2-redisedrbac": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \insolita\redisedrbac\AutoloadExample::widget(); ?>```