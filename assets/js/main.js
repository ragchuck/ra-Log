// Require.js allows configure shortcut alias
requirejs.config({

      config: {
            baseUrl: '/ra_log'
      },

      paths: {
            // system libs
            'jquery': 'libs/jquery/jquery-min',
            'underscore': 'libs/underscore/underscore-min',
            'backbone' : 'libs/backbone/backbone-min',
            
            // jQuery plugins
            'jquery.dateFormat': 'libs/jquery/jquery.dateFormat',
            
            // Backbone plugins
            'backbone.forms': 'libs/backbone-forms/backbone-forms.min',

            // external libs
            'bootstrap' : 'libs/bootstrap/bootstrap.min',
            'highcharts': 'libs/highcharts/highcharts',

            // plugins
            'text': 'libs/require/text',
            'json': 'libs/require/json',

            // templates path
            'templates': '../../assets/templates'
      },


      shim: {

            'underscore': {
                  exports: function() {
                        return _.noConflict();
                  }
            },
            
            'jquery': {
                  exports: function() {
                        return $.noConflict();
                  }
            },

            'backbone': {
                  deps: ['underscore', 'jquery'],
                  exports: function() {
                        return Backbone.noConflict();
                  }
            },

            'backbone.forms': {
                  deps: ['underscore', 'jquery', 'backbone']
            },

            'bootstrap' : {
                  deps: ['jquery']
            },

            'highcharts': {
                  deps: ['jquery'],
                  exports: function() {
                        Highcharts.setOptions({
                              global: {
                                    useUTC : false
                              }
                        });
                        return Highcharts;
                  }
            }
      }

});

// initialize the App
requirejs(['app'], function(App) {
      App.start();
});