define([
      'jquery',
      'underscore',
      'backbone',

      'json!/ra_log/config/list/charts.json',
      'views/chart',
      'models/chart'
      ], function($, _, Backbone, chartConfig, ChartView, ChartModel){
            //console.log(chartConfig);
            var dashboardView = Backbone.View.extend({

                  // Selector of the DOM-element where the dashboard is placed
                  el: '#content-container',

                  charts: [],
                  
                  template: _.template('\
                        <div class="chart-tabs tabbable tabs-right">\
                              <ul class="nav nav-tabs">\
                                    <% _.each (chartTypes, function(chartType){ %>\
                                          <li>\
                                                <a href="#chart/<%=chartType.key%>" data-chart="<%=chartType.key%>" data-target="#tab-<%=chartType.key%>"><%=chartType.value%></a>\
                                          </li>\
                                    <% }); %>\
                              </ul>\
                              <div class="tab-content">\
                                    <% _.each (chartTypes, function(chartType){ %>\
                                          <div class="tab-pane" id="tab-<%=chartType.key%>">\
                                                <div class="chart-container"></div>\
                                                <div class="chart-pager"></div>\
                                                <div class="chart-table"></div>\
                                          </div>\
                                    <% }); %>\
                              </div>\
                        </div>'),

                  render : function() {
                        $('.nav li.active').removeClass('active');
                        $('.nav li:has(a[href=#dashboard])').addClass('active');
                        
                        // Append the compiled template to the content-container
                        this.$el.html(this.template({chartTypes:chartConfig.options}));
                        
                        // Reset the charts
                        this.charts = [];
                        return this;
                  },

                  show : function(chartHash) {

                        // First ender dashboard if it's not loaded
                        if (!$('.chart-tabs')[0]) this.render();

                        // Split the chartHash into an array
                        // e.g. 'day/2010/02/20'
                        var d = chartHash.split('/');

                        // The first string represents the chart type
                        var type = d.shift();

                        // Show the (twitter)Bootstrap tab
                        $('.chart-tabs a[data-chart="' + type + '"]').tab('show');

                        // Build a date from the hash
                        if (d.length > 0)
                              var date = new Date(
                                          d[0],
                                          d[1] - 1 || 0,
                                          d[2] || 1
                                    );
                        else
                              date = new Date();

                        var chart;

                        if (!this.charts[type]) {

                              // Create the chartModel
                              chart = new ChartModel({
                                    "id": type,
                                    "date": date
                              });
                              

                              // Set up the chart's view
                              var view = new ChartView({
                                    el: '#tab-' + type,
                                    model: chart
                              });
                              
                              
                              chart.fetch({
                                    success: function(){
                                          view.render();
                                    }
                              });

                              // Store the chart's reference to the dashboard
                              this.charts[type] = view;
                              
                        // ... and fetch the data from the server
                        //chart.fetch();
                        }
                        else {
                              this.charts[type].chart.showLoading();
                              chart = this.charts[type].model;
                              // only set the new date and fetch data from the server
                              // the view's render function should be called automatically
                              // after the model has changed
                              chart.set({
                                    'date': date, 
                                    'series': []
                              })
                        //chart.fetch();
                        }
                        
                        // Trigger an event
                        this.trigger('change:chart', chartHash);
                  }
            });

            return new dashboardView;
      });
