define([
      'backbone',
      'views/dashboard',
      'views/profile'
      ], function(Backbone, Dashboard, Profile){

            var AppRouter = Backbone.Router.extend({
                  routes: {
                        // Define some URL routes
                        '': 'showDashboard',
                        'dashboard': 'showDashboard',
                        'profile': 'showProfile',

                        'chart/*path': 'showChart',

                        // Default
                        '*actions': 'defaultAction'
                  },

                  start: function(){
                        //console.log('router start');
                        var Router = this;
                        
                        // Listen to the Dashboards chart-change event
                        // and update the 
                        Dashboard.on('change:chart', function(chartHash) {
                              if (Backbone.history.fragment != chartHash)
                                    Router.navigate('chart/' + chartHash);
                        });
                        
                        Backbone.history.start();
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
                  
                  defaultAction: function(action){
                        console.log('No route:', action);
                  }
            });
            
            return new AppRouter;

      });