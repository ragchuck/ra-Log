(function() {
    var _subtittle_text = '{$time|date_format}'
    , _container_id = '{$container_id}'
    , _chart_name = '{$chart_name}'
    , _chart_type = '{$chart_type}'
    , _series = $.parseJSON('{$series|@json_encode}');
    if(raLog.chart.charts[_chart_type] != undefined) { // chart already exists
	  $.each(_series,function(i,o){
		raLog.chart.charts[_chart_type].series[i].setData(o,true);
	  });
    }
    else {
	  raLog.chart.charts[_chart_type] = new Highcharts.Chart({
		"chart": {
		    "renderTo": _container_id
		},
		"title": {
		    "text": _chart_name
		},
		"subtitle": {
		    "text": _subtittle_text
		},
		"series": _series,
		"xAxis": {
		    "type": 'datetime'
		},
		"yAxis": {
		    "min": 0,
		    "title" : {
			  "text" : ""
		    }
		},
		"tooltip": {
		    "shared": true,
		    "crosshairs": true,
		    "formatter": function() {
			  var s = '<b>'+ Highcharts.dateFormat('%H:%M', this.x) +'</b>';
			  $.each(this.points, function(i, point) {
				s += '<br/>'+ point.series.name +': '+ point.y + '';
			  });
			  return s;
		    }
		}
	  });
    }
})();