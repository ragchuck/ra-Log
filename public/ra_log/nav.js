steal('jquery/controller/route',
      function ($) {

            "use strict";

            $.Controller('Navigation', {

                  init : function () {
                        steal.dev.log("Controller Navigation.init()");
                        $.route("/:action");
                        this.$content = $('#content');
                  },

                  "/:action route" : function (a) {
                        steal.dev.log("Controller Navigation: '/:action route' function()", arguments);
                        this.$content.load(a.action);
                  }

            });
      });