steal('jquery/controller',
      function ($) {

            "use strict";

            $.Controller('Notify', {

                  $nc : null,
                  $ncToggler : null,
                  $ncTogglerA : null,
                  $ncContent : null,

                  init : function () {
                        steal.dev.log("Controller Notify.init()");

                        // get the notify template
                        this.$nc = $($.View('ra_log/notify/notify.mustache', {}));

                        // add the notification-center template to page
                        $('#page').prepend(this.$nc);

                        // bind the actions
                        this.$nc.find('.toggler a').on('click', this.proxy('toggle'));
                        this.$nc.find('.toggler a').on('mouseenter mouseleave', this.proxy('shiv'));
                        this.$nc.ajaxError(this.proxy('ajaxError'));
                        window.notify = this.proxy('alert');

                  },

                  ajaxError : function (e, jqxhr, settings, exception) {
                        var error, cl, phpError

                        try {
                              phpError = $.parseJSON(jqxhr.responseText).error;
                              steal.dev.log(phpError);
                              switch (phpError.type) {
                              case 'error':
                                    cl = 'label-important';
                                    break;
                              case 'warning':
                                    cl = 'label-warning';
                                    break;
                              case 'notice':
                                    cl = 'label-info';
                                    break;
                              default:
                                    cl = '';
                              }

                              error = $('<p>')
                                    .append($('<span>')
                                          .addClass("label " + cl)
                                          .text(phpError.type + "[" + phpError.code + "]")
                                          .attr('title', phpError.file + ':' + phpError.line))
                                    .append(' ' + phpError.message);
                        } catch (ex) {
                              error = $('<p>').addClass("well").text(jqxhr.responseText);
                        }

                        this.alert('error', exception.toString(), error);


                        //console.error(exception);
//                        steal.dev.log('settings:', settings);
//                        steal.dev.log('event:', e);
//                        steal.dev.log('jqxhr:', jqxhr);
                  },

                  toggle : function () {
                        steal.dev.log("Notify.toggle()");
                        this.$nc.find('.content').slideToggle(300, function () {
                              $(this).parent().find('.toggler a i').removeClass('icon-chevron-down icon-chevron-up');
                        });
                  },

                  shiv : function () {
                        this.$nc.find('.toggler a i').toggleClass(
                              this.$nc.find('.content').is(":visible") ?
                                          'icon-chevron-up' :
                                          'icon-chevron-down'
                        );
                  },

                  alert : function (type, heading, content) {
                        steal.dev.log("Notify.alert()", arguments);
                        content = typeof (content) !== "object" ? $(content) : content;
                        var $alert = $($.View('ra_log/notify/notify-alert.mustache', {
                              type: type,
                              heading: heading
                        }));
                        $alert.find('.alert-content').append(content);

                        this.$nc.find('.content').prepend($alert).slideDown(150);
                        this.$nc.find('.toggler a i').removeClass('icon-white');
                        return $alert;
                  }

            });
      });