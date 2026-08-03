var fs = require('fs');

var sites = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150];

var pages = [];

var $i = 0;



function openpage(){

	// Manage pages

	if(sites.length == 0){

		console.log('Bye');

		phantom.exit();

	}else{

		var currentPage = sites[0];

		

		// Open Page

		pages[$i] = require( 'webpage' ).create();

		pages[$i].viewportSize = {width: 1903, height: 521};

		pages[$i].settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0';

		

		pages[$i].open('https://www.heportal.net/Thanos/reportscron?ni='+currentPage+'&type=annual', function( status ) {

			if ( status === "success" ) {

		        console.log( "Start Request #"+currentPage+": " + status );

		    }else{

		        nextRequest(currentPage, pages[$i]);

		    }

		});



		pages[$i].onResourceReceived = function(response) {

		    if (response.stage !== "end") return;

		    if(response.status != 200){

		        nextRequest(currentPage, pages[$i]);

		    }

		};





		pages[$i].onConsoleMessage = function(msg) {

			if(msg == 'complete_pdf_cron'){

		        nextRequest(currentPage, pages[$i]);

		    }

		};

	}

}



function nextRequest(currentPage, page){

	console.log("Complete Request #"+currentPage+": ");

    page.clearMemoryCache();

    page.close();



	$i++;

	sites.shift();

    

    openpage();

}



// init request

openpage();

