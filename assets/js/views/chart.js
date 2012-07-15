define([
        'jquery',
        'underscore',
        'backbone',
        'highcharts'
        ], function($, _, Backbone, Highcharts) {

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
            <div class="table"></div>';

      return Backbone.View.extend({
            chartOptions: {
                  chart: {},
                  plotOptions: {
                        area: {
                              marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                          hover: {
                                                enabled: true
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
                                    s += '<br/>' + point.series.name + ': ' + point.y + '';
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
                  }
                  else {
                        // Update the chart's title
                        chart.setTitle(options.title, options.subtitle);

                        $.each(options.series, function(i, o) {
                              chart.series[i].setData(o.data, true, true);
                        });

                  }

                  // Update the chart details
                  this.$el.find('.chart-details').html(_.template(chartDetailView, options))

                  return this;
            }

      });
});