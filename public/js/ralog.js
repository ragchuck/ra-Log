/**
 * Base
 */
var raLog = {
    /**
     * initialization
     * executed when document loads
     */
    init: function(){
        //raLog.chart.load('day',1263682800000);
        $( "div#container" ).ajaxError(raLog.ajaxError);
	  raLog.chart.init();
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
        $.get('import/getfiles',null,function(response){

        });
    },
    chart : {
	  config: {
		highchartsOptions: {
		    global: {
			  useUTC: true
		    }
		}
	  },
	  init: function(){

		Highcharts.setOptions(raLog.chart.config.highchartsOptions);
		console.log('hc init');
//		$.getScript(raLog.chart.config.highchartsUrl,function(){
//		    Highcharts.setOptions(raLog.chart.config.highchartsOptions);
//		});


	  },
	  load: function(type,t){
		var d = new Date(),
		    p = [];

		if(t!=undefined)
		    d = new Date(t);

		if(type==undefined)
		    type = 'day';
		/*
		switch(type) {
		    case 'day'  :p.unshift(raLog.util.n2s2(d.getDate()));
		    case 'month':p.unshift(raLog.util.n2s2(d.getMonth()+1));
		    case 'year' :p.unshift(d.getFullYear());break;
		    default     :p.unshift(type);
		}
		p.unshift('chart');
		$.getScript(p.join('/'));*/
		//$.getScript('chart/day?t='+d.getTime());
	  }
    }
};
$(document).ready(raLog.init);
console.log(raLog);

