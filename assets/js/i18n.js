define([
    'underscore',
    'json!/ralog/i18n.json'

], function (_, I18n) {

    var strtr = function (string, values) {
        _.each(values, function (val, key) {
            string.replace(key, val);
        });
        return string;
    };

    return function (string, values) {
        var str = I18n[string] || string;
        return _.isEmpty(values) ? str : strtr(string, values);
    }
});