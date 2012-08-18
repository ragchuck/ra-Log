define([
      'underscore',
      'backbone',
      'views/dashboard',
      
      'jquery.dateFormat',
      'backbone.workflow',
      
      ], function(_, Backbone, Dashboard) {
            
            var ImportStateModel = Backbone.Model.extend({
                  
                  defaults: {
                        filesQueue: [],
                        filesProcessed: [],
                        filesCount: 0,
                        last_response: null // stores the last response if paused
                  },
                  
                  workflow: {
                        initial: 'stopped',
                        events: [{
                              name: 'start',
                              from: 'stopped',
                              to: 'running'
                        },{
                              name: 'pause',
                              from: 'running',
                              to: 'paused'
                        },{
                              name: 'continue',
                              from: 'paused',
                              to: 'running'
                        },{
                              name: 'fail',
                              from: 'running',
                              to: 'failed'
                        },{
                              name: 'recover',
                              from: 'failed',
                              to: 'running'
                        },{
                              name: 'succeed',
                              from: 'running',
                              to: 'succeeded'
                        }]
                  },
                  
                  initialize : function() {
                        _.bindAll(this);
                        _.extend(this, new Backbone.Workflow(this, {attrName: 'import_state'}));
                        //this.on('all',function(){console.log(arguments)});
                  },
                  
                  get_progress: function() {                                         
                        var currentFile = response.file,
                            processed = this.get('filesProcessed').length,
                            count = this.get('filesCount'),
                            left = this.get('filesQueue').length;
                         return 100 - left / count * 100;
                  },
                  
                  start: function() {
                        var _this = this;
                        
                        if(this.get('import_state') !== "stopped") {
                              throw "Cannot start importing. current state: " + this.get('import_state');
                        }
                        
                        this.triggerEvent('start');
                        // ? this.reset();
                        this.set('filesProcessed', []);
                        this.set('filesQueue', []);
                        this.set('filesCount', 0);
                        
                        $.getJSON('import/getfiles').success(function (response) {
                              _this.set('filesQueue', response.files);
                              _this.set('filesCount', response.files.length);

                              if (_this.get('filesCount') > 0) {
                                    _this._load_next();
                              }
                        });
                  },
                  
                  pause: function() {
                        this.triggerEvent('pause');
                        
                  },
                  
                  carry_on: function() {
                        this.triggerEvent('carry_on');
                        this._load_next(this.get('last_response'));
                  },
                  
                  stop: function() {
                        this.triggerEvent('stop');
                  },
                  
                  abort: function() {
                        this.triggerEvent('abort');                        
                  },
            
                  recover: function() {
                        this.triggerEvent('recover');
                  },
                  
                  _load_next : function (response) {
                        
                        this.set('last_response', response);
                        
                        // Abort here if the import isn't running
                        if (this.get('import_state') !== "running") return;
                        
                        
                        this.trigger('update_progress', response);
                        /**
                        var dayChart = Dashboard.charts['day'];
                        
                        if (response && response.count && dayChart) {
                              
                              // Create a shortcut for date format
                              var _df =  function(value) {
                                    return $.format.date(value, 'yyyy/MM/dd');
                              }
                              
                              // Create a callback to add the new points from the processed file
                              var _addPoints = function() {
                                          
                                    // Don't add points, which are not 'new' to the chart
                                    // This can be triggered by <code>this._load_next(this.last_response);</code>
                                    if (dayChart.chart.xAxis[0].getExtremes().dataMax > response.data[0][0])
                                          return;
                                    
                                    // Loop throw the response's data points 
                                    // and add them without redrawing
                                    _.each(response.data, function(point) {
                                          
                                          // only add points if it's on the chart's scale...
                                          if (dp == _df (point[0]))
                                                dayChart.chart.series[0].addPoint(point, false);
                                    });
                                    
                                    // Redraw the chart after the points are added
                                    dayChart.chart.redraw();
                              }
                              
                              // Make a date from the first data-point
                              var dp = _df (response.data[0][0]);
                              
                              // Check if the current dayChart corresponds with the loaded points
                              if (_df (dayChart.model.get('date') || 0) != dp) {
                                    // Show the chart
                                    var path = 'day/' + dp;
                                    dayChart.on('chart:load', _addPoints);
                                    Dashboard.show(path);
                              }
                              else { // Current chart is already loaded
                                    _addPoints();
                              }
                        }
                        */

                        if (this.get('filesQueue').length > 0) {                              

                              var filesQueue = this.get('filesQueue'),
                                    currentFile = filesQueue[0],
                                    filesProcessed = this.get('filesProcessed');

                              filesProcessed.push(filesQueue.shift());

                              this.set({
                                    "filesProcessed": filesProcessed,
                                    "filesQueue": filesQueue
                              });

                              $.post('import/file', {
                                    file : currentFile
                              }, this._load_next).error(this.abort);

                        } else {
                              this.triggerEvent('succeed');
                              // Restart the the import in 5 minutes
                              setTimeout(this.model.start, 5*60*1000);                       
                        }
                  }
            });
      
            var ImportView = Backbone.View.extend({
                  el: '#import',
                        
                  events: {
                        'click .btn-start' : function(){this.model.start()},
                        'click .btn-cancel' : function(){this.model.pause()},
                        'click .btn-continue' : function(){this.model.carry_on()},
                        'click .btn-stop' : function(){this.model.stop()}
                        //'click .js-close' : function() {
                        //    this.$el.slideUp(750)
                        //}
                  },
                        
                  initialize : function() {
                        _.bindAll(this);
                        this.model.on('transition:to:running', this.onImportStart);
                        this.model.on('transition:to:paused', this.onImportPause);
                        this.model.on('transition:to:succeeded', this.onImportSuccess);
                        this.model.on('transition:to:failed', this.onImportFail);
                        this.model.on('update_progress', this.onImportProgress)
                  },                  
                  
                  onImportFail : function () {
                        console.log("onImportFail",arguments);
                        this.$el.find('.info').append(' --- IMPORT FAILED!');
                        this.$el.addClass('alert-danger');
                        this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-danger');
                        this.$el.find('.btn-cancel').attr('disabled', true);
                        this.$el.find('.btn-continue').attr('disabled', false);
                  },                  
                  onImportStart: function() {
                        console.log('onImportStart',arguments);
                        this.$el.slideDown(500);
                        this.$el.removeClass('alert-success alert-danger alert-warning').addClass('alert-info');
                        this.$el.find('.progress').removeClass('progress-danger progress-success').addClass('progress-info progress-striped active');
                        this.$el.find('.btn-cancel').attr('disabled', false);
                        this.$el.find('.btn-continue').attr('disabled', true);
                  },
                  onImportPause: function() {
                        console.log('onImportPause',arguments);
                        this.$el.addClass('alert-warning');
                        this.$el.find('.progress').removeClass('progress-info').addClass('progress-warning').removeClass('progress-striped active');
                        this.$el.find('.btn-cancel').attr('disabled', true);
                        this.$el.find('.btn-continue').attr('disabled', false);
                  },
                  onImportSuccess: function() {
                        console.log('onImportSuccess',arguments);
                        var processed = this.model.get('filesProcessed').length,
                              count = this.model.get('filesCount');
                        this.$el.find('.info').text('Import finished. (' + processed + '/' + count + ' files processed)');
                        this.$el.find('.bar').css('width', '100%');
                        this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-success');
                        this.$el.find('.btn-cancel').attr('disabled', true);
                        this.$el.removeClass('alert-info').addClass('alert-success');
                        this.$el.delay(5000).slideUp(500);
                  },
                  onImportProgress: function(response) {
                        console.log('onImportProgress',arguments);                        
                        var currentFile = response.file,
                            processed = this.model.get('filesProcessed').length,
                            count = this.model.get('filesCount'),
                            progress = this.model.get_progress();
                            
                        this.$el.find('.info').text(
                              _.template('Importing file (<%=i%>/<%=len%>): "<%=file%>"',{
                                    'i': processed,
                                    'len': count,
                                    'file': currentFile
                              }));

                        this.$el.find('.bar').css('width', progress + '%');
                  }

            });
            
            var ImportController = function() {
                  
            };
            
            ImportController.prototype.start = function() {
                  
            }
      
            var importState = new ImportStateModel;
            var view = new ImportView({model: importState});
      
            return importState;
      });