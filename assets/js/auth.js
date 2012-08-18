define([
      'jquery',
      'underscore',
      'backbone',
      'i18n',      
      'backbone.forms.bootstrap',
      
      'libs/util/md5',

      ], function($, _, Backbone, __, Form){
            
            var AuthUser = Backbone.Model.extend({
                  schema: {
                        'username': {
                              type: 'Text',
                              validators: [{
                                    type: 'required', 
                                    message: __("Username is required")
                              }] 
                        },
                        'password': {
                              type: 'Password',
                              validators: [{
                                    type: 'required', 
                                    message: __("Enter your password")
                              }] 
                        },
                        'remember': {
                              type: 'Checkbox'
                        }
                  },
                  defaults: {
                        id: null,
                        username: '',
                        password: '',
                        remember: false,
                        roles: []
                  },
                  url: 'auth/login',
                  initialize: function() {
                        _.bindAll(this);
                  },
                  logged_in: function() {
                        var id = this.get('id');
                        return (id !== undefined && id !== null);
                  },
                  /**
                   * @param roles
                   * @return bool if user has all given roles
                   */
                  has_role: function() {
                        var role, found = false, _self = this;
                        // take a look on _.detect
                        switch(arguments.length) {
                              case 0:
                                    found = this.get('roles').length > 0;
                                    break;
                              case 1:
                                    role = arguments[0];
                                    break;
                              default:
                                    role = arguments;
                                    break;
                        }
                        switch(typeof role) {
                              case 'string':
                                    _.each(this.get('roles'), function(_role) {
                                          if(_role.name == role)
                                                found = true;
                                    }); 
                                    break;
                              case 'number':
                                    _.each(this.get('roles'), function(_role) {
                                          if(_role.id == role)
                                                found = true;
                                    }); 
                                    break;
                              case 'object':
                                    found = _.reduce(role, function(_found, _role) {
                                          return _found && _self.has_role(_role);
                                    }, true); 
                                    break;
                        }                        
                        return found;
                  },
                  login: function() {
                        var _self = this;
                        $.ajax({
                              url: 'auth/login.json',
                              type: 'POST',
                              dataType: 'json',
                              contentType: 'application/json',
                              data: JSON.stringify({
                                    username: this.get('username'),
                                    password: this.get('password'), //md5(this.get('password')),
                                    remember: this.get('remember')
                              }),
                              success: function(response) {
                                    //console.log(response.message);
                                    if (response.user) {
                                          _self.set(response.user);
                                          _self.trigger('login:success', _self);
                                    }
                                    else {                      
                                          _self.trigger('login:error', response.message);
                                    }
                              },
                              error:  function() {
                                    console.log('login error', arguments)                        
                              }
                        });
                  },
                  logout: function() {
                        var _self = this;
                        $.ajax({
                              url: 'auth/logout.json',
                              type: 'POST',
                              dataType: 'json',
                              contentType: 'application/json',
                              data: JSON.stringify(this.attributes),
                              success: function(response) {
                                    //console.log(response.message);
                                    if (!response.user) {
                                          _self.clear();
                                          _self.trigger('logout:success');
                                    }
                                    else {                      
                                          _self.trigger('logout:error', response.message);
                                    }
                              },
                              error:  function() {
                                    console.log('logout error', arguments)                        
                              }
                        });
                  },
                  live: function() {                        
                        var _self = this;
                        $.ajax({
                              url: 'auth/live.json',
                              type: 'GET',
                              dataType: 'json',
                              success: function(response) {
                                    //console.log(response.message);
                                    if(response.user) {
                                          _self.set(response.user);
                                          _self.trigger('login:success', _self);
                                    }
                              },
                              error: function() {
                                    console.log('live error', arguments)  
                              }
                        });
                  }
            });
            
            var authUser = new AuthUser;

            var LoginView = Backbone.View.extend({

                  tagName: 'div',
                  className: 'modal fade',            
                  events: {
                        'click .btn-primary': 'submit',
                        'keypress input[type=text], input[type=password]': 'onKeyPress'
                  },
                  rendered: false,
                  tmplModal: _.template('\
                        <div class="modal-header">\
                              <a class="close" data-dismiss="modal">×</a>\
                              <h3><%=lbl.login%></h3>\
                        </div>\
                        <div class="modal-body">\
                        </div>\
                        <div class="modal-footer">\
                              <a href="#" class="btn" data-dismiss="modal"><%=lbl.close%></a>\
                              <a href="#" class="btn btn-primary"><%=lbl.login%></a>\
                        </div>'),
                  
                  tmplError: _.template('\
                        <div class="alert alert-error">\
                              <a class="close" data-dismiss="alert">×</a>\
                              <%= message %>\
                        </div>'),
            
                  initialize: function() {
                        var _self = this;
                        this.model = authUser;
                        this.model.bind('login:success', function(){
                              _self.$el.modal('hide');
                        });
                        this.model.bind('login:error', function(error){
                              _self.$el.find('.modal-body:not(:contains("'+error+'"))').prepend(_self.tmplError({
                                    message: error
                              }));
                        });
                        this.form = new Form({
                              model: this.model
                        });
                  },

                  render: function() {
                        if (this.rendered)
                              this.$el.modal('show');
                        else {
                              this.$el.html(this.tmplModal({
                                    lbl: {
                                          login:__("Login"),
                                          close:__("Close")
                                    }
                              }));
                              this.$el.appendTo('body');
                              this.$el.modal();
                              this.rendered = true;                              
                        }
                        this.$el.find('.modal-body').html(this.form.render().el)
                        return this;
                  },
                  
                  submit: function(e) {
                        e.preventDefault();
                        var errors = this.form.commit();
                        if(!errors)
                              this.model.login();
                  },
                  
                  onKeyPress: function(e) {       
                        if (e.keyCode != 13) return;
                        this.submit(e);
                  }
            });
            
            return {
                  loginView: new LoginView,
                  user: authUser,
                  logout: function() {
                        authUser.logout()
                  }
            }
      });
