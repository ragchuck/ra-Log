<!DOCTYPE HTML>
<html>
      <head>

            <title>ra|Log - <?= $title ?></title>

            <base href="<?= Kohana::$base_url ?>" />

            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />

            <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
            <link rel="stylesheet" href="assets/css/bootstrap-responsive.min.css" />

            <link rel="stylesheet" href="assets/js/libs/backbone-forms/templates/bootstrap.css" />

            <link rel="stylesheet" href="assets/css/ra_log.css" />
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
                                    <li class="dropdown">
                                          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <?= __("Config") ?>
                                                <b class="caret"></b>
                                          </a>
                                          <ul class="dropdown-menu">
                                                <li class="">
                                                      <a href="#" class="js-login">
                                                            <i class="icon-user"></i>&nbsp;
                                                            <?= __("Login") ?>
                                                      </a>
                                                </li>
                                                <li class="">
                                                      <a href="#" class="js-import-start">
                                                            <i class="icon-retweet"></i>&nbsp;
                                                            <?= __("Refresh Data") ?>
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
                        <p class="pull-right">
                              <a class="js-close" href="#"><?= __("Close") ?></a>
                        </p>
                        <p class="info"></p>
                        <div class="btn-group pull-right">
                              &nbsp;
                              <button class="btn btn-mini btn-continue"><i class="icon-play"></i></button>
                              <button class="btn btn-mini btn-cancel"><i class="icon-stop"></i></button>
                        </div>
                        <div class="progress">
                              <div class="bar"></div>
                        </div>
                  </div>                  
            </div>

            <div id="page" class="container">

                  <div class="page-header">
                        <h1>Dashboard <small>Index</small></h1>
                  </div>

                  <div id="content-container">
                  </div>

                  <div class="footer">
                        <p class="pull-right">
                              <a href="#top"><?= __("Back to top") ?></a>
                        </p>
                        <p>
                              <?= $stats ?>
                        </p>
                  </div>
            </div><!-- container end -->

            <script data-main="assets/js/main" src="assets/js/libs/require/require.js"></script>

      </body>

</html>
