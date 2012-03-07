// load('ra_log/ra_log/scripts/crawl.js')

load('steal/rhino/rhino.js')

steal('steal/html/crawl', function(){
  steal.html.crawl("ra_log.html","out")
});
