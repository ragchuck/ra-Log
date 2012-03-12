steal(// jQuery
	'jquery',
	// Bootstrap, from Twitter
	'bootstrap/bootstrap.css',
	'bootstrap/bootstrap-responsive.css',
	'bootstrap/bootstrap.min.js',
	// ra_log assets
	'ra_log/ra_log.css',
	'ra_log/charts',
      'ra_log/notify',
      'ra_log/import',
      'ra_log/nav.js',
	// plugins
	'mustache',

	function ($) {

		"use strict";

		// Routes;

		$.route.bind('change', function (ev, attr, how, newVal, oldVal) {
			//steal.dev.log('steallog: route changed.',arguments);
		});

            window.lbl = {};

            $(window).navigation();
            $(window).notify();
            $(window).importr();

		// set up the Charts
		$('#chart-tabs').charts();



	}
);
