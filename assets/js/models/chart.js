define([
      'backbone',
      'highcharts',
      'collections/series',
      'models/table',
      'backbone.deep-model'
      ], function(Backbone, Highcharts, Series, Table) {

            return Backbone.DeepModel.extend({
                  
                  defaults: {
                        // The default chart
                        id : "day",
                        
                        // Date
                        date : null,
                        
                        // The default chartOptions
                        options: {
                              chart: {
                                    events: {}
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
                        }
                  },
                  
                  initialize : function () {
                        
                        var _self = this;
                        
                        this.series = new Series;                        
                        this.series.on("all", function(event_name){
                              //console.log('SERIES event recognized:', arguments);
                              _self.trigger('series:' + event_name);
                        });
                        
                        this.table = new Table;
                        this.table.on("all", function(event_name){
                              _self.trigger('table:' + event_name);
                        });
                        this.table.url = this.makeUrl('chart/table', true);
                        this.table.fetch();
                        
                        
                        this.on("change", function(event_name){
                              //console.log('CHART event recognized:', arguments);
                        });
                        
                        // Update the series' url when the date changes
                        this.on('change:date', function(){
                              this.set("options.subtitle.text", Highcharts.dateFormat("%e. %B %Y",this.get('date')));
                              this.series.url = this.makeUrl('chart/series', true);
                              this.series.fetch();
                              
                              this.table.url = this.makeUrl('chart/table', true);
                              this.table.fetch();
                        });                        
                  },

                  url: function() {
                        return this.makeUrl('chart', true);
                  },
                  
                  makeUrl: function(urlRoot, withTime) {
                        var url =  urlRoot + '/' + this.id                        
                        var date = this.get('date');
                        
                        if(!withTime || !date)
                              return url + '.json';
                        
                        return url + Highcharts.dateFormat('/%Y/%m/%d', date) + '.json';
                  }
            });

      });