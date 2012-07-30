define([
      'backbone',
      'models/plot'
      ], function(Backbone, Plot) {
      
            return Backbone.Collection.extend({
                  model: Plot
            });
      
      });