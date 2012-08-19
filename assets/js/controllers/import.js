define([
    'underscore',
    'backbone',
    'models/import',
    'views/import'
], function (_, Backbone, ImportModel, ImportView) {

    var ImportController = function () {
        this.state = {};

        // At this time the import isn't initialized, though we want the ability to add eventListeners
        _.extend(this.state, Backbone.Events);
        this.view = new ImportView({model: this.state});
    };

    ImportController.prototype.start = function () {
        var model;

        if (this.state instanceof ImportModel && this.state.get('import_state') === 'running')
            throw "Import is already running!";

        // Initialize the import and add the eventListeners
        model = _.extend(new ImportModel, {_callbacks: this.state._callbacks});

        // Update the view's model
        this.view.model = model;
        this.state = model;

        // Start the import
        this.state.start();

        this.state.on('transition:to:succeeded', function () {
            // Restart the the import in 5 minutes
            setTimeout(this.state.start, 5 * 60 * 1000);
        }, this)
    };

    return new ImportController;
});