define([
      'backbone'
      ], function(Backbone) {
      
            return Backbone.Model.extend({
                  defaults: {
                        id: "",
                        chart_id: "",
                        filters: [],
                        table_name: "",
                        column_name_x: "",
                        column_name_y: "",
                        order_by: "",
                        options: {
                              data: "",
                              legendIndex: undefined,
                              name: "",
                              stack: null,
                              type: "line",
                              xAxis: 0,
                              yAxis: 0
                        }
                  }
            });
      
      });