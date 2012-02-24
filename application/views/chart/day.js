(function() {
    var day = new Highcharts.Chart({
	  chart: {
		renderTo: '{$container_id}',
		events: {
                load: raLog.chart.load
            }
	  },
	  title: {
		text: null
	  },
	  subtitle: {
		text: null
	  },
	  series: [],
	  xAxis: {
		type: 'datetime'
	  },
	  yAxis: {
		min: 0,
		title : {
		    text : null
		}
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
	  }
    });
    //console.log(day);
    raLog.chart.charts['day'] = day;
})();