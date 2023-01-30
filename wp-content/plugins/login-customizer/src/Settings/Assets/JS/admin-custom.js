(function($) {
	'use strict';
	  $( function() {

		$('.logincust-log-file').on('click', function(event) {

			event.preventDefault();

			$.ajax({

				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'logincust_help',
					security: logincust_js.log_nonce,

				},
				beforeSend: function() {
					$(".log-file-sniper").show();
				},
				success: function(response) {
					$(".log-file-sniper").hide();
					$(".log-file-text").show();

					if (!window.navigator.msSaveOrOpenBlob) { // If msSaveOrOpenBlob() is supported, then so is msSaveBlob().
						$("<a />", {
							"download": "login-customizer-log.txt",
							"href": "data:text/plain;charset=utf-8," +
							encodeURIComponent(response),
						}).appendTo("body")
						.click(function() {
							$(this).remove()
						})[0].click()
					} else {
						var blobObject = new Blob([response]);
						window.navigator.msSaveBlob( blobObject, 'login-customizer-log.txt' );
					}
					setTimeout(function() {
						$(".log-file-text").fadeOut()
					}, 3000 );
				}
			});
		});
	});
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.