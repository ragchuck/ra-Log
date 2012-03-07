//steal/js ra_log/ra_log/scripts/compress.js

load("steal/rhino/rhino.js");
steal('steal/clean',function(){
	steal.clean('ra_log.html',{
		indent_size: 1,
		indent_char: '\t',
		jslint : true,
		ignore: /jquery\/jquery.js/,
		predefined: {
			steal: true,
			jQuery: true,
			$ : true,
			window : true
			}
	});
});
