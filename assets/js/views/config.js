define([
      'underscore',
      'backbone',
      'json!/ra_log/config/list/configs.json',      
      'backbone.forms.bootstrap',
      'i18n'
], function(_, Backbone, configList, Form, __) {
            
      var Config = Backbone.Model.extend({
            url: function() {
                  return 'config/' + this.get('id') + '.json'
            },
            schema: {},
            initialize: function() {
            }
            
      });
      
      var ConfigCollection = Backbone.Collection.extend({
            url: 'config.json',
            model: Config
      })
      
      var ConfigView = Backbone.View.extend({
            
            el: '#content-container',
                        
            xxtemplate: _.template('\
                  <div class="config-tabs tabbable">\
                        <ul class="nav nav-tabs">\
                              <% configs.each (function(config){ %>\
                                    <li>\
                                          <a href="#config/<%= config.get("id") %>" data-key="<%= config.get("id") %>" data-target="#tab-<%= config.get("id") %>"><%= config.get("id") %></a>\
                                    </li>\
                              <% }); %>\
                        </ul>\
                        <div class="tab-content">\
                              <% configs.each (function(config){ %>\
                                    <div class="tab-pane" id="tab-<%= config.get("id") %>">\
                                          <%= config.get("form").render().$el.html() %>\
                                    </div>\
                              <% }); %>\
                        </div>\
                  </div>'),
            template: _.template('\
                  <div class="config-tabs tabbable">\
                        <ul class="nav nav-tabs">\
                              <% _.each (configs, function(name, key){ %>\
                                    <li>\
                                          <a href="#config/<%= key %>" data-key="<%= key %>" data-target="#tab-<%= key %>"><%= name %></a>\
                                    </li>\
                              <% }); %>\
                        </ul>\
                        <div class="tab-content">\
                              <% _.each (configs, function(name, key){ %>\
                                    <div class="tab-pane" id="tab-<%= key %>"></div>\
                              <% }); %>\
                        </div>\
                  </div>'),
            
            actions: _.template('\
                  <div class="form-actions">\
                        <button type="submit" class="btn btn-primary"><%=lbl.save_changes%></button>\
                        <button type="reset" class="btn"><%=lbl.reset%></button>\
                  </div>'),
            
      
            initialize: function() {
                  _.bindAll(this);
                  this.configs = new ConfigCollection;    
            },
            
            render: function() {      
                  var _self = this;
//                  this.configs.fetch({success:function(configs){
//                        _self.$el.html(_self.template({configs:configs}));                              
//                  }});
                  this.$el.html(this.template({configs:configList.options}))
                  return this;
            },
            
            show: function(key) {
                  var _self = this;
                  if (!$('.config-tabs')[0]) this.render();
                  
                  console.log(key);
                  
                  $('.config-tabs a[data-key="' + key + '"]').tab('show');
                  $('.config-tabs #tab-' + key).text('loading...');
                  
                  var cfg = new Config({id:key});
                  cfg.fetch({
                        success: function(_cfg) {
                              var form = new Form({
                                    idPrefix: _cfg.get('id')+'-',
                                    //model: _cfg,
                                    schema: _cfg.get('schema'),
                                    data: _cfg.get('options')
                              });
                              
                              _cfg.unset('schema');
                              
                              form.render();
                              form.$el.append(_self.actions({
                                    lbl:{
                                          save_changes: __('Save changes'),
                                          reset: __('Reset')
                                    }
                              }));
                              form.$el.find('button[type=submit]').on('click',function(e){
                                    e.preventDefault();
                                    var errors = form.validate();
                                    if(!errors) {
                                          var options = form.getValue();
                                          _cfg.set('options', options);
                                          _cfg.save();
                                    }
                                                
                              });
                              $('body').popover({
                                    selector: '#import-ch_filter .popable',
                                    delay: {
                                          show: 500,
                                          hide: 0
                                    }
                              });
                              $('.config-tabs #tab-' + _cfg.get('id')).html(form.el);  
                              
                              this.form = form;
                        }
                  });
                  
                  this.configs.add(cfg);
            }
      });
      
      return new ConfigView;
})