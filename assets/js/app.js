define([
      'jquery',
      'underscore',
      'backbone',
      'router',
      'views/import',

      //'backbone.forms',
      'bootstrap'

      ], function($, _, Backbone, Router, Import){

            var AppView = Backbone.View.extend({
                  
                  el: 'body',
                  
                  events : {
                        "click .js-login" : "login",
                        "click .js-import-start" : "startImport"
                  },
                  
                  // Starts the app, called in main.js
                  start : function() {                        
                        // initialize the router
                        Router.start();
                        Import.start();
                  },
                  
                  login : function(event) {
                        event.preventDefault();
                        alert('to be implemented :]');
                  },
                  
                  startImport : function(event) {
                        event.preventDefault();
                        Import.start();
                  }
            });

            return new AppView;
      });