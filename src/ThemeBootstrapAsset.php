<?php

namespace infinitiweb\widgets\yii2\select2;

use yii\web\AssetBundle;

class ThemeBootstrapAsset extends AssetBundle
{
    public $sourcePath = '@vendor/infinitiweb/yii2-select2/src/assets';

    public $css = [
        'css/select2-bootstrap-theme' . (!YII_DEBUG ? '.min' : '') . '.css',
    ];
}
