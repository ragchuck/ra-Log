define([
        'jquery',
        'underscore',
        'backbone',
        'highcharts'
        ], function($, _, Backbone, Highcharts) {

      nf = function(number, c) {
            return  Highcharts.numberFormat(number, c, ',', '.');
      }

      var chartDetailView = '\
            <div class="pager">\
                  <ul class="pager">\
                        <li class="previous">\
                              <a href="<%= pager.prev.href %>"><%= pager.prev.text %></a>\
                        </li>\
                        <li class="next">\
                              <a href="<%= pager.next.href %>"><%= pager.next.text %></a>\
                        </li>\
                  </ul>\
            </div>\
            <div class="table">\
                  <table class="table table-bordered table-condensed">\
                        <% _.each(table, function(row, i) { %>\
                        <tr>\
                              <% if(i == 0) { %>\
                              <td><%= row[0] %></td>\
                              <th><%= row[1] %></th>\
                              <th><%= row[2] %></th>\
                              <th><%= row[3] %></th>\
                              <th><%= row[4] %></th>\
                              <th><%= row[5] %></th>\
                              <th><%= row[6] %></th>\
                              <% } else { %>\
                              <th><%= row[0] %></th>\
                              <td class="value"><%= nf(row[1], 2) %></td>\
                              <td class="value"><%= row[2] %></td>\
                              <td class="value"><%= nf(row[3], 2) %></td>\
                              <td class="value"><%= nf(row[4], 2) %></td>\
                              <td class="value"><%= nf(row[5], 2) %></td>\
                              <td class="value"><%= nf(row[6] * 100, 1) %></td>\
                              <% } %>\
                        </tr>\
                        <% }) %>\
                  </table>\
            </div>';

      return Backbone.View.extend({
            chartOptions: {
                  chart: {
                        events: {
                        }
                  },
                  plotOptions: {
                        area: {
                              marker: {
                                    symbol: 'circle',
                                    radius: 0,
                                    states: {
                                          hover: {
                                                enabled: true,
                                                radius: 4
                                          }
                                    }
                              }
                        }
                  },
                  title: { text: null },
                  subtitle: { text: null },
                  credits: { enabled: false },
                  series: {
                        data: []
                  },
                  xAxis: {
                        type: 'datetime'
                  },
                  yAxis: {
                        min: 0,
                        title : {
                              text : null
                        }
                  },
                  tooltip: {
                        shared: true,
                        //crosshairs: true,
                        formatter: function() {
                              var s = '<b>' + Highcharts.dateFormat('%H:%M', this.x) + '</b>';
                              $.each(this.points, function(i, point) {
                                    s += '<br/>' + point.series.name + ': ' + Highcharts.numberFormat(point.y, 2, ',', '.') + ' W';
                              });
                              return s;
                        }
                  }
            },

            initialize: function() {
                  _.bindAll(this);
                  this.model.bind('change', this.render);
            },

            render: function() {
                  var options = _.clone(this.chartOptions);
                  options = _.extend(options, this.model.toJSON());

                  var chart = this.chart;

                  if (!chart) {

                        options.chart.renderTo = this.$el.find('.chart-container')[0];
                        chart = new Highcharts.Chart(options);
                        this.chart = chart;
                        if (typeof this.callback == "function")
                              callback();
                  }
                  else {

                        // Remove all series from the chart
                        _.each(chart.series, function(series) {
                              series.remove(false)
                        });

                        // Reset the color's counter to begin with the first color
                        chart.counters.color = 0;

                        // Add the new series to the charts
                        _.each(options.series, function(series) {
                              chart.addSeries(series, false);
                        })
                        
                        // Update the chart's title
                        chart.setTitle(options.title, options.subtitle);
                        chart.redraw();
                  }

                  chart.hideLoading();

                  // Update the chart details
                  this.$el.find('.chart-details').html(_.template(chartDetailView, options))

                  return this;
            }

      });
});