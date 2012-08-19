define([
    'jquery',
    'underscore',
    'backbone',
    'views/dashboard',
    'models/chart',
    'views/chart',

    'jquery.dateFormat'
], function ($, _, Backbone, DashboardView, ChartModel, ChartView) {

    var DashboardController = function () {
        this.charts = [];
        this.view = new DashboardView;
        _.bindAll(this);
    };
    DashboardController.prototype.show = function (chartHash) {

        // First render dashboard if it's not loaded
        if (!$('.chart-tabs')[0]) this.view.render();

        // Split the chartHash into an array
        // e.g. 'day/2010/02/20'
        var chartHashElements = chartHash.split('/');

        // The first string represents the chart type
        var type = chartHashElements.shift();

        // Show the (twitter)Bootstrap tab
        $('.chart-tabs a[data-chart="' + type + '"]').tab('show');

        // Build a date from the hash
        var date;
        if (chartHashElements.length > 0) {
            date = new Date(
                chartHashElements[0],
                chartHashElements[1] - 1 || 0,
                chartHashElements[2] || 1
            );
        }
        else {
            date = new Date();
        }

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
                success: function () {
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
            });
            //chart.fetch();
        }

        // Trigger an event
        this.view.trigger('change:chart', chartHash);
    };

    DashboardController.prototype.update = function (response) {

        var dayChart = this.charts['day'];

        if (!response || !response.count || !dayChart) return;

        // Create a shortcut for date format
        var _df = function (value) {
            return $.format.date(value, 'yyyy/MM/dd');
        }

        // Create a callback to add the new points from the processed file
        var _addPoints = function () {

            // Don't add points, which are not 'new' to the chart
            // This can be triggered by <code>this._load_next(this.last_response);</code>
            if (dayChart.chart.xAxis[0].getExtremes().dataMax > response.data[0][0])
                return;

            // Loop throw the response's data points
            // and add them without redrawing
            _.each(response.data, function (point) {

                // only add points if it's on the chart's scale...
                if (dp == _df(point[0]))
                    dayChart.chart.series[0].addPoint(point, false);
            });

            // Redraw the chart after the points are added
            dayChart.chart.redraw();
        }

        // Make a date from the first data-point
        var dp = _df(response.data[0][0]);

        // Check if the current dayChart corresponds with the loaded points
        if (_df(dayChart.model.get('date') || 0) != dp) {
            // Show the chart
            var path = 'day/' + dp;
            dayChart.on('chart:load', _addPoints);
            this.show(path);
        }
        else { // Current chart is already loaded
            _addPoints();
        }


    };

    return new DashboardController;
});
