define([
    'jquery',
    'underscore',
    'backbone',
    'text!templates/dashboard.html',
    'json!/ralog/config/list/charts.json'
], function ($, _, Backbone, DashboardTemplate, ChartConfig) {

    var DashboardView;

    DashboardView = Backbone.View.extend({

        // Selector of the DOM-element where the dashboard is placed
        el: '#content-container',

        template: _.template(DashboardTemplate),

        render: function () {
            $('.nav li.active').removeClass('active');
            $('.nav li:has(a[href=#dashboard])').addClass('active');

            // Append the compiled template to the content-container
            this.$el.html(this.template({chartTypes: ChartConfig.options}));

            // Reset the charts
            //this.charts = [];
            return this;
        }
    });

    return DashboardView;
});
