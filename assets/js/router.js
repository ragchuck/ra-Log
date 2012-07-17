define([
      'backbone',
      'views/dashboard',
      'views/profile'
      ], function(Backbone, Dashboard, Profile){

            var AppRouter = Backbone.Router.extend({
                  routes: {
                        // Define some URL routes
                        'dashboard': 'showDashboard',
                        'profile': 'showProfile',

                        'chart/*path': 'showChart',

                        // Default
                        '*actions': 'defaultAction'
                  },

                  start: function(){
                        console.log('router start');
                        Backbone.history.start();
                        if (Backbone.history.fragment === '')
                              this.navigate('dashboard', {
                                    trigger: true
                              });
                  },

                  showDashboard: function(){
                        Dashboard.render().showChart('day');
                  },
                  showProfile: function(){
                        Profile.render();
                  },
                  showChart: function(path){
                        Dashboard.showChart(path);
                  },
                  defaultAction: function(actions){
                        console.log('No route:', actions);
                  }
            });
            
            return new AppRouter;

      });