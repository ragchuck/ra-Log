define([
      'backbone',
      'views/dashboard',
      'views/profile'
      ], function(Backbone, dashboardView, profileView){

            var AppRouter = Backbone.Router.extend({
                  routes: {
                        // Define some URL routes
                        'dashboard': 'showDashboard',
                        'profile': 'showProfile',

                        'chart/*path': 'showChart',

                        // Default
                        '*actions': 'defaultAction'
                  },


                  showDashboard: function(){
                        dashboardView.render().showChart('day');
                  },
                  showProfile: function(){
                        profileView.render();
                  },
                  showChart: function(path){
                        dashboardView.showChart(path);
                  },
                  defaultAction: function(actions){
                        console.log('No route:', actions);
                  }
            });

            var initialize = function(){
                  var router = new AppRouter;
                  Backbone.history.start();
                  if (Backbone.history.fragment === '')
                        router.navigate('dashboard', {trigger: true});
            };
            return {
                  initialize: initialize
            };

      });