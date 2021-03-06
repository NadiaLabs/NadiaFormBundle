(function ($, global, undefined) {
    'use strict';

    global['__default__DynamicChoiceBuildAjaxUriCallback'] = function ($, ajaxUri) {
        return ajaxUri;
    };

    global['__default__DynamicChoiceBuildAjaxDataCallback'] = function ($) {
        let $node = $(this);
        let key = $node.data('default-ajax-data-key');
        let data = {};

        data[key] = $node.find('input[type="hidden"]').val();

        return data;
    };

    global['__default__DynamicChoiceRenderHtmlCallback'] = function ($target, ajaxResponse, $) {
        let $targetList = $target.find('select:first');
        let $html = $('<div>'+ajaxResponse+'</div>');
        let $newList = $html.find('select:first');
        let $options;

        if ($newList.length) {
            $options = $newList.find('option');
        } else {
            $options = $html.find('option');
        }

        $targetList.empty();

        $options.each(function (i, option) {
            $targetList.append(option);
        });

        $targetList.trigger('initialize-selected-value');
    };

    $('div[data-form-type="dynamic-choice"]').each(function () {
        let that = this;
        let $node = $(that);
        let $list = $node.find('select:first');
        let $value = $node.find('input[type="hidden"]');
        let ajaxUri = $node.data('ajax-uri');
        let $target = $($node.data('target'));
        let buildAjaxUriCallbackName = $node.data('build-ajax-uri-callback-name');
        let buildAjaxDataCallbackName = $node.data('build-ajax-data-callback-name');
        let renderHtmlCallbackName = $node.data('render-html-callback-name');

        if ($target.length) {
            if (undefined === global[buildAjaxUriCallbackName]) {
                throw new Error('Cannot get AJAX parameters! Callback "' + buildAjaxUriCallbackName + '" is not exists!');
            }
            if (undefined === global[renderHtmlCallbackName]) {
                throw new Error('Cannot update select html! Callback "' + renderHtmlCallbackName + '" is not exists!');
            }
        }

        function ajax()
        {
            if ($target.length) {
                $.ajax($.extend(
                    $node.data('ajax-extra-settings'),
                    {
                        url: global[buildAjaxUriCallbackName].call(that, $, ajaxUri),
                        method: $node.data('ajax-method'),
                        data: global[buildAjaxDataCallbackName].call(that, $),
                        success: function (response) {
                            global[renderHtmlCallbackName].call(that, $target, response, $);
                        }
                    }
                ));
            }
        }

        $list.on('change', function () {
            $value.val($list.val());

            ajax();
        });

        $list.on('initialize-selected-value', function () {
            let hasSelectedValue = false;
            let value = $value.val();

            $list.find('option').each(function (i, option) {
                if ($(option).val() === value) {
                    hasSelectedValue = true;
                }
            });

            if (hasSelectedValue) {
                $list.val(value);
            } else {
                $list.find('option:first').prop('selected', true);
            }

            $list.trigger('change');
        });

        if ($node.data('auto-call-ajax-onload')) {
            if ($target.length) {
                let $targetValue = $target.find('input[type="hidden"]');

                if (($targetValue.length && '' !== $targetValue.val()) || '' !== $value.val()) {
                    ajax();
                }
            }
        }
    });
})(jQuery, window);
