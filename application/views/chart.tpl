<figure>
    {*<figcaption>{$caption}</figcaption>-->*}
    <div id="{$container_id}"></div>
</figure>
{* Pager *}
{if $chart_type neq 'total'}
    <ul class="pager">
	  <li class="previous">
		<a href="#"></a>
	  </li>
	  <li class="next">
		<a href="#"></a>
	  </li>
    </ul>
{/if}
{* Pager (end) *}
<script type="text/javascript">
{include $chart_js}
</script>