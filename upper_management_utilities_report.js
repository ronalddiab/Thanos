var fs = require('fs');

function openpage(){

	var webPage = require('webpage');
	var page = webPage.create();

	page.viewportSize = {width: 1903, height: 521};
	page.settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0';
	//console.log("Start "+ Date());
	page.open( 'https://www.heportal.net/Thanos/reportscron/upper_management_utilities_report', function( status ) {
        //var script2 = "function(){ console.log(window.sites_chart_cost_img); }";
        //page.evaluateJavaScript(script2);
		// Render the page to this file
		if ( status === "success" ) {
	       	//console.log("END "+ Date());
			page.render('rgraph.png');
	    }else{
	        
	    }
	}); 

	page.onResourceReceived = function(response) {
		// console.log("Response Received : "+ JSON.stringify(response));
	    if (response.stage !== "end") return;		    
	};

	page.onConsoleMessage = function(msg) {
        /*console.log(msg);
		if(msg == 'complete_pdf_cron'){
			console.log("complete_pdf_cron");
	    }*/
	};

	page.onLoadFinished = function(status) { 
	  	// console.log('Status onLoadFinished : ' + status);		  
		//phantom.exit();    
		setTimeout(function(){
			// don't forget to exit
			phantom.exit();
		}, 2000);
	};
}
 
// init request 
openpage();
// console.log('End'); 