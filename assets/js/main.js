// Require.js allows configure shortcut alias
requirejs.config({

    deps: ['app'],

    config: {
        baseUrl: '/ralog'
    },

    paths: {
        // system libs
        'jquery': 'libs/jquery/jquery-min',
        'underscore': 'libs/underscore/underscore-min',
        'backbone': 'libs/backbone/backbone-min',

        // jQuery plugins
        'jquery.dateFormat': 'libs/jquery/jquery.dateFormat',

        // Backbone plugins
        'backbone.deep-model': 'libs/backbone-deep-model/deep-model',
        'backbone.forms': 'libs/backbone-forms/backbone-forms.amd.min',
        'backbone.forms.bootstrap': 'libs/backbone-forms/templates/bootstrap',
        'backbone.forms.default': 'libs/backbone-forms/templates/default',
        'backbone.workflow': 'libs/backbone-workflow/workflow',

        // external libs
        'bootstrap': 'libs/bootstrap/bootstrap.min',
        'highcharts': 'libs/highcharts/highcharts',
        'prettify': 'libs/prettify/prettify',

        // plugins
        'text': 'libs/require/text',
        'json': 'libs/require/json',

        // templates path
        'templates': '../../assets/templates',

        // Server
        'api': '/ralog'
    },


    shim: {

        'underscore': {
            exports: '_'
        },

        'jquery': {
            exports: function () {
                return $.noConflict();
            }
        },

        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },

        'backbone.forms': ['backbone'],
        'backbone.forms.bootstrap': ['backbone', 'backbone.forms'],
        'backbone.forms.default': ['backbone', 'backbone.forms'],
        'backbone.deep-model': ['backbone'],
        'backbone.workflow': ['backbone'],

        'bootstrap': ['jquery'],

        'highcharts': {
            deps: ['jquery'],
            exports: 'Highcharts'
        }
    }

});