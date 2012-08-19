define(
    [
        'jquery',
        'underscore',
        'backbone',
        'router',
        'auth',
        'controllers/import',
        'controllers/dashboard',
        'i18n',

        'bootstrap',
        'prettify'
    ], function ($, _, Backbone, Router, Auth, ImportController, DashboardController, __) {


        var AppView = Backbone.View.extend({

            el: 'body',

            events: {
                "click .js-login": "login",
                "click .js-logout": "logout",
                "click .js-import-start": "startImport"
            },

            initialize: function () {
                _.bindAll(this);

                // register ajaxError handler
                this.$el.ajaxError(this.ajaxError);
            },


            login: function (event) {
                event.preventDefault();
                if (!Auth.user.logged_in())
                    Auth.loginView.render();
            },

            logout: function (event) {
                event.preventDefault();
                Auth.logout();
            },

            startImport: function (event) {
                event.preventDefault();
                ImportController.start();
            },

            ajaxError: function (e, jqxhr) {
                var error, cl, phpError;

                try {
                    phpError = $.parseJSON(jqxhr.responseText).error;
                    console.log(phpError);
                    switch (phpError.type) {
                        case 'error':
                            cl = 'label-important';
                            break;
                        case 'warning':
                            cl = 'label-warning';
                            break;
                        case 'notice':
                            cl = 'label-info';
                            break;
                        default:
                            cl = '';
                    }

                    error = $('<p>')
                        .append($('<span>')
                        .addClass("label " + cl)
                        .text(phpError.type + "[" + phpError.code + "]")
                        .attr('title', phpError.file + ':' + phpError.line))
                        .append(' ' + phpError.message)
                        .append(phpError.source);
                } catch (ex) {
                    error = $('<pre>').html(jqxhr.responseText);
                }

                var opts = {
                    title: jqxhr.statusText,
                    message: error.html(),
                    lbl: {
                        close: __("Close")
                    }
                };

                var tmpl = _.template($('#tmpl-ajax-error').html(), opts);
                this.$el.append($(tmpl).modal());
                prettyPrint();
            }
        });


        var App = function () {
            var App = this;

            // initialize the router
            Router.start();
            //ImportController.start();
            prettyPrint();

            Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });

            App.view = new AppView;

            Auth.user.on('login:success', function () {
                App.view.$el.addClass('logged-in')
            });
            Auth.user.on('logout:success', function () {
                App.view.$el.removeClass('logged-in')
            });

            // Wake up user
            Auth.user.live();

            ImportController.state.on('update_progress', DashboardController.update);
        };

        return new App;
    });