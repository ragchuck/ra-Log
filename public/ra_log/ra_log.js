steal(// jQuery
	'jquery',
	// Bootstrap, from Twitter
	'bootstrap/bootstrap.css',
	'bootstrap/bootstrap-responsive.css',
	'bootstrap/bootstrap.min.js',
	// plugins
	'mustache', // Templating
      'bootbox',  // Bootbox - Twitter Bootstrap powered alert, confirm and flexible dialog boxes
	// ra_log assets
	'ra_log/ra_log.css',
      'ra_log/nav',
      'ra_log/notify',
      'ra_log/import',
      'ra_log/charts',

	function ($) {

		"use strict";


            $(document).navigation();
            $(document).notify();
            $(document).importr();

		// set up the Charts
            $('.chart-tabs').charts();

	}
);
