<?php

namespace infinitiweb\widgets\yii2\select2;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class InfinitiSelect2Asset extends AssetBundle
{
    public $sourcePath = '@vendor/infinitiweb/yii2-select2/src/assets';

    public $js = [
        'js/select2.js'
    ];
    public $css = [
        'css/select2.css'
    ];

    public $depends = [
        JqueryAsset::class,
        SlimScrollAsset::class
    ];
}
