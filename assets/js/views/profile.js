define([
      'jquery',
      'underscore',
      'backbone',

      ], function($, _, Backbone){
            
             var profileView = Backbone.View.extend({

                  // Selector of the DOM-element where the dashboard is placed
                  el: '#content-container',
                  
                  template: _.template('here comes the profile'),

                  render : function() {
                        $('.nav li.active').removeClass('active');
                        $('.nav li:has(a[href=#profile])').addClass('active');

                        //var compiledTemplate = _.template(profileView, {});
                        // Append the compiled template to the content-container
                        this.$el.html(this.template());
                        //console.log('profileView rendered');
                  }
            });
            
            return new profileView;
      });
