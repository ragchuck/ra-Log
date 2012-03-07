steal(// jQuery
      'jquery',
      'jquery/dom/route',
      // Bootstrap, from Twitter
      'bootstrap/bootstrap.css',
	'bootstrap/bootstrap-responsive.css',
	'bootstrap/bootstrap.min.js',
	// ra_log assets
	'ra_log/ra_log.css',
	'ra_log/charts',function($) {


    // Routes;
    $.route( "/:action" );

    $.route.bind('change', function(ev, attr, how, newVal, oldVal) {
	  //steal.dev.log('steallog: route changed.',arguments);
    })

    // set up the Charts
    $('#chart-tabs').charts();



})
