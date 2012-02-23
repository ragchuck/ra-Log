<figure>
    <!--<figcaption>{$caption}</figcaption>-->
    <div id="{$container_id}"></div>
</figure>
{* Pager *}
{if $chart_type neq 'total'}
    <ul class="pager">
	  {$prev=strtotime("-1 $chart_type",$time)}
	  <li class="previous">
		<a href="./index/{$prev|date_format:"%Y/%m/%d"}">« {$prev|date_format}</a>
	  </li>
	  {$next=strtotime("+1 $chart_type",$time)}
	  <li class="next">
		<a href="./index/{$next|date_format:"%Y/%m/%d"}">{$next|date_format} »</a>
	  </li>
    </ul>
{/if}
{* Pager (end) *}
<script type="text/javascript">
{include 'chart/day.js'}
</script>