define([
      'jquery',
      'underscore',
      'backbone',
      'router',
      'auth',
      'views/import',
      'i18n',

      'bootstrap',
      'prettify'

      ], function($, _, Backbone, Router, Auth, Import, __){
                        
                        
            var AppView = Backbone.View.extend({
                  
                  el: 'body',
                  
                  events : {
                        "click .js-login": "login",
                        "click .js-logout": "logout",
                        "click .js-import-start": "startImport"
                  },
                  
                  initialize: function() {
                        _.bindAll(this);
                        var _self = this;
                        Auth.user.on('login:success', function(){
                              _self.$el.addClass('logged-in')
                        });
                        Auth.user.on('logout:success', function(){
                              _self.$el.removeClass('logged-in')
                        });
                        this.$el.ajaxError(this.ajaxError);
                  },
                  
                  // Starts the app, called in main.js
                  start: function() {                        
                        // initialize the router
                        Router.start();
                        //Import.start();
                        prettyPrint();
                  },
                  
                  login: function(event) {
                        event.preventDefault();
                        if(!Auth.user.logged_in())
                              Auth.loginView.render();
                  },
                  
                  logout: function(event) {
                        event.preventDefault();
                        Auth.logout();
                  },
                  
                  startImport: function(event) {
                        event.preventDefault();
                        Import.start();
                  },
                  
                  ajaxError: function(e, jqxhr, options) {
                        var error, cl, phpError
                        
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
                        
                        var tmpl = _.template($('#tmpl-ajax-error').html(), opts)
                        this.$el.append($(tmpl).modal());
                        prettyPrint();
                  }
            });

            return new AppView;
      });