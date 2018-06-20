;

(function (root, factory) {
    // CommonJS support
    if (typeof exports === 'object') {
        module.exports = factory();
    }
    // AMD
    else if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    // Browser globals
    else {
        factory(root.jQuery);
    }
}(this, function ($) {
    'use strict';

    let selector = {
        base: '.infinitiweb-select2',
        items_selected_multiple: '.select2-selection--multiple .select2-selection__rendered'
    };

    let infinitiwebSelect2 = function (element, options) {
        this.element = element;
        this.options = options;
        this.init();
    };

    infinitiwebSelect2.prototype = {
        constructor: infinitiwebSelect2,

        init: function () {
            this.destroy();
            this.create();
        },

        create: function () {
            this.initS2();
        },

        destroy: function () {

        },

        afterLoadData: function () {
            let $el = $(this.element),
                deferred = new $.Deferred();

            if (!$el.data('loadItemsUrl')) {
                deferred.resolve('afterLoadData not load');
                return deferred;
            }
            let url = $el.data('loadItemsUrl');
            $.ajax({
                url: url,
                dataType: 'json',
                success: $.proxy(function (data) {
                    this.options['data'] = data.results;
                    deferred.resolve('afterLoadData success');
                }, this)
            });
            return deferred;
        },

        initS2: function () {
            $.when(
                this.afterLoadData()
            ).done($.proxy(function () {
                this.initWidget();
                this.initS2ToggleAll();
            }, this));
        },

        initWidget: function () {
            $(this.element).select2(this.options);
            this.initScroll();
        },

        initScroll: function (e) {
            let self = this;
            let scroll = $(this.element).data('scrollHeight');
            if (!scroll) {
                return;
            }
            $(self.element)
                .closest(selector.base)
                .find(selector.items_selected_multiple)
                .slimScroll({height: ''})
                .css('max-height', scroll + 'px');
        },

        initS2ToggleAll: function () {
            let $el = $(this.element);
            let id = $el.attr('id'), togId = '#' + 's2-togall-' + id, $tog = $(togId);

            if (!$el.attr('multiple') || !$el.data('toggleEnable')) {
                return;
            }

            $el.on('select2:open.krajees2', function () {
                if ($tog.parent().attr('id') === 'parent-' + togId || !$el.attr('multiple')) {
                    return;
                }
                $('#select2-' + id + '-results')
                    .closest('.select2-dropdown')
                    .prepend($tog);
                $('#parent-' + togId).remove();
            }).on('change.krajeeselect2', function () {
                if (!$el.attr('multiple')) {
                    return;
                }
                let tot = 0, sel = $el.val() ? $el.val().length : 0;
                $tog.removeClass('s2-togall-select s2-togall-unselect');

                $el.find('option:enabled')
                    .each(function () {
                        if ($(this).val().length) {
                            tot++;
                        }
                    });
                if (tot === 0 || sel !== tot) {
                    $tog.addClass('s2-togall-select');
                } else {
                    $tog.addClass('s2-togall-unselect');
                }
            });

            $tog.off('.krajees2').on('click.krajees2', function () {
                let isSelect = $tog.hasClass('s2-togall-select'), flag = true, ev = 'selectall';
                if (!isSelect) {
                    flag = false;
                    ev = 'unselectall';
                }
                $el.find('option')
                    .each(function () {
                        let $opt = $(this);
                        if (!$opt.attr('disabled') && $opt.val().length) {
                            $opt.prop('selected', flag);
                        }
                    });
                $el.select2('close')
                    .trigger('krajeeselect2:' + ev)
                    .trigger('change');
            });
        }
    };

    $.fn.infinitiwebSelect2 = function (option) {
        let options = typeof option == 'object' && option;
        new infinitiwebSelect2(this, options);
        return this;
    };
    $.fn.infinitiwebSelect2.Constructor = infinitiwebSelect2;
}));