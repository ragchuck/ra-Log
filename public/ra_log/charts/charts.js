steal('jquery/class',
      'jquery/controller/route',
      'highcharts', function ($) {

            'use strict';

            // Charts Controller
            $.Controller('Charts', {

//                  defaults: {
//                        year: d.getFullYear(),
//                        month: d.getMonth() + 1,
//                        day: d.getDate()
//                  },

                  init : function () {
                        steal.dev.log("Controller Charts.init()");

                        $.route.ready(false);

                        //steal.dev.log($.route.attrs());
                        // no hash - display default page
                        if (!window.location.hash.substr(2).length /* bzw. !$.isEmptyObject($.route.attrs())*/) {
                              // the below code is sometimes failing to trigger the event. Hence changing the hash manually
                              // $.route.attrs({ "nav": "category", "page": "category"}, true);
                              window.location.hash = $.route.url({"type": "day"});
                        }
                        $.route.ready(true);
                  },

                  "/chart/:type route" : function (a) {
                        this.activate(a);
                  },
                  "/chart/:type/:year route" : function (a) {
                        this.activate(a);
                  },
                  "/chart/:type/:year/:month route" : function (a) {
                        this.activate(a);
                  },
                  "/chart/:type/:year/:month/:day route" : function (a) {
                        this.activate(a);
                  },

                  activate: function (a) {
                        steal.dev.log("Controller Charts.activate()", arguments);

                        // first activate the dashboard, if it's not visible
                        $(document).navigation('activate', 'dashboard');

                        var chart,
                              $tab = $('a[data-chart="' + a.type + '"]');

                        $tab.tab('show');

                        if (!Chart.exists(a.type)) {
                              chart = new Chart(a.type);
                        } else {
                              Chart.col[a.type].load();
                        }
                  },

                  ".nav-tabs a[data-chart] show": function (e) {
                        steal.dev.log("Controller Charts.nav-tabs a[data-chart] show()", arguments);
                  }

            });

            $.Class('Chart', {
                  /* @static */

                  // collection of all charts
                  col: [],

                  exists: function (type) {
                        return this.get(type) !== undefined;
                  },

                  get: function (type) {
                        return this.col[type];
                  },

                  set: function (type, chart) {
                        this.col[type] = chart;
                  },

                  hc_options : {
                        title: {
                              text: null
                        },
                        subtitle: {
                              text: null
                        },
                        plotOptions: {
                              area: {
                                    marker: {
                                          enabled: false,
                                          symbol: 'circle',
                                          radius: 2,
                                          states: {
                                                hover: {
                                                      enabled: true
                                                }
                                          }
                                    }
                              }
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
                              //crosshairs: true,
                              formatter: function () {
                                    var s = '<b>' + Highcharts.dateFormat('%H:%M', this.x) + '</b>';
                                    $.each(this.points, function (i, point) {
                                          s += '<br/>' + point.series.name + ': ' + point.y;
                                    });
                                    return s;
                              }
                        }
                  }
            }, {
                  /* @prototype */
                  // initialization, called when an instance is created
                  init: function (type) {
                        steal.dev.log("Class Chart.init()", arguments);

                        if (type === undefined) {
                              throw new Error('Type is undefined.');
                        }

                        if (this.constructor.exists(type)) {
                              throw new Error('Cannot initialize more than one Chart of a type. (' + type + ')');
                        }

                        this.type = type;

                        this.$tab = $('#tab-' + type);

                        this.Class.set(type, this);

                        // Load the chart config
                        //$('#'+type).load('./chartoption/get/'+type,null,this.proxy('build'));
                        this.build();
                        //var container_id = 'chart-' + type;

                        //$.View('//view/partials/chart.mustache', {type: type}, this.build);
                  },

                  build: function (options) {
                        steal.dev.log("Class Chart.build()", arguments);

                        // create a container
                        var container_id = 'chart-' + this.type;
                        this.$container = $('<div>').attr('id', container_id);
                        this.$tab.append(this.$container);
                        this.$container_details = $('<div>').attr('id', container_id + '-details');
                        this.$tab.append(this.$container_details);

                        this.hc_instance = new Highcharts.Chart(
                              $.extend(this.Class.hc_options, {
                                    chart: {
                                          renderTo: container_id,
                                          events: {
                                                load: this.proxy('load')
                                          }
                                    }
                              }, options || {})
                        );

                  },

                  load: function () {
                        steal.dev.log("Class Chart.load()", arguments);

                        // get the current hash
                        var href = window.location.hash.substr(2);

                        if (this.last_href !== href && href.length > 0) {
                              $.get(href + '.json', null, this.proxy('populate'));
                        }

                        this.last_href = href;
                  },

                  populate: function (res) {
                        steal.dev.log("Class Chart.populate()", arguments);

                        var chart = this.Class.col[res.chart.type].hc_instance;

                        // Title
                        chart.setTitle(res.chart.title, res.chart.subtitle);

                        // Add the series to the chart
                        if (chart.series.length > 0) {
                              $.each(res.chart.series, function (i, o) {
                                    chart.series[i].setData(o.data, true, true);
                              });
                        } else {
                              /*$.each(chart.series,function(i,o){
					chart.series[i].remove();
				  })*/
                              $.each(res.chart.series, function (i, o) {
                                    chart.addSeries(o);
                              });
                        }

                        if (res.chart.series.length !== chart.series.length) {
                              throw new Error("Series lenght is different.");
                        }
                        this.$container_details.html('//view/partials/chart_details.mustache', {pager: res.pager});
                  }


            });
      });