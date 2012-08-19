define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var ImportView;

    ImportView = Backbone.View.extend({
        el: '#import',

        events: {
            'click .btn-pause': function () {
                this.model[this.model.get('import_state') === 'paused' ? 'carryOn' : 'pause']();
            },
            'click .btn-recover': function () {
                this.model.recover();
            }
        },

        initialize: function () {
            this.model.on('transition:to:running', this.onImportStart, this);
            this.model.on('transition:to:paused', this.onImportPause, this);
            this.model.on('transition:to:succeeded', this.onImportSuccess, this);
            this.model.on('transition:to:failed', this.onImportFail, this);
            this.model.on('update_progress', this.onImportProgress, this);
        },

        onImportFail: function () {
            console.log("onImportFail", arguments);
            this.$el.find('.info').append(' --- IMPORT FAILED!');
            this.$el.addClass('alert-danger');
            this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-danger');
            this.$el.find('.btn-pause').attr('disabled', true);
            this.$el.find('.btn-recover').attr('disabled', false);
        },
        onImportStart: function () {
            console.log('onImportStart', arguments);
            this.$el.stop(true).clearQueue();
            if (this.$el.is(':hidden')) this.$el.slideDown(500);
            this.$el.removeClass('alert-success alert-danger alert-warning').addClass('alert-info');
            this.$el.find('.progress').removeClass('progress-danger progress-success progress-warning').addClass('progress-info progress-striped active');
            this.$el.find('.progress').html($('<div class="bar" />').css('width', this.model.getProgress() + '%'));
            this.$el.find('.btn-pause').attr('disabled', false);
            this.$el.find('.btn-pause i').removeClass('icon-play').addClass('icon-pause');
            this.$el.find('.btn-recover').attr('disabled', true);
        },
        onImportPause: function () {
            console.log('onImportPause', arguments);
            this.$el.addClass('alert-warning');
            this.$el.find('.info').append(' --- IMPORT PAUSED');
            this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-warning');
            this.$el.find('.btn-pause i').removeClass('icon-pause').addClass('icon-play');
        },
        onImportSuccess: function () {
            console.log('onImportSuccess', arguments);
            var processed = this.model.get('filesProcessed').length,
                count = this.model.get('filesCount');
            this.$el.find('.info').text('Import finished. (' + processed + '/' + count + ' files processed)');
            this.$el.find('.bar').css('width', '100%');
            this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-success');
            this.$el.find('.btn-pause').attr('disabled', true);
            this.$el.find('.btn-recover').attr('disabled', true);
            this.$el.removeClass('alert-info').addClass('alert-success');
            this.$el.delay(7500).slideUp(500);
        },
        onImportProgress: function (response) {
            console.log('onImportProgress', arguments);
            var currentFile = this.model.get('currentFile'),
                processed = this.model.get('filesProcessed').length,
                count = this.model.get('filesCount'),
                progress = this.model.getProgress();

            if (this.$el.is(':hidden')) this.$el.slideDown(500);
            this.$el.find('.info').text(
                _.template('Importing file (<%=i%>/<%=len%>): "<%=file%>"', {
                    'i': processed,
                    'len': count,
                    'file': currentFile
                }));

            this.$el.find('.bar').css('width', progress + '%');
        }

    });

    return ImportView;
});