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
                        $("*[data-action=import_start]").live("click", this.proxy("start"));

                        // fetch labels
                        $.get('i18n/import', null, function (response) {
                              $.extend(window.lbl, response);
                        })
                        this.start();
                  },

                  cancel : function () {
                        steal.dev.log("Import.cancel()");
                        this.canceled = true;
                        this.$alert.removeClass('alert-info').addClass('alert-danger');
                        this.$alert.find('.progress').addClass('progress-danger');
                        this.$alert.find('.btn-cancel').attr('disabled', 'disabled');
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

                              steal.dev.log(this.$alert);

                              this.load_next();
                        }
                  },

                  load_next : function (response) {

                        if (this.canceled === true) {
                              steal.dev.log("Import.loadNext() CANCELED");
                              this.canceled = false;
                              return;
                        }

                        if (this.filesQueue.length === 0) {
                              throw new Error("No files in queue!")
                        }

                        var currentFile = this.filesQueue[0],
                              progress = 100 - this.filesQueue.length / this.filesCount * 100;

                        this.$alert.find('.bar').css('width', progress + '%');

                        this.filesProcessed.push(this.filesQueue.shift());

                        if (this.filesQueue.length > 0) {
                              $.post('import/file', {file : currentFile}, this.proxy('load_next'));
                        } else {
                              this.$alert.find('.bar').css('width', '100%');
                              this.$alert
                                    .removeClass('alert-info')
                                    .addClass('alert-success');

                              this.$alert.find('.progress').addClass('progress-success');
                              this.$alert.find('.btn-cancel').attr('disabled', true);

                              //window.setTimeout("raLog.importer.$alert.slideUp(750)", 5000);
                        }
                  },
            });
      });