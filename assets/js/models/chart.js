

define([
        'backbone',
        'highcharts'
        ], function(Backbone, Highcharts) {

      return Backbone.Model.extend({

            urlRoot : 'chart',

            defaults: {
                  "id": "day",
                  "title": { text: null },
                  "date": null
            },

            url: function() {
                  var url =  Backbone.Model.prototype.url.call(this);
                  var date = this.get('date');
                  if (date) url += Highcharts.dateFormat('/%Y/%m/%d', date);
                  return url;
            }
      });

});