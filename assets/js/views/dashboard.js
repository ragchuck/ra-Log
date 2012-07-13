define([
      'jquery',
      'underscore',
      'backbone',

      'text!templates/dashboard.html',
      'json!/ra_log/config/charts.json'
      ], function($, _, Backbone, dashboardTemplate, chartConfig){
            //console.log(chartConfig);
            var dashboardView = Backbone.View.extend({

                  // Selector of the DOM-element where the dashboard is placed
                  el: '#content-container',

                  render : function() {
                        $('.nav li.active').removeClass('active');
                        $('.nav li:has(a[href=#dashboard])').addClass('active');
                        var compiledTemplate = _.template(dashboardTemplate, chartConfig);
                        // Append the compiled template to the content-container
                        this.$el.html(compiledTemplate);
                        console.log('dashboardView rendered');
                  },

                  renderChart : function() {
                        // render chart here
                  }
            });

            return new dashboardView;
      });
