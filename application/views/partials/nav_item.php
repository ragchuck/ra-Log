<? if(!isset($nav_item['dropdown'])): ?>
<li class="<?= Arr::get($nav_item, 'active', false) ? 'active' : '' ?>">
      <a href="<?= $nav_item['href'] ?>" class="<?= $nav_item['class'] ?>"
         <? foreach(Arr::get($nav_item, 'data', array()) as $field => $value): ?>
            data-<?= $field ?>="<?= $value ?>"
         <? endforeach ?>>
            <? if(isset($nav_item['icon'])): ?>
                  <i class="icon-<?= $nav_item['icon'] ?>"></i>&nbsp;
            <? endif ?>
            <?= $nav_item['text'] ?>
      </a>
</li>
<? else: ?>
<li class="dropdown">
	<a href="<?= $nav_item['href'] ?>" class="dropdown-toggle" data-toggle="dropdown">
		<?= $nav_item['text'] ?>
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
      <? foreach($nav_item['dropdown'] as $nav_item1): ?>
            <?= View::factory('partials/nav_item')->bind('nav_item', $nav_item1) ?>
	<? endforeach ?>
	</ul>
</li>
<? endif ?>