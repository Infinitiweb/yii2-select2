<?php

namespace infinitiweb\widgets\yii2\select2;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class Select2 extends \yii\widgets\InputWidget
{
    const JS_KEY = 'infinitiweb/widgets/yii2//select2/';

    const THEME_DEFAULT = 'classic';
    const THEME_BOOTSTRAP = 'bootstrap';

    //  Triggered whenever an option is selected or removed.
    const EVENT_CHANGE = 'change';
    //  Triggered whenever the dropdown is closed.
    const EVENT_CLOSE = 'select2:close';
    // Triggered before the dropdown is closed. This event can be prevented.
    const EVENT_CLOSING = 'select2:closing';
    //  Triggered whenever the dropdown is opened.
    const EVENT_OPEN = 'select2:open';
    //  Triggered before the dropdown is opened. This event can be prevented.
    const EVENT_OPENING = 'select2:opening';
    //  Triggered before a result is selected. This event can be prevented.
    const EVENT_SELECT = 'select2:select';
    //  Triggered whenever a result is selected.
    const EVENT_SELECTING = 'select2:selecting';
    //  Triggered whenever a selection is removed.
    const EVENT_UNSELECT = 'select2:unselect';
    //  Triggered before a selection is removed. This event can be prevented.
    const EVENT_UNSELECTING = 'select2:unselecting';

    /** @var string */
    public $language = 'ru';
    /** @var array */
    public $options = [];

    public $loadItemsUrl;
    public $ajax;
    /** @var bool */
    public $ajaxCache = true;
    /** @var int */
    public $minimumInputLength = 0;

    public $tags;
    /** @var bool */
    public $multiple = false;
    /** @var string */
    public $theme = self::THEME_BOOTSTRAP;
    /** @var string */
    public $placeholder = '';
    /** @var array */
    public $events = [];
    /** @var array */
    public $clientOptions = [];
    /** @var array */
    public $items = [];
    /** @var bool */
    public $firstItemEmpty = false;
    /** @var string */
    public $selectLabel = 'Выбрать все';
    /** @var string */
    public $unselectLabel = 'Отменить выбор всего';
    /** @var bool */
    public $toggleEnable = true;
    /** @var array */
    public $toggleOptions = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->fillOption();
    }

    private function fillOption()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (isset($this->tags)) {
            $this->options['data-tags'] = $this->tags;
            $this->options['multiple'] = true;
        }

        if ($this->loadItemsUrl !== null) {
            $this->options['data-load-items-url'] = Url::to($this->loadItemsUrl);
        }

        if (isset($this->ajax)) {
            $this->options['data-ajax--url'] = Url::to($this->ajax);
            $this->options['data-ajax--cache'] = $this->boolToStr($this->ajax);
            $this->options['data-minimum-input-length'] = $this->minimumInputLength;
        }

        $this->options['data-toggle-enable'] = $this->boolToStr($this->toggleEnable);
        $this->options['data-multiple'] = $this->boolToStr($this->multiple);
        $this->options['multiple'] = $this->multiple;
        $this->options['data-language'] = $this->language;
        $this->options['data-placeholder'] = $this->placeholder;

        $this->clientOptions['theme'] = $this->theme;

        Html::addCssStyle($this->options, ['width' => '100%'], false);
        Html::addCssClass($this->options, 'select2 form-control');
    }

    public function run()
    {
        parent::run();

        $this->renderInput();
        $this->renderToggleAll();
        $this->registerAssets();
    }

    protected function renderInput()
    {
        if ($this->firstItemEmpty && !$this->multiple) {
            $this->items = array_merge(['' => $this->placeholder], $this->items);
        }

        if (array_key_exists('itemWidthAuto', $this->options) && !empty($this->options['itemWidthAuto'])) {
            Html::addCssClass($this->options, 'select2-auto');
        } else {
            Html::addCssClass($this->options, 'select2-width-100');
        }

        $isModel = $this->hasModel();
        if (!$isModel && $this->value === null) {
            $this->value = \Yii::$app->request->get($this->name);
        }

        $input = Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        if ($isModel) {
            $input = Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        }

        echo Html::tag('div', $input, ['class' => 'infinitiweb-select2']);
    }

    protected function renderToggleAll()
    {
        if (!$this->multiple || !$this->toggleEnable) {
            return;
        }

        $settings = array_merge_recursive([
            'selectLabel' => '<i class="glyphicon glyphicon-unchecked"></i>' . $this->selectLabel,
            'unselectLabel' => '<i class="glyphicon glyphicon-check"></i>' . $this->unselectLabel,
            'selectOptions' => [],
            'unselectOptions' => [],
            'options' => ['class' => 's2-toggle-button']
        ], $this->toggleOptions);

        $selectOptions = $settings['selectOptions'];
        $unselectOptions = $settings['unselectOptions'];
        $prefix = 's2-toggle-';

        Html::addCssClass($settings['options'], "{$prefix}select");
        Html::addCssClass($selectOptions, "s2-select-label");
        Html::addCssClass($unselectOptions, "s2-unselect-label");

        $settings['options']['id'] = $prefix . $this->options['id'];

        $label = Html::tag('span', $settings['selectLabel'], $selectOptions) . Html::tag('span', $settings['unselectLabel'], $unselectOptions);
        $out = Html::tag('span', $label, $settings['options']);

        echo Html::tag('span', $out, ['id' => 'parent-' . $settings['options']['id'], 'style' => 'display:none']);
    }

    /**
     * Registers Assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        Select2Asset::register($view);
        InfinitiSelect2Asset::register($view);
        Select2LanguageAsset::register($view)->addLanguage($this->language);

        if ($this->theme == self::THEME_BOOTSTRAP) {
            ThemeBootstrapAsset::register($view);
        }

        $id = $this->options['id'];
        $clientOptions = Json::htmlEncode($this->clientOptions);

        $view->registerJs("jQuery('#{$id}').infinitiwebSelect2({$clientOptions});", $view::POS_READY, self::JS_KEY . $id);
        $this->registerEvents();
    }

    /**
     * Register plugin' events.
     */
    protected function registerEvents()
    {
        $view = $this->getView();
        $selector = '#' . $this->options['id'];
        if (!empty($this->events)) {
            $js = [];
            foreach ($this->events as $event => $callback) {
                if (is_array($callback)) {
                    foreach ($callback as $function) {
                        if (!$function instanceof JsExpression) {
                            $function = new JsExpression($function);
                        }
                        $js[] = "jQuery('$selector').on('$event', $function);";
                    }
                } else {
                    if (!$callback instanceof JsExpression) {
                        $callback = new JsExpression($callback);
                    }
                    $js[] = "jQuery('$selector').on('$event', $callback);";
                }
            }
            if (!empty($js)) {
                $js = implode("\n", $js);
                $view->registerJs($js, $view::POS_READY, self::JS_KEY . 'events/' . $this->options['id']);
            }
        }
    }

    /**
     * @param boolean $var
     * @return string
     */
    private function boolToStr($var)
    {
        return (bool)$var ? 'true' : 'false';
    }

}
