steal('jquery/controller',
      function ($) {

            "use strict";

            $.Controller('importr', {

                  filesQueue: [],
                  filesProcessed: [],
                  filesCount: 0,
                  canceled: false,

                  init : function () {
                        steal.dev.log("Import.init()");

                        // disable importing during development
                        //this.start();
                  },

                  ".js-import-start click" : function () {
                        this.start();
                  },

                  cancel : function () {
                        steal.dev.log("Import.cancel()");
                        this.canceled = true;
                        this.$alert.removeClass('alert-info').addClass('alert-danger');
                        this.$alert.find('.progress').addClass('progress-danger');
                        this.$alert.find('.btn-cancel').attr('disabled', true);
                  },

                  start : function () {
                        $.get('import/getfiles', null, this.proxy('start_load'));
                  },

                  start_load : function (response) {
                        steal.dev.log("Import.start_load()", response);
                        this.filesQueue = response.files;
                        this.filesCount = response.files.length;

                        if (this.filesCount > 0) {

                              this.$alert = window.notify(
                                    'info',
                                    'Dataload',
                                    $.View('ra_log/import/progress.mustache', {lbl : window.lbl})
                              );

                              this.$alert.find('.btn-cancel').on('click', this.proxy('cancel'))

                              this.load_next();
                        }
                  },

                  load_next : function (response) {

                        if (this.canceled === true) {
                              steal.dev.log("Import.load_next() CANCELED");
                              this.canceled = false;
                              return;
                        }

                        if (this.filesQueue.length > 0) {

                              var currentFile = this.filesQueue[0],
                                    progress = 100 - this.filesQueue.length / this.filesCount * 100;

                              this.filesProcessed.push(this.filesQueue.shift());
                              this.$alert.find('.bar').css('width', progress + '%');

                              $.post('import/file', {file : currentFile}, this.proxy('load_next'));

                        } else {
                              this.$alert.find('.bar').css('width', '100%');
                              this.$alert.find('.progress').addClass('progress-success');
                              this.$alert.find('.btn-cancel').attr('disabled', true);
                              this.$alert.removeClass('alert-info').addClass('alert-success');
                              this.$alert.delay(5000).slideUp(750);
                        }
                  }
            });
      });