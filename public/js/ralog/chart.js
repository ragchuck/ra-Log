raLog.chart = {
    config: {
        highchartsUrl: 'js/highcharts/js/highcharts.js',
        highstockUrl: 'js/highstock/js/highstock.js',
        highchartsOptions: {
            global: {
                useUTC: false
            }
        }
    },
    init: function(){
        if(raLog.chart.initialized)
            return;

        $.getScript(raLog.chart.config.highchartsUrl,function(){
            Highcharts.setOptions(raLog.chart.config.highchartsOptions);
        });


        raLog.chart.initialized=true;
    },
    load: function(type,t){
        raLog.chart.init();
        var d = new Date(),
            p = [];

        if(t!=undefined)
            d = new Date(t);

        if(type==undefined)
            type = 'day';
        /*
        switch(type) {
            case 'day'  :p.unshift(raLog.util.n2s2(d.getDate()));
            case 'month':p.unshift(raLog.util.n2s2(d.getMonth()+1));
            case 'year' :p.unshift(d.getFullYear());break;
            default     :p.unshift(type);
        }
        p.unshift('chart');
        $.getScript(p.join('/'));*/
        $.getScript('chart/day?t='+d.getTime());
    }
};
