<figure>
	<figcaption><?=$caption?></figcaption>
	<div id="<?=$container_id?>"></div>
</figure>
<script type="text/javascript">
(function(){
	Highcharts.Chart({
		chart: {
			renderTo: '<?=$container_id?>'
		},
		title: {
			text: '<?=__($chart_name)?>'
		},
		subtitle: {
			text: Highcharts.dateFormat('%e. %B %Y', <?=doubleval(gmdate('U',$time)*1000)?>)
		},
		xAxis: {
			type: 'datetime'
		},
		yAxis: {
			min: 0
		},
		tooltip: {
			shared: true,
			crosshairs: true,
			formatter: function() {
				var s = '<b>'+ Highcharts.dateFormat('%H:%M', this.x) +'</b>';

				$.each(this.points, function(i, point) {
					s += '<br/>'+ point.series.name +': '+ point.y + '';
				});

				return s;
			}
		},
		series: <?=json_encode($series)?>
	});
})();
</script>