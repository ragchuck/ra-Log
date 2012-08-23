;
(function ($, window) {
    var document = window.document;

    var methods = {

        'init' : function (options, callback) {

            // access via $.highcharts(options)
            if (typeof this === "function") {
                $.data(document, "highcharts", options);
                return this;
            }

            return this.each(function (index, element) {
                var $this = $(this),
                    _options = options || {},
                    _defaults = {
                        options : {
                            chart : {
                                renderTo : $this[0]
                            }
                        },
                        extract : null
                    };

                _options = $.extend(true,
                    _defaults,
                    $(document).data("highcharts"),
                    $this.data("highcharts"),
                    _options
                );

                if (_options.url && !_options._continue) {
                    $.ajax({
                        type : "GET",
                        url : _options.url,
                        context : $this,
                        success : function (response) {
                            var __options = {};

                            if (typeof _options.extract === "function") {
                                __options.options = _options.extract(response);
                            } else if (typeof _options.extract === "string") {
                                __options.options = response[_options.extract];
                            } else {
                                __options.options = response;
                            }

                            if (!__options.options)
                                $.error('Cannot extract data from the response. Extractor: ' + _options.extract);

                            __options = $.extend(true, __options, _options);
                            __options._continue = true;

                            // delete 'url' and 'extract' to prevent reaching here again
                            delete __options.url;
                            delete __options.extract;

                            this.highcharts(__options);
                        }
                    });
                    return $this;
                }

                var c_llb_ck = _options.callback || callback || undefined;
                this.chart = new Highcharts.Chart(_options.options, c_llb_ck);
            });

        }
    };

    /**
     * Plugin to build Highcharts in your page.
     *
     */
    $.highcharts = $.fn.highcharts = function (method) {

        var chart = (this[0]) ? this[0].chart : {};

        if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else if (chart && chart[method]) {
            return chart[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error('Method \'' + method + '\' does not exist on the Chart object.');
        }
    };

}(jQuery, window));
    