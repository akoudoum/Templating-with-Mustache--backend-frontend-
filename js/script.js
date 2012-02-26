require.config({
    paths: {
    	"jquery": "libs/jquery-1.7.1.min",
    	"mustache" : "libs/mustache",
        "views": "../views",
        "models": "../models"    
    }
});

require(["jquery","text!views/list.html","mustache","js/plugins.js"],
    function($,html) {
    	$('.loader_btn').click(function(e){
    		e.preventDefault();
    		var url = $(this).attr('href');
    		$('#results').slideUp(function(){
    			history.pushState(null,"",url);
	    		$.getJSON(url).then(function(data){
	    			var template = Mustache.render(html,data);
	    			$('#results').html(template).slideDown();
	    		});
    		});
    		
    	})
    }
);





