// BPS MScan AJAX
// CAUTION: The AJAX post object/url: $.post(bps_mscan_ajax.ajaxurl... is different in BPS Pro.
// Note: BPS free code is a simplified version of BPS Pro code > Here and everywhere else.
jQuery(document).ready( function($) {
	
	// MScan Malware Scanner: Start. MScan Status: 1
	// MScan Stop is handled in mscan-ajax-functions.php by using a PHP file contents check: /bps-backup/master-backups/mscan-stop.txt
	$( "input#bps-mscan-start-button" ).on({ "click": function() { 
	
		var data = {
			action: 'bps_mscan_scan_processing', 
			mscan_nonce: $( "input#_wpnonce" ).val(),
			post_var: 'bps_mscan'
		};

		$.post(bps_mscan_ajax.ajaxurl, data, function(response) {

	 	});	
		console.log( "clicked!" ); 
	},
	"mouseover": function() { 
		console.log( "hovered!" );
	}
	});

	// MScan Malware Scanner: Scan Time Estimate Tool. MScan Status: 5
	// The Scan Estimate button and Form no longer exist on the MScan page. The Form processing and mscan AJAX code still exist.
	$( "input#bps-mscan-time-estimate-button" ).on({ "click": function() { 
	
		var data = {
			action: 'bps_mscan_scan_estimate', 
			mscan_nonce: $( "input#_wpnonce" ).val(),
			post_var: 'bps_mscan_estimate'
		};

		$.post(bps_mscan_ajax.ajaxurl, data, function(response) {

	 	});	
		console.log( "clicked!" ); 
	},
	"mouseover": function() { 
		console.log( "hovered!" );
	}
	});
});