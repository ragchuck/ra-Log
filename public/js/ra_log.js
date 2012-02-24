/**
 * Base
 */
var raLog = {

    /**
     * initialization
     * executed when document loads
     */
    init: function(){


	  $('#content')
	  .ajaxError(function(e, jqxhr, settings, exception) {

		var error,cl;

		try {
		    var phpError = $.parseJSON(jqxhr.responseText).error;
		    console.log(phpError);
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
			  .addClass("label "+cl)
			  .text(phpError.type+"["+phpError.code+"]")
			  .attr('title',phpError.file+':'+phpError.line))
		    .append(' '+phpError.message)
		}
		catch(ex) {
		    error = $('<p>').addClass("well").text(jqxhr.responseText);
		}


		$( this )
		.prepend(
		    $('<div>')
		    .addClass('alert alert-block alert-error fade in')
		    .append($('<a class="close" data-dismiss="alert" href="#">&times;</a>'))
		    .append($('<h4>').addClass("alert-heading").text(exception.toString()))
		    .append(error)
		    );

		//console.error(exception);
		console.groupCollapsed("Exception");
		console.log('settings:',settings);
		console.log('event:',e);
		console.log('jqxhr:',jqxhr);
		console.groupEnd();
	  })

	  // jQuery Address
	  $.address.init(function(){
		//console.log('--> address.init()');
		if ($.address.value() == '/')
		    $.address.value('/chart/day');
	  })

	  // Watch for hashchanges and executes the function
	  $.address.change(function(event){
		//console.log('--> address.change()',event.value);

		// Route chart hash changes to the chart tabs
		if( event.pathNames[0] == 'chart' ) {
		    var $tab = $('a[data-chart="'+event.pathNames[1]+'"]'),
			  href = event.value;
		    if($tab.parent().hasClass('active')) {
			  raLog.chart.load();
		    }
		    else {
			  // Activate chart-tab
			  //$tab.attr('href','#'+href).tab('show');
			  $tab.tab('show');
		    }
		}
	  })

	  // Fix broken anchors
	  $('a').live('click',function(e){
		//console.log("--> a.click()");
		var href = $(this).attr('href');
		if( href.indexOf('#') == 0 && href != '#top'){
		    e.preventDefault();
		    $.address.value(href.replace(/^#/,''))
		}
	  })

	  $('.nav-tabs a[data-chart]').on('show', function(e){
		//console.log("--> tab.show()",$tab);
		var $tab = $(e.target), // activated tab
		    chart_type = $tab.data('chart');

		// Check if the chart is loaded already
		if ($tab.data('loaded')==undefined) {
		    // Load the chart config
		    $('#'+chart_type).load('./chartoption/get/'+chart_type,null,function(){
			  $tab.data('loaded',true)
		    })
		}
		else {
		    console.log("tab is loaded");
		    raLog.chart.load();
		}

	  })

	  raLog.chart.init();
    },

    checkForUpdates: function(){
	  $.get('import/getfiles',{},function(response, status, jqxhr){

		})
    },

    chart : {

	  init: function(){

		// Set the default options
		Highcharts.setOptions({
		    global: {
			  useUTC: true
		    }
		})

	  },

	  load: function(){
		//console.log("--> chart.load()");
		var href = $.address.value();
		href = href.indexOf('.')>0 ? href.replace(/\.\w*/,'.json') : href+'.json';
		$.get('.'+href,null,raLog.chart.populate);
	  },

	  populate: function(response, status, jqxhr) {
		//console.log("--> chart.populate()",response);

		var chart = raLog.chart.charts[response.chart.type];

		// Title
		chart.setTitle(response.chart.title,response.chart.subtitle);

		// Add the series to the chart
		if (chart.series.length > 0) {
		    $.each(response.chart.series,function(i,o){
			  chart.series[i].setData(o.data,true,true);
		    })
		}
		else {
		    $.each(response.chart.series,function(i,o){
			  chart.addSeries(o);
		    })
		}

		if (response.chart.series.length != chart.series.length)
		    throw new Error("Series lenght is different.");

		var $pager = $('#'+response.chart.type+' ul.pager');

		$pager.find('li.previous a')
		    .attr('href',response.pager.prev.href)
		    .text(response.pager.prev.text);
		$pager.find('li.next a')
		    .attr('href',response.pager.next.href)
		    .text(response.pager.next.text);

	  },

	  charts: []

    }
};

$(document).ready(raLog.init);
//console.log(raLog);

