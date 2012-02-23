{extends 'layouts/desktop.tpl'}
{block name='content'}
<div class="tabbable tabs-right">
    <ul class="nav nav-tabs">
	  {foreach $chart_types as $ct}
		<li
		    {if $ct eq $chart_type}
			  class="active"
		    {/if}>
		    <a href="#/chart/{$ct}" data-target="#{$ct}">{$ct|ucfirst|__}</a>
		</li>
	  {/foreach}
    </ul>
    <div class="tab-content">
	  {foreach $chart_types as $ct}
		<div class="tab-pane
		     {if $ct eq $chart_type}
			   active
		     {/if}" id="{$ct}">
		    {if $ct eq $chart_type}
			  {$chart}
		    {/if}
		</div>
	  {/foreach}
    </div>
</div>
{/block}