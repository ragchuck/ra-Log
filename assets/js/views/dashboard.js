define([
      'jquery',
      'underscore',
      'backbone',

      'text!templates/dashboard.html',
      'json!/ra_log/config/charts.json',
      'views/chart',
      'models/chart'
      ], function($, _, Backbone, dashboardTemplate, chartConfig, ChartView, ChartModel){
            //console.log(chartConfig);
            var dashboardView = Backbone.View.extend({

                  // Selector of the DOM-element where the dashboard is placed
                  el: '#content-container',

                  charts: {},

                  render : function() {
                        $('.nav li.active').removeClass('active');
                        $('.nav li:has(a[href=#dashboard])').addClass('active');
                        var compiledTemplate = _.template(dashboardTemplate, chartConfig);
                        // Append the compiled template to the content-container
                        this.$el.html(compiledTemplate);
                        console.log('dashboardView rendered');
                        return this;
                  },

                  showChart : function(chartHash, callback) {

                        // First ender dashboard if it's not loaded
                        if (!$('.chart-tabs')[0]) this.render();

                        // Split the chartHash into an array
                        // e.g. 'day/2010/02/20'
                        var d = chartHash.split('/');

                        // The first string represents the chart type
                        var type = d.shift();

                        // Show the (twitter)Bootstrap tab
                        $('a[data-chart="' + type + '"]').tab('show');

                        // Build a date from the hash
                        if (d.length > 0)
                              var date = new Date(
                                          d[0],
                                          d[1] - 1 || 0,
                                          d[2] || 1
                                    );

                        var chart;

                        if (!this.charts[type]) {

                              // Create the chartModel
                              chart = new ChartModel({
                                    id: type,
                                    date: date
                              });

                              // Set up the chart's view
                              var view = new ChartView({
                                    el: '#tab-' + type,
                                    model: chart,
                                    callback: callback
                              });

                              // Store the chart's reference to the dashboard
                              this.charts[type] = view;
                              
                              // ... and fetch the data from the server
                              chart.fetch();
                        }
                        else {
                              this.charts[type].chart.showLoading();
                              chart = this.charts[type].model;
                              // only set the new date and fetch data from the server
                              // the view's render function should be called automatically
                              // after the model has changed
                              chart.set({'date': date, 'series': []}, {silent: true})
                              chart.fetch();
                        }
                  }
            });

            return new dashboardView;
      });
