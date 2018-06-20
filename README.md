Select2 widgets
================
Select2 widget for Yii2

Preview
------------
<img src="https://lh3.googleusercontent.com/-SYtyKxfvZz4/VbCwEPzvxEI/AAAAAAAAAC4/Or5c1ObK7EM/s339-Ic42/select2Preview.png">

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist infinitiweb/yii2-select2 "*"
```

or add

```
"infinitiweb/yii2-select2": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php

<?php

use \infinitiweb\widgets\yii2\select2\Select2;

echo Select2::widget([
    'toggleEnable' => false, // visible select all/unselect all
    'selectLabel' => 'select all',
    'unselectLabel' => 'unselect all',
    'options' => [
        'data-scroll-height' => 150, // auto scroll
        'data-item-width'    => 100, // 100|auto
    ],
    'multiple' => true,
    'value' => [
       'val1',
       'val2'
    ],
    'name' => 'inputName',
    'items' => [
        'val1' => 'options1',
        'val2' => 'options2',
        'val3' => 'options3',
        'val4' => 'options4',
    ],
]); ?>
```

```php
<?php

use \infinitiweb\widgets\yii2\select2\Select2;
 
echo $form->field($model, 'list')->widget(Select2::class, [
    'items' => [
        'val1' => 'options1',
        'val2' => 'options2',
        'val3' => 'options3',
        'val4' => 'options4',
    ],
    'options' => [
        'class' => 'myCssClass'
    ],
    'clientOptions' => [],   // js options select2
]);
?>

```

