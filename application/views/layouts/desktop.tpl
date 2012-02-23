<!DOCTYPE HTML>
<html>
    <head>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	  <title>ra|Log</title>

	  <base href="{$base_url}" />

	  <meta name="viewport" content="width=device-width, initial-scale=1.0">

	  <!--[if lt IE 9]>
	  <script src="js/html5.js"></script>
	  <![endif]-->

	  <link rel="stylesheet" href="css/bootstrap.css" />
	  <link rel="stylesheet" href="css/bootstrap-responsive.css" />
	  <link rel="stylesheet" href="css/ra_log.css" />

	  <script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>
	  <script type="text/javascript" src="js/jquery/jquery.address.js"></script>

	  <script type="text/javascript" src="js/bootstrap.min.js"></script>

	  <!--<script type="text/javascript" src="js/highcharts/highcharts.js"></script>-->
	  <script type="text/javascript" src="js/highcharts/highcharts.src.js"></script>
	  <!--<script type="text/javascript" src="http://highcharts.com/js/testing.js"></script>-->

	  <!--<script type="text/javascript" src="js/highstock/highstock.js"></script>-->

	  <script type="text/javascript" src="js/ra_log.js"></script>

    </head>

    <body>
	  <nav class="navbar navbar-fixed-top">
		<div class="navbar-inner">
		    <div class="container">
			  <a class="brand" href="./">
				<span id="ra">ra</span>|Log
			  </a>

			  <ul class="nav">
				{*{$nav=array('./dashboard/' => __("Dashboard"),'./profile/' => __("System profile"))}*}
				{*{foreach $nav as $l}*}
				<li class="active">
				    <a href="./dashboard/">{__ t="Dashboard"}</a>
				</li>
				{*{/foreach}*}
				<li>
				    <a href="./profile/">{__ t="System profile"}</a>
				</li>
			  </ul>
			  <ul class="nav pull-right">
				<li class="dropdown">
				    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					  {__ t="Config"}
					  <b class="caret"></b>
				    </a>
				    <ul class="dropdown-menu">
					  <li>
						<a href="">{__ t="Login"}</a>
					  </li>
				    </ul>
				</li>
			  </ul>
			  <form class="navbar-search pull-right">
				<input type="text"
					 class="search-query"
					 placeholder="{__ t="Direct"}">
			  </form>
		    </div>
		</div>
	  </nav>
        <a name="top"></a>

	  <div class="container">

		<div class="page-header">
		    <h1>
			  $page_title
			  <small>Dashboard</small>
		    </h1>
		</div>

		<div id="content">
		    {block name="content"}{/block}
		</div>

		<div class="footer">
		    <p class="pull-right">
			  <a href="#top">{__ t="Back to top"}</a>
		    </p>
		    <p>
			  {$helper->stats('%3$d files using %2$.1fMB in %1$.0fms')}
		    </p>
		</div>
	  </div><!-- container end -->

    </body>

</html>
