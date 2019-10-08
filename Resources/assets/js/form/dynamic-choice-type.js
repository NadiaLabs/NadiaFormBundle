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

    global['__default__DynamicChoiceRenderHtmlCallback'] = function ($, ajaxResponse) {
        let $node = $(this);
        let $target = $($node.data('target'));
        let $html = $('<div>'+ajaxResponse+'</div>');
        let $select = $html.find('select:first');
        let $options;

        if ($select.length) {
            $options = $select.find('option');
        } else {
            $options = $html.find('option');
        }

        $target.empty();

        $options.each(function (i, option) {
            $target.append(option);
        });

        $target.find('option:first').prop('selected', true);
    };

    $('div[data-form-type="dynamic-choice"]').each(function () {
        let that = this;
        let $node = $(that);
        let $list = $node.find('select');
        let $value = $node.find('input[type="hidden"]');
        let ajaxUri = $node.data('ajax-uri');
        let $target = $($node.data('target'));
        let buildAjaxUriCallbackName = $node.data('build-ajax-uri-callback-name');
        let buildAjaxDataCallbackName = $node.data('build-ajax-data-callback-name');
        let renderHtmlCallbackName = $node.data('render-html-callback-name');

        $list.on('change', function () {
            $value.val($list.val());

            if ($target.length) {
                if (undefined === global[buildAjaxUriCallbackName]) {
                    throw new Error('Cannot get AJAX parameters! Callback "'+buildAjaxUriCallbackName+'" is not exists!');
                }
                if (undefined === global[renderHtmlCallbackName]) {
                    throw new Error('Cannot update select html! Callback "'+renderHtmlCallbackName+'" is not exists!');
                }

                let uri = global[buildAjaxUriCallbackName].call(that, $, ajaxUri);
                let data = global[buildAjaxDataCallbackName].call(that, $);
                let method = $node.data('ajax-method');
                let extraSettings = $node.data('ajax-extra-settings');
                let settings = $.extend(extraSettings, {
                    url: uri,
                    method: method,
                    data: data,
                    success: function (response) {
                        global[renderHtmlCallbackName].call(that, $, response);
                    }
                });

                $.ajax(settings);
            }
        });
    });
})(jQuery, window);
