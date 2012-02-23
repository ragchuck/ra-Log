{extends 'layouts/desktop.tpl'}
{block name='content'}
<div class="tabbable tabs-right">
    <ul class="nav nav-tabs">
	  {foreach $chart_types as $ct}
		{if $ct eq $chart_type}
		    {$class="active"}
		    {$loaded="true"}
		{else}
		    {$class=""}
		    {$loaded="false"}
		{/if}
		<li class="{$class}">
		    <a href="#/chart/{$ct}" data-target="#{$ct}" data-loaded="{$loaded}">{$ct|ucfirst|__}</a>
		</li>
	  {/foreach}
    </ul>
    <div class="tab-content">
	  {foreach $chart_types as $ct}
		{if $ct eq $chart_type}
		    {$class="tab-pane active"}
		{else}
		    {$class="tab-pane"}
		{/if}
		<div class="{$class}" id="{$ct}">
		    {if $ct eq $chart_type}
			  {$chart}
		    {/if}
		</div>
	  {/foreach}
    </div>
</div>
{/block}