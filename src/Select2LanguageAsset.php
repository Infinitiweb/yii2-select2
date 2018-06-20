<?php

namespace infinitiweb\widgets\yii2\select2;

use yii\web\AssetBundle;

/**
 * Class KakSelect2LanguageAsset
 * @package kak\widgets\select2
 */
class Select2LanguageAsset extends AssetBundle
{
    public $sourcePath = '@bower/select2/dist';

    public $depends = [
        Select2Asset::class
    ];

    /**
     * Add selected language
     *
     * @param string $lang
     * @return $this
     */
    public function addLanguage($lang)
    {
        $lang = !empty($lang) ? $lang : 'en';
        $this->js[] = 'js/i18n/' . $lang . '.js';

        return $this;
    }
}
