var sites = [1,2,3];

var pages = [];

var $i = 0;



function openpage(){

	if(sites.length == 0){

		console.log('Bye');

		phantom.exit();

	}else{

		var currentPage = sites[0];

		pages[$i] = require( 'webpage' ).create();

		pages[$i].viewportSize = {width: 1903, height: 1400};

		pages[$i].settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0';

		pages[$i].settings.webSecurityEnabled = false;

		pages[$i].onInitialized = function() {
			pages[$i].evaluate(function() {
				if (!Object.assign) {
					Object.assign = function(target) {
						if (target == null) {
							throw new TypeError('Cannot convert undefined or null to object');
						}
						var to = Object(target);
						for (var i = 1; i < arguments.length; i++) {
							var next = arguments[i];
							if (next != null) {
								for (var key in next) {
									if (Object.prototype.hasOwnProperty.call(next, key)) {
										to[key] = next[key];
									}
								}
							}
						}
						return to;
					};
				}
				window.requestAnimationFrame = function(callback) {
					return window.setTimeout(function() { callback(Date.now()); }, 16);
				};
				window.cancelAnimationFrame = function(id) {
					window.clearTimeout(id);
				};
			});
		};

		pages[$i].onError = function(msg) {
			console.log('Page error: ' + msg);
		};

		pages[$i].onResourceError = function(resourceError) {
			console.log('Resource error: ' + resourceError.url + ' ' + resourceError.errorString);
		};

		var openedPage = pages[$i];

		openedPage.open('https://www.heportal.net/Thanos/reportscron?ni='+currentPage+'&type=mytd', function( status ) {

			if ( status === "success" ) {

		        console.log( "Start Request #"+currentPage+": " + status );

		    }else{

		        nextRequest(currentPage, openedPage);

		    }

		    console.log("Site - "+currentPage);

		});

		openedPage.onConsoleMessage = function(msg) {

			if(msg == 'complete_pdf_cron'){

		        nextRequest(currentPage, openedPage);

		    }

		};

	}

}



function nextRequest(currentPage, page){

	if (page.__done) {
		return;
	}
	page.__done = true;

	console.log("Complete Request #"+currentPage+": ");

    page.clearMemoryCache();

    page.close();



	$i++;

	sites.shift();

    openpage();

}



openpage();
