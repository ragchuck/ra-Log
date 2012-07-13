<nav class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="./">
				<span id="ra">ra</span>|Log
			</a>

			<ul class="nav">
			<? foreach($nav_items_left as $nav_item): ?>
				<?= View::factory('partials/nav_item')->bind('nav_item', $nav_item) ?>
			<? endforeach ?>
                  </ul>


			<ul class="nav pull-right">
			<? foreach($nav_items_right as $nav_item): ?>
				<?= View::factory('partials/nav_item')->bind('nav_item', $nav_item) ?>
			<? endforeach ?>
			</ul>

			<? if(isset($search)): ?>
			<form class="navbar-search pull-right">
				<input type="text"
					 class="search-query"
					 placeholder="{{__ Direct}}">
			</form>
			<? endif ?>

		</div>
	</div>
</nav>