<!DOCTYPE HTML>
<html>
<head>
    <title>ra|Log - <?= $title ?></title>
    <base href="<?= Kohana::$base_url ?>"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="assets/css/bootstrap-responsive.min.css"/>
    <link rel="stylesheet" href="assets/js/libs/backbone-forms/templates/bootstrap.css"/>
    <link rel="stylesheet" href="assets/js/libs/prettify/prettify.css"/>
    <link rel="stylesheet" href="assets/css/ra_log.css"/>

    <script id="tmpl-ajax-error" type="text/template">
        <div class="modal ajax-error">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>

                <h3>ajaxError: <%=title%></h3>
            </div>
            <div class="modal-body">
                <%=message%>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"><%=lbl . close%></a>
            </div>
        </div>
    </script>

</head>
<body>
<a name="top"></a>

<!-- Navigation start -->
<nav class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="./">
                <span id="ra">ra</span>|Log
            </a>

            <ul class="nav">
                <li class="active">
                    <a href="#dashboard">
                        <?= __("Dashboard") ?>
                    </a>
                </li>
                <li class="">
                    <a href="#profile">
                        <?= __("System profile") ?>
                    </a>
                </li>
            </ul>


            <ul class="nav pull-right">
                <li class="dropdown" id="config">
                    <a href="#config" class="dropdown-toggle" data-toggle="dropdown">
                        <?= __("Meta") ?>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="">
                            <a href="#" class="js-login logged-out">
                                <i class="icon-user"></i>&nbsp;
                                <?= __("Login") ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="js-logout logged-in">
                                <i class="icon-user"></i>&nbsp;
                                <?= __("Logout") ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="js-import-start logged-in">
                                <i class="icon-refresh"></i>&nbsp;
                                <?= __("Refresh Data") ?>
                            </a>
                        </li>
                        <li class="logged-in acl-admin">
                            <a href="#config">
                                <i class="icon-cog"></i>&nbsp;
                                <?= __("Configuration") ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>


        </div>
    </div>
</nav>
<!-- Navigation Ende -->

<div id="import">
    <div class="container">
        <!--
                        <p class="pull-right">
                              <a class="js-close" href="#"><?= __("Close") ?></a>
                        </p>
                        -->
        <div class="row">
            <div class="span12">
                <p class="info"></p>
            </div>
        </div>
        <div class="row">
            <div class="span10 progress ">
            </div>
            <div class="span2">
                <button class="btn btn-mini btn-pause"><i class="icon-pause"></i></button>
                <button class="btn btn-mini btn-recover" disabled="disabled"><i class="icon-repeat"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="page" class="container">

    <div class="page-header">
        <h1>Dashboard
            <small>Index</small>
        </h1>
    </div>

    <div id="content-container">
    </div>

    <div class="footer">
        <p class="pull-right">
            <a href="#top"><?= __("Back to top") ?></a>
        </p>
    </div>
    <!-- #footer end -->
</div>
<!-- #page.container end -->

<script data-main="assets/js/main" src="assets/js/libs/require/require.js"></script>

</body>

</html>
