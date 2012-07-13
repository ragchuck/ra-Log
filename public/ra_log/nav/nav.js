steal('jquery/controller/route',
      'jquery-validation/jquery.validate.js',

      function ($) {

            "use strict";

            // Labels
            window.lbl = {};
            $.get('i18n/labels', null, function (response) {
                  $.extend(window.lbl, response);
                  if (window.lbl.validator_messages) {
                        var messages = {};
                        $.each(window.lbl.validator_messages, function (i, v) {
                              messages[i] = v.indexOf("{0}") > -1 ? $.validator.format(v) : v;
                        });
                        $.extend($.validator.messages, messages);
                  }
            });


            $.Controller('Navigation', {

                  init : function () {
                        steal.dev.log("Controller Navigation.init()");
                        this.$content = $('#content');

                        // enable bootstrap popover to validator
                        $.validator.setDefaults({
                              validClass: 'success',
                              errorClass: 'error',
                              errorElement: 'span',
                              highlight: function (element, errorClass, validClass) {
                                    $(element).parents("div.control-group").addClass(errorClass).removeClass(validClass);
                              },
                              unhighlight: function (element, errorClass, validClass) {
                                    $(element).parents(".error").removeClass(errorClass).addClass(validClass);
                              },
                              errorPlacement: function (err, element) {
                                    $(element).after(err.addClass('help-inline'));
                              }
                        });

                        $(window).load(function(){
                              steal.dev.log('window.load()', arguments);
                        });
                  },

                  ".js-login click" : function (el, e) {

                        e.preventDefault();

                        var view = $.View('/user/login.html', {});

                        this.dialog = bootbox.dialog(view, [{
                              "label" : "Cancel",
                              "class" : ""
                        }, {
                              "label" : "Login",
                              "callback" : function () {
                                    $('form.login').submit();
                              }
                        }], {
                              "header" : window.lbl.login
                        });

                        // Add form validation
                        $('form.login').validate({
                              submitHandler: function (form) {
                                    steal.dev.log("form.login.validate.submitHandler()");
                                    $.ajax({
                                          url: '/user/login',
                                          type: 'POST',
                                          data: {
                                                username: $(form.username).val(),
                                                password: $(form.password).val()
                                          },
                                          context: form,
                                          success: function () {

                                          }
                                    });
                              }
                        });
                  },

                  "/:controller route" : function (a) {
                        steal.dev.log("Controller Navigation: '/:action route' function()", arguments);
                        this.activate(a.controller);
                  },

                  "activate" : function (controller) {
                        steal.dev.log("Controller Navigation.activate()", controller);
                        var container_id = 'content-' + controller,
                              container = this.$content.find('#' + container_id);

                        $('.nav').find('.active').removeClass('active');
                        $('.nav').find('.js-' + controller).parent('li').addClass('active');

                        window.document.title = 'ra|Log - ' + controller;

                        this.$content.children('.active').removeClass('active');

                        if (container.length) {
                              container.addClass('active');
                        } else {
                              container = $('<div>').attr('id', container_id).addClass('active').load(controller);
                              this.$content.append(container);
                        }
                  }

            });
      });