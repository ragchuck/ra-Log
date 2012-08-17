define(['backbone'],function(Backbone){
      
      "use strict";
      
      return Backbone.Model.extend({
            url: function() {
                  return 'config/' + this.get('id') + '.json'
            },
            schema: {}
      });            
});