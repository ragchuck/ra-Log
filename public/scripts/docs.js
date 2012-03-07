//js ra_log/ra_log/scripts/doc.js

load('steal/rhino/rhino.js');
steal("documentjs").then(function(){
	DocumentJS('ra_log.html', {
		markdown : ['ra_log']
	});
});