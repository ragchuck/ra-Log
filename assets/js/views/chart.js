define([
    'jquery',
    'underscore',
    'backbone',
    'highcharts',

    'jquery.highcharts'
], function ($, _, Backbone, Highcharts) {

    _.nf = function (number, c) {
        return  Highcharts.numberFormat(number, c, ',', '.');
    };

    _.df = function (date) {
        var date_part = Highcharts.dateFormat('%d.%m.%Y', date);
        var date_link = Highcharts.dateFormat('<a href="#chart/day/%Y/%m/%d">' + date_part + '</a>', date);
        var time_part = Highcharts.dateFormat('%H:%M:%S', date);
        return time_part != '00:00:00' ? time_part : date_link;
    };

    return Backbone.View.extend({

        tmplPager : _.template('\
                        <div class="pager">\
                              <ul class="pager">\
                                    <li class="previous">\
                                          <a href="<%= prev.href %>"><%= prev.text %></a>\
                                    </li>\
                                    <li class="next">\
                                          <a href="<%= next.href %>"><%= next.text %></a>\
                                    </li>\
                              </ul>\
                        </div>'),

        tmplTable : _.template('\
                        <div class="table">\
                              <table class="table table-bordered table-condensed">\
                                    <% _.each(table.rows, function(row, i) { %>\
                                    <tr>\
                                          <% if(i == 0) { %>\
                                          <td><%= row[0] %></td>\
                                          <th class="header"><%= row[1] %></th>\
                                          <th class="header"><%= row[2] %></th>\
                                          <th class="header"><%= row[3] %></th>\
                                          <th class="header"><%= row[4] %></th>\
                                          <th class="header"><%= row[5] %></th>\
                                          <th class="header"><%= row[6] %></th>\
                                          <% } else { %>\
                                          <th><%= row[0] %></th>\
                                          <td class="value"><%= _.nf(row[1], 2) %></td>\
                                          <td class="value"><%= _.df(row[2]) %></td>\
                                          <td class="value"><%= _.nf(row[3], 2) %></td>\
                                          <td class="value"><%= _.nf(row[4], 2) %></td>\
                                          <td class="value"><%= _.nf(row[5], 2) %></td>\
                                          <td class="value"><%= _.nf(row[6] * 100, 1) %></td>\
                                          <% } %>\
                                    </tr>\
                                    <% }) %>\
                              </table>\
                        </div>'),

        initialize : function () {
            _.bindAll(this);
            //this.model.bind('change', this.render);
            this.model.bind('series:reset', this.renderSeries);
            this.model.bind('table:change', this.renderTable);
            this.model.bind('change:date', this.renderMeta)
        },

        render : function () {

            var self = this,
                options = self.model.get('options'),
                chart = self.chart;

            this.renderMeta();

            if (!chart) {
                self.$el.find('.chart-container').highcharts({options : options}, function () {
                    self.chart = this;
                    self.chart.hideLoading();

                    // Trigger 'load' event when the chart is loaded
                    self.trigger('chart:load');
                });
            }
            else {
                console.log('chart already loaded.');
            }

            // Update the chart details
            //this.$el.find('.chart-details').html(_.template(chartDetailView, options))
            return this;
        },

        renderSeries : function () {

            var Chart = this.chart;
            var Series = this.model.series;

            // Remove all series from the chart
            _.each(Chart.series, function (series) {
                series.remove(false)
            });

            // Reset the color's counter to begin with the first color
            Chart.counters.color = 0;

            // Add the new series to the charts
            Series.each(function (plot) {
                Chart.addSeries(plot.get('options'), false);
            });
            Chart.hideLoading();
            Chart.redraw();
            return this;
        },

        renderMeta : function () {

            var model = this.model,
                date = model.get('date'),
                type = model.get('id'),
                options = model.get('options'),
                chartUrl, nextDateFormat, prevDateFormat,
                nextDate = this.dateAdd(date, type, 1),
                prevDate = this.dateAdd(date, type, -1);

            if(this.chart) {
                this.chart.showLoading();
                this.chart.setTitle(options.title, options.subtitle);
            }

            switch (type) {
                case "day":
                    chartUrl = "#chart/" + type + "/%Y/%m/%d";
                    nextDateFormat = "%e. %b %Y &rarr;";
                    prevDateFormat = "&larr; %e. %b %Y";
                    break;
                case "month":
                    chartUrl = "#chart/" + type + "/%Y/%m";
                    nextDateFormat = "%b %Y &rarr;";
                    prevDateFormat = "&larr; %b %Y";
                    break;
                case "year":
                    chartUrl = "#chart/" + type + "/%Y";
                    nextDateFormat = "%Y &rarr;";
                    prevDateFormat = "&larr; %Y";
                    break;
                default:
                    chartUrl = "#chart/" + type;
                    break;
            }

            if ($.inArray(type, ["day", "month", "year"]) > -1) {
                this.$el.find('.chart-pager').html(this.tmplPager({
                    next : {
                        href : Highcharts.dateFormat(chartUrl, nextDate),
                        text : Highcharts.dateFormat("%e. %b %Y &rarr;", nextDate)
                    },
                    prev : {
                        href : Highcharts.dateFormat(chartUrl, prevDate),
                        text : Highcharts.dateFormat("&larr; %e. %b %Y", prevDate)
                    }
                }));
            }
            else {
                this.$el.find('.chart-pager').empty();
            }
        },

        renderTable : function () {
            this.$el.find('.chart-table').html(
                this.tmplTable({
                    table : this.model.table.attributes
                })
            );
        },

        dateAdd : function (objDate, sInterval, iNum) {
            var objDate2 = new Date(objDate);
            if (!sInterval || iNum == 0) return objDate2;
            switch (sInterval.toLowerCase()) {
                case "day":
                    objDate2.setDate(objDate2.getDate() + iNum);
                    break;
                case "month":
                    objDate2.setMonth(objDate2.getMonth() + iNum);
                    break;
                case "year":
                    objDate2.setFullYear(objDate2.getFullYear() + iNum);
                    break;
            }
            return objDate2;
        }

    });
});