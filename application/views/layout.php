<!DOCTYPE HTML>
<html>
	<head>

		<title>ra|Log - <?= $title ?></title>

            <base href="<?= Kohana::$base_url ?>" />

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	</head>

	<body>

		<a name="top"></a>

		<? include Kohana::find_file('views', 'partials/nav') ?>

		<div id="page" class="container">

			<div class="page-header">
				<h1><?= $title ?> <small><?= $subtitle ?></small></h1>
			</div>

			<div id="content">
                        <?= $content ?>
			</div>

			<? include Kohana::find_file('views', 'partials/footer') ?>
		</div><!-- container end -->

		<!--<script type="text/javascript" src="steal/steal.js?ra_log"></script>-->


	</body>

</html>
