// Require.js allows configure shortcut alias
requirejs.config({
      
      deps: ['app'],
      
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
            'backbone.deep-model': 'libs/backbone-deep-model/deep-model',
            'backbone.forms': 'libs/backbone-forms/backbone-forms.amd.min',
            'backbone.forms.bootstrap': 'libs/backbone-forms/templates/bootstrap',
            'backbone.forms.default': 'libs/backbone-forms/templates/default',

            // external libs
            'bootstrap' : 'libs/bootstrap/bootstrap.min',
            'highcharts': 'libs/highcharts/highcharts',
            
            // other
            'prettify' : 'libs/prettify/prettify',

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
                  deps: ['backbone']
            },
            
            'backbone.forms.bootstrap': {
                  deps: ['backbone','backbone.forms']
            },
            
            'backbone.forms.default': {
                  deps: ['backbone','backbone.forms']
            },
            
            'backbone.deep-model': {
                  deps: ['backbone']
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
