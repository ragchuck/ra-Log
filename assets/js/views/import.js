define([
      'underscore',
      'backbone',
      'views/dashboard',
      
      'jquery.dateFormat'
      
      ], function(_, Backbone, Dashboard) {
      
            var importView = Backbone.View.extend({
                  el: '#import',
                        
                  events: {
                        'click .btn-cancel' : 'cancel',
                        'click .btn-continue' : 'continue_load',
//                        'click .js-close' : function() {
//                              this.$el.slideUp(750)
//                        }
                  },                 
                  
                  filesQueue: [],
                  filesProcessed: [],
                  filesCount: 0,
                  canceled: false,
                        
                  initialize : function() {
                        _.bindAll(this);
                        this.$el.find('.btn-continue').attr('disabled', true);
                  },
                  
                  
                  cancel : function () {
                        console.log("import CANCELED");
                        this.canceled = true;
                        this.$el.addClass('alert-danger');
                        this.$el.find('.progress').removeClass('progress-info').addClass('progress-danger').removeClass('progress-striped active');
                        this.$el.find('.btn-cancel').attr('disabled', true);
                        this.$el.find('.btn-continue').attr('disabled', false);
                  },

                  start : function (continue_load) {
                        console.log("import started");
                        
                        // Reset progress elements
                        this.canceled = false;
                        this.$el.slideDown(500);
                        this.$el.removeClass('alert-success alert-danger').addClass('alert-info');
                        this.$el.find('.progress').removeClass('progress-danger progress-success').addClass('progress-info progress-striped active');
                        this.$el.find('.btn-cancel').attr('disabled', false);
                        this.$el.find('.btn-continue').attr('disabled', true);
                        
                        if (continue_load) {
                              this.load_next(this.last_response);
                        }
                        else {
                              this.filesProcessed = [];
                              $.get('import/getfiles').success(this.start_load).error(function(){
                                    alert('Error during getfiles')
                              });
                        }
                  },
                  
                  continue_load : function () {
                        this.start(true);
                  },                  

                  start_load : function (response) {
                        console.log("Import.start_load()", response);
                        this.filesQueue = response.files;
                        this.filesCount = response.files.length;

                        if (this.filesCount > 0) {
                              this.load_next();
                        }
                  },

                  load_next : function (response) {
                        
                        if (this.canceled === true) {
                              console.log("import stopped");
                              this.last_response = response;
                              return;
                        }
                        
                        var dayChart = Dashboard.charts['day'];
                        
                        if (response && response.count && dayChart) {
                              
                              // Create a shortcut for date format
                              var _df =  function(value) {
                                    return $.format.date(value, 'yyyy/MM/dd');
                              }
                              
                              // Create a callback to add the new points from the processed file
                              var _addPoints = function() {
                                          
                                    // Don't add points, which are not 'new' to the chart
                                    // This can be triggered by <code>this.load_next(this.last_response);</code>
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


                        if (this.filesQueue.length > 0) {

                              var currentFile = this.filesQueue[0],
                              progress = 100 - this.filesQueue.length / this.filesCount * 100;


                        this.$el.find('.info').text(
                              _.template('Importing file (<%=i%>/<%=len%>): "<%=file%>"',{
                                    'i': this.filesProcessed.length,
                                    'len': this.filesCount,
                                    'file': currentFile
                              }));

                              this.filesProcessed.push(this.filesQueue.shift());
                              this.$el.find('.bar').css('width', progress + '%');

                              $.post('import/file', {
                                    file : currentFile
                              }, this.load_next).error(this.load_error);

                        } else {
                              this.$el.find('.info').text('Import finished. (' + this.filesProcessed.length + '/' + this.filesCount + ' files processed)');
                              this.$el.find('.bar').css('width', '100%');
                              this.$el.find('.progress').removeClass('progress-info progress-striped active').addClass('progress-success');
                              this.$el.find('.btn-cancel').attr('disabled', true);
                              this.$el.removeClass('alert-info').addClass('alert-success');
                              this.$el.delay(5000).slideUp(500);
                              setTimeout(this.start, 5*60*1000);
                        }
                  },
                  
                  load_error : function(jXHR) {
                        
                        console.log("import aborted due error");
                        this.$el.find('.info').append(' --- IMPORT ABORTED!');
                        
                        try {
                              var error = JSON.parse(jXHR.responseText);
                              this.$el.find('.info').append(error.message);
                        }
                        catch(e) {console.log(e)};
                        
                        this.cancel();
                        return;
                  }
            });
      
            return new importView;
      });