{extends 'layouts/desktop.tpl'}
{block name="page_header"}
    <h1>
	  {__ t="Dashboard"}
	  <small></small>
    </h1>
{/block}
{block name='content'}
<div id="chart-tabs" class="tabbable tabs-right">
    <ul class="nav nav-tabs">
	  {foreach $chart_types as $ct}
		<li>
		    <a href="#!/chart/{$ct}" data-chart="{$ct}" data-target="#tab-{$ct}">{$ct|ucfirst|__}</a>
		</li>
	  {/foreach}
    </ul>
    <div class="tab-content">
	  {foreach $chart_types as $ct}
		<div class="tab-pane" id="tab-{$ct}">
		</div>
	  {/foreach}
    </div>
</div>
{/block}