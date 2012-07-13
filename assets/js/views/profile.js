define([
      'jquery',
      'underscore',
      'backbone',

      'text!templates/profile.html',

      ], function($, _, Backbone, profileTemplate){
            //console.log(chartConfig);
            var profileView = Backbone.View.extend({

                  // Selector of the DOM-element where the dashboard is placed
                  el: '#content-container',

                  render : function() {
                        $('.nav li.active').removeClass('active');
                        $('.nav li:has(a[href=#profile])').addClass('active');

                        //var compiledTemplate = _.template(profileView, {});
                        // Append the compiled template to the content-container
                        this.$el.html(profileTemplate);
                        console.log('profileView rendered');
                  }
            });

            return new profileView;
      });
