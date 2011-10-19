/**
 * Base
 */
var raLog = {
    /**
     * initialization 
     * executed when document loads
     */
    init: function(){
        raLog.chart.load('day',1263682800000);
        $( "div#container" ).ajaxError(raLog.ajaxError);
    },
    ajaxError: function(e, jqxhr, settings, exception) {
        if (settings.dataType=='script') {
            $(this).text( "Triggered ajaxError handler. See log for mor information" ).append(jqxhr.responseText);
        }
        console.error(exception);
        console.groupCollapsed();
        console.log('settings:',settings);
        console.log('event:',e);
        console.log('jqxhr:',jqxhr);
        console.groupEnd();
    },
    checkForUpdates: function(){
        $.get('data/import-file-list',null,function(response){
            
        });
    }
};
$(document).ready(raLog.init);
console.log(raLog);

