require.config({
    paths: {
    	"jquery": "libs/jquery-1.7.1.min",
    	"mustache" : "libs/mustache",
    	"history" : "libs/history",
        "views": "../views",
        "models": "../models"    
    }
});

require(["order!jquery","text!views/list.html","order!history","mustache","js/plugins.js"],
    function($,html) {
    	
    	var history = window.History;
    	
    	
    	$('.loader_btn').click(function(e){
    		e.preventDefault();
    		var url = $(this).attr('href');
    		history.pushState(null,"",url);

    	});
    	
    	history.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        	var state = history.getState(); // Note: We are using History.getState() instead of event.state
        	redirect(state.url);
    	});
    	
    	
    	function redirect(url) {
	    	$('#results').slideUp(function(){
		    		$.getJSON(url).then(function(data){
		    			var template = Mustache.render(html,data);
		    			$('#results').html(template).slideDown();
		    		});
	    	});
    	}
    	
    }
    
    
);





