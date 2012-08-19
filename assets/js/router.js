define([
    'backbone',
    'controllers/dashboard',
    'views/profile',
    'views/config'
], function (Backbone, DashboardController, Profile, Config) {

    var AppRouter = Backbone.Router.extend({
        routes: {
            // Define some URL routes
            '': 'showDashboard',
            'dashboard': 'showDashboard',
            'profile': 'showProfile',
            'config': 'showConfig',
            'config/*key': 'showConfig',

            'chart/*path': 'showChart',

            // Default
            '*actions': 'defaultAction'
        },

        start: function () {
            //console.log('router start');
            var _self = this;

            // Listen to the Dashboards chart-change event
            // and update the fragment
            DashboardController.view.on('change:chart', function (chartHash) {
                if (Backbone.history.fragment != chartHash)
                    _self.navigate('chart/' + chartHash);
            });

            Backbone.history.start();
        },

        showDashboard: function () {
            DashboardController.show('day');
        },
        showProfile: function () {
            Profile.render();
        },
        showConfig: function (key) {
            Config.show(key);
        },
        showChart: function (path) {
            DashboardController.show(path);
        },

        defaultAction: function (action) {
            console.log('No route:', action);
        }
    });

    return new AppRouter;

});