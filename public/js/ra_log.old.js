/**
 * Base
 */
var raLog = {

    /**
     * initialization
     * executed when document loads
     */
    init: function(){


	  raLog.notifier.init();
	  raLog.chart.init();



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
		switch (event.pathNames[0]) {
		    case 'chart':
			  // Find the tab
			  var $tab = $('a[data-chart="'+event.pathNames[1]+'"]');
			  if($tab.parent().hasClass('active')) {
				//console.log('address.change() --> chart.load()');
				raLog.chart.load();
			  }
			  else {
				// Activate chart-tab
				//$tab.attr('href','#'+href).tab('show');
				$tab.tab('show');
			  }
			  break;
		    default:
		// do nothing
		}
	  })

	  // Fix broken anchors
	  $('a:not(.prevent-default)').live('click',function(e){
		//console.log("--> a.click()");
		var href = $(this).attr('href');
		if (href.indexOf('#') == 0 && href != '#top'){
		    e.preventDefault();
		    $.address.value(href.replace(/^#/,''))
		}
	  })

	  $('a.prevent-default').live('click',function(e){
		e.preventDefault();
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
		    console.log('a.click() --> chart.load()');
		    raLog.chart.load();
		}

	  })

	  $('.action-importstart').click(function(){
		raLog.importer.start();
	  })


    //raLog.importer.start();

    },

    notifier : {

	  $nc : null,
	  $ncToggler : null,
	  $ncTogglerA : null,
	  $ncContent : null,

	  init: function() {

		raLog.notifier.$nc = $('#notification-center');
		raLog.notifier.$ncToggler = raLog.notifier.$nc.find('.toggler');
		raLog.notifier.$ncTogglerA = raLog.notifier.$nc.find('.toggler a');
		raLog.notifier.$ncContent = raLog.notifier.$nc.find('.content');

		raLog.notifier.$ncTogglerA.on('click',function(){
		    raLog.notifier.$ncContent.slideToggle(300,function(){
			  raLog.notifier.$ncTogglerA.find('i').removeClass('icon-chevron-down icon-chevron-up')
		    })
		})

		raLog.notifier.$ncTogglerA.on('mouseenter mouseleave',function(){
		    $(this).find('i').toggleClass(
			  raLog.notifier.$ncContent.is(":visible") ?
			  'icon-chevron-up' : 'icon-chevron-down')
		})

		raLog.notifier.$nc.ajaxError(function(e, jqxhr, settings, exception) {

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

		    raLog.notifier.alert('error',exception.toString(),error);


		    //console.error(exception);
		    console.groupCollapsed("Exception");
		    console.log('settings:',settings);
		    console.log('event:',e);
		    console.log('jqxhr:',jqxhr);
		    console.groupEnd();
		})
	  },

	  alert: function(type,heading,content) {
		var $alert = $('<div>').addClass('alert alert-'+type);
		$alert.append($('<a class="close" data-dismiss="alert" href="#">&times;</a>'));
		$alert.append($('<h4>').addClass("alert-heading").text(heading));
		$.each(content,function(i,o){
		    $alert.append(o);
		})
		$alert.prependTo(raLog.notifier.$ncContent);
		raLog.notifier.$ncContent.slideDown(150);
		$('#notification-center .toggler a i').removeClass('icon-white');
		//if ($('#notification-center .content:hidden').length)
		//$('#notification-center').animate({height:'+=10px'},100,'swing',function(){$(this).animate({height:'-=10px'},100)})
		return $alert;
	  }

    },


    importer : {

	  filesQueue: [],
	  filesProcessed: [],
	  filesCount: [],
	  cancel: false,


	  start: function(){
		$.get('import/getfiles',null,function(response){
		    console.log("load files",response);
		    raLog.importer.filesQueue = response.files;
		    raLog.importer.filesCount = response.files.length;

		    if (raLog.importer.filesCount > 0) {
			  raLog.importer.$alert = raLog.notifier.alert('info', 'Dataload', [
				$('<p>').text('Loading new files. '),
				$('<button>').addClass('btn btn-mini btn-cancel pull-right').text('Cancel').click(function(){
				    this.disabled = true;
				    raLog.importer.cancel = true;
				}),
				$('<div>').addClass('progress').append(
				    $('<div>').addClass('bar')),
				]);

			  raLog.importer.loadNext();
		    }
		})
	  },

	  loadNext: function(){
		if (raLog.importer.cancel === true) {
		    console.log("loadNext() CANCELED")
		    raLog.importer.$alert
		    .removeClass('alert-info')
		    .addClass('alert-danger');
		    raLog.importer.$alert.find('.progress')
		    .addClass('progress-danger');
		    raLog.importer.cancel = false;
		    return;
		}

		if (raLog.importer.filesQueue.length == 0) {
		    throw new Error("No files in queue!")
		}

		var currentFile = raLog.importer.filesQueue[0];

		raLog.importer.filesProcessed.push(raLog.importer.filesQueue.shift());
		//console.log("loadNext() --> loading:",currentFile);

		$.post('import/file',{
		    file : currentFile
		},function(response){
		    //console.log("loadNext().fn --> response:",response);
		    var progress = 100 - raLog.importer.filesQueue.length/raLog.importer.filesCount*100;
		    raLog.importer.$alert.find('.bar').css('width',progress+'%');
		    if (raLog.importer.filesQueue.length > 0) {
			  raLog.importer.loadNext();
		    }
		    else {
			  raLog.importer.$alert
			  .removeClass('alert-info')
			  .addClass('alert-success');

			  raLog.importer.$alert.find('.progress').addClass('progress-success');
			  raLog.importer.$alert.find('.btn-cancel').attr('disabled',true);

			  setTimeout("raLog.importer.$alert.slideUp(750)",5000);
		    }
		})
	  }

    },

    chart : {

	  charts: [],

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

	  }
    }
};

//$(document).ready(raLog.init);
//console.log(raLog);
