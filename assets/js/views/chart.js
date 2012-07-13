define([
        'jquery',
        'backbone',
        'highcharts'
        ], function($, Backbone, Highcharts) {

      return Backbone.View.extend({
            chartOptions: {
                  chart: {
                  },

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
                  this.chartOptions = $.extend(true, {}, this.chartOptions); // deep clone
            },

            render: function() {
                  if (this.chart)
                        this.chart.destroy();

                  var options = this.chartOptions;
                  options.chart.renderTo = this.$el[0];

                  _.each(this.model, function(model, i) {
                        options.series.data.push(Number(model.value));
                        options.xAxis.categories.push(model.label);
                  }, this);

                  console.log(options.series.data);
                  console.log(options.xAxis.categories);

                  this.chart = new Highcharts.Chart(options);
                  return this;
            }

      });
});