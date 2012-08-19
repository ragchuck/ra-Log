define([
    'underscore',
    'backbone',
    'json!/ralog/config/list/configs.json',
    'backbone.forms.bootstrap',
    'i18n',
    'models/config'
], function (_, Backbone, configList, Form, __, ConfigModel) {

    var ConfigCollection = Backbone.Collection.extend({
        url: 'config.json',
        model: ConfigModel
    });

    var ConfigView = Backbone.View.extend({

        el: '#content-container',

        /*
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
         <% _.each (configs, function(config){ %>\
         <li>\
         <a href="#config/<%=config.key%>" data-key="<%=config.key%>" data-target="#tab-<%=config.key%>"><%=config.value%></a>\
         </li>\
         <% }); %>\
         </ul>\
         <div class="tab-content">\
         <% _.each (configs, function(config){ %>\
         <div class="tab-pane" id="tab-<%=config.key%>"></div>\
         <% }); %>\
         </div>\
         </div>'),
         */
        template: _.template('\
                        <div class="config"></div>\
                  '),

        actions: _.template('\
                  <div class="form-actions">\
                        <button type="submit" class="btn btn-primary"><%=lbl.save_changes%></button>\
                        <button type="reset" class="btn"><%=lbl.reset%></button>\
                  </div>'),


        initialize: function () {
            _.bindAll(this);
            this.configs = new ConfigCollection;
        },

        render: function () {
            //this.configs.fetch({success:function(configs){
            //    _self.$el.html(_self.template({configs:configs}));
            //}});
            this.$el.html(this.template({
                configs: configList.options
            }));
            return this;
        },

        show: function (key) {
            var _self = this;

            // Show the first options from the config list
            if (_.isUndefined(key) && !_.isUndefined(configList.options[0])) {
                _self.show(configList.options[0].key);
                return;
            }

            // Ensure that ConfigView is rendered
            if (!$('.config-tabs')[0]) this.render();

            //console.log(key);

            //$('.config-tabs a[data-key="' + key + '"]').tab('show');
            //var tab = $('.config-tabs #tab-' + key);
            _self.$el.text('loading...');

            var cfg = new ConfigModel({
                id: key
            });

            cfg.fetch({
                success: function (model) {
                    var _id = model.get('id');
                    var _schema = model.get('schema');
                    var _options = model.get('options');

                    if (_.isNull(_schema) || _.isEmpty(_schema)) {
                        _self.$el.text(__('This is a readonly config. You can view it here:'));
                        _self.$el.append($('<pre>').addClass('well').html(JSON.stringify(model.get('options'), null, 2)));
                        return;
                    }

                    var form = new Form({
                        idPrefix: _id + '-',
                        //model: _cfg,
                        schema: _schema,
                        data: _options
                    });

                    model.unset('schema');

                    form.render();
                    form.$el.append(_self.actions({
                        lbl: {
                            save_changes: __('Save changes'),
                            reset: __('Reset')
                        }
                    }));
                    form.$el.find('button[type=submit]').on('click', function (e) {
                        e.preventDefault();
                        var errors = form.validate();
                        if (!errors) {
                            var options = form.getValue();
                            model.set('options', options);
                            model.save();
                        }

                    });
                    $('body').popover({
                        selector: '#import-ch_filter .popable',
                        delay: {
                            show: 500,
                            hide: 0
                        }
                    });
                    //$('.config-tabs #tab-' + model.get('id')).html(form.el);
                    _self.$el.html(form.el);

                    _self.form = form;
                }
            });

            this.configs.add(cfg);
        }
    });

    return new ConfigView;
});