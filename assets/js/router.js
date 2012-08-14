define([
      'backbone',
      'views/dashboard',
      'views/profile',
      'views/config'
      ], function(Backbone, Dashboard, Profile, Config){

            var AppRouter = Backbone.Router.extend({
                  routes: {
                        // Define some URL routes
                        '': 'showDashboard',
                        'dashboard': 'showDashboard',
                        'profile': 'showProfile',
                        'config': 'showConfig',
                        'config/:key': 'showConfig',

                        'chart/*path': 'showChart',

                        // Default
                        '*actions': 'defaultAction'
                  },

                  start: function(){
                        //console.log('router start');
                        var _self = this;
                        
                        // Listen to the Dashboards chart-change event
                        // and update the fragment
                        Dashboard.on('change:chart', function(chartHash) {
                              if (Backbone.history.fragment != chartHash)
                                    _self.navigate('chart/' + chartHash);
                        });
                        
                        Backbone.history.start();
                  },

                  showDashboard: function(){
                        Dashboard.render().show('day');
                  },
                  showProfile: function(){
                        Profile.render();
                  },
                  showConfig: function(key){
                        Config.show(key);
                  },
                  showChart: function(path){
                        Dashboard.show(path);
                  },
                  
                  defaultAction: function(action){
                        console.log('No route:', action);
                  }
            });
            
            return new AppRouter;

      });