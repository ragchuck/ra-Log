define([
    'underscore',
    'backbone',

    'backbone.workflow'
], function (_, Backbone) {

    var ImportStateModel;
    ImportStateModel = Backbone.Model.extend({

        defaults: {
            filesQueue: [],
            filesProcessed: [],
            filesCount: 0,
            currentFile: null,
            last_response: null // stores the last response if paused
        },

        workflow: {
            initial: 'initialized',
            events: [
                {
                    name: 'start',
                    from: 'initialized',
                    to: 'running'
                },
                {
                    name: 'pause',
                    from: 'running',
                    to: 'paused'
                },
                {
                    name: 'carryOn',
                    from: 'paused',
                    to: 'running'
                },
                {
                    name: 'fail',
                    from: 'running',
                    to: 'failed'
                },
                {
                    name: 'recover',
                    from: 'failed',
                    to: 'running'
                },
                {
                    name: 'succeed',
                    from: 'running',
                    to: 'succeeded'
                }
            ]
        },

        initialize: function () {
            _.bindAll(this);
            _.extend(this, new Backbone.Workflow(this, {attrName: 'import_state'}));
            //this.on('all',function(){console.log(arguments)});
        },

        getProgress: function () {
            var processed = this.get('filesProcessed').length - 1,
                count = this.get('filesCount');
            return processed / count * 100;
        },

        start: function () {
            var state = this;

            if (state.get('import_state') !== "initialized") {
                throw "Cannot start importing. current state: " + state.get('import_state');
            }

            this.triggerEvent('start');

            this.set('filesProcessed', []);
            this.set('filesQueue', []);
            this.set('filesCount', 0);

            $.ajax({
                url: 'import/getFiles',
                type: 'GET',
                success: function (response) {
                    var filesQueue = response.files;

                    state.set('filesQueue', filesQueue);
                    state.set('filesCount', filesQueue.length);

                    if (filesQueue.length > 0) {
                        state._loadNext();
                    }
                }
            });
        },

        pause: function () {
            this.triggerEvent('pause');
        },

        carryOn: function () {
            this.triggerEvent('carryOn');
            this._loadNext(this.get('last_response'));
        },

        fail: function () {
            this.triggerEvent('fail');
        },

        recover: function () {
            this.triggerEvent('recover');
            this._loadNext();
        },

        _loadNext: function (response) {
            var state = this;

            state.set('last_response', response);

            // Abort here if the import isn't running
            if (state.get('import_state') !== "running") return;

            if (state.get('filesQueue').length > 0) {

                var filesQueue = state.get('filesQueue'),
                    filesProcessed = state.get('filesProcessed'),
                    currentFile = filesQueue[0];

                filesProcessed.push(filesQueue.shift());

                this.set({
                    "filesProcessed": filesProcessed,
                    "filesQueue": filesQueue,
                    "currentFile": currentFile
                });

                $.ajax({
                    type: 'POST',
                    url: 'import/file',
                    data: {
                        file: currentFile
                    },
                    success: this._loadNext,
                    error: function () {
                        state.triggerEvent('fail');
                    }
                });

                // Tell the listeners that we're progressing
                state.trigger('update_progress', response);

            } else {
                // Tell the listeners that we've finished
                state.triggerEvent('succeed');
            }
        }
    });

    return ImportStateModel;
});
