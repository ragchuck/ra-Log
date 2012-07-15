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
                  exports: '_'
            },

            'backbone': {
                  deps: ['underscore', 'jquery'],
                  exports: 'Backbone'
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
      App.initialize();
});