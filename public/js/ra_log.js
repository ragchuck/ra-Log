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
	  $('#content')
	  .ajaxError(function(e, jqxhr, settings, exception) {

		$( this )
		.prepend(
		    $('<div>')
		    .addClass('alert alert-block alert-error fade in')
		    .append($('<a class="close" data-dismiss="alert" href="#">&times;</a>'))
		    .append($('<h4>').addClass("alert-heading").text(e.type))
		    .append($('<p>').text(exception.toString()))
		    .append($('<p>').addClass("well").text(jqxhr.responseText))
		    );

		console.error(exception);
		console.groupCollapsed("Exception");
		console.log('settings:',settings);
		console.log('event:',e);
		console.log('jqxhr:',jqxhr);
		console.groupEnd();
	  });

	  // jQuery Address
	  $.address.change(function(event){
		console.log('address.change()',event.value);

		if( event.pathNames[0] == 'chart' ) {
		    // Activate chart-tab
		    $('a[data-target="#'+event.pathNames[1]+'"]').tab('show');
		}
	  })

	  // Fix brokan anchors
	  $('a').on('click',function(e){
		var href = $(this).attr('href');
		if( href.indexOf('#') == 0 && href != '#top') {
		    e.preventDefault();
		    $.address.value(href.replace(/^#/,''))
		}
	  })

	  $('.nav-tabs a[data-target]').on('show', function (e) {
		var $tab = $(e.target), // activated tab
		_target = $tab.data('target'),
		_href = $tab.attr('href').replace(/^(#|\.)/, '');
		console.log("tab show",$tab);
		// Check if the chart is loaded already
		if( $tab.data('loaded') == undefined ) {
		    // Load the chart
		    $(_target).load('.'+_href,null,function(){
			  $tab.data('loaded',true)
		    })
		}
		else {

	  }

	  // Add the changed chart to browser history
	  //$.address.value(_href);
	  })


	  raLog.chart.init();
    },
    checkForUpdates: function(){
	  $.get('import/getfiles',{},function(e, jqxhr, settings){

		});
    },
    chart : {
	  init: function(){

		// Set the default options
		Highcharts.setOptions({
		    global: {
			  useUTC: true
		    }
		});

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
	  },
	  charts: []
    /* Chart.options:
		Highcharts.dateFormat('%e. %B %Y', {$time*1000|gmdate})
		    "tooltip": {
			  "shared": true,
			  "crosshairs": true,
			  "formatter": function() {
				var s = '<b>'+ Highcharts.dateFormat('%H:%M', this.x) +'</b>';
				$.each(this.points, function(i, point) {
				    s += '<br/>'+ point.series.name +': '+ point.y + '';
				});
				return s;
			  }
		    }
	   */
    }
};
$(document).ready(raLog.init);
//console.log(raLog);

