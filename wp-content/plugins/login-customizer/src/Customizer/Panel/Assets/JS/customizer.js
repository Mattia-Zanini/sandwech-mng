/* global logincust_script */

jQuery( document ).ready( function( $ ) {

	$( '<li class="accordion-section control-section control-section-default control-subsection"><h4 class="accordion-section-title"><a href="https://wordpress.org/support/plugin/login-customizer/reviews/#new-post" target="_blank">Like our plugin? Leave a review here!</a></h4></li><li style="padding: 10px; text-align: center;">Made with ❤ by <a href="https://loginpress.pro/?utm_source=login-customizer-lite&utm_medium=customizer" target="_blank">Hardeep Asrani</a></li>' ).appendTo( '#sub-accordion-panel-logincust_panel' );

	if ( wp.customize( 'login_customizer_options[logincust_bg_image]' )._value === '' ) {
		$( '#customize-control-login_customizer_options-logincust_bg_image_size' ).hide();
		$( '#customize-control-login_customizer_options-logincust_bg_size' ).hide();
		$( '#customize-control-login_customizer_options-logincust_bg_image_repeat' ).hide();
		$( '#customize-control-login_customizer_options-logincust_bg_image_position' ).hide();
	}

	if ( wp.customize( 'login_customizer_options[logincust_bg_image_size]' )._value !== 'custom' ) {
		$( '#customize-control-login_customizer_options-logincust_bg_size' ).hide();
	}

	if (  wp.customize('login_customizer_options[logincust_logo_show]' )._value == 1) {
		$( '#customize-control-login_customizer_options-logincust_logo' ).hide();
		$( '#customize-control-login_customizer_options-logincust_logo_width' ).hide();
		$( '#customize-control-login_customizer_options-logincust_logo_height' ).hide();
		$( '#customize-control-login_customizer_options-logincust_logo_padding' ).hide();
		$( '#customize-control-login_customizer_options-logincust_logo_link' ).hide();
	} else {
		$( '#customize-control-login_customizer_options-logincust_logo' ).show();
		$( '#customize-control-login_customizer_options-logincust_logo_width' ).show();
		$( '#customize-control-login_customizer_options-logincust_logo_height' ).show();
		$( '#customize-control-login_customizer_options-logincust_logo_padding' ).show();
		$( '#customize-control-login_customizer_options-logincust_logo_link' ).show();
	}

	if ( wp.customize( 'login_customizer_options[logincust_button_height_width]' )._value === 'auto' ) {
		$( '#customize-control-login_customizer_options-logincust_button_width_size' ).hide();
		$( '#customize-control-login_customizer_options-logincust_button_height_size' ).hide();
	}

	if ( wp.customize( 'login_customizer_options[logincust_button_border_width]' )._value === '0px' ) {
		$( '#customize-control-login_customizer_options-logincust_button_border' ).hide();
		$( '#customize-control-login_customizer_options-logincust_button_hover_border' ).hide();
	}

	/*
	 * Detect when the Login Customizer panel is expanded (or closed) so we can preview the login form easily.
	*/
	wp.customize.panel( 'logincust_panel', function( section ) {
		section.expanded.bind( function( isExpanding ) {

			// Value of isExpanding will = true if you're entering the section, false if you're leaving it.
			if ( isExpanding ) {

				// Only send the previewer to the login Customizer page, if we're not already on it.
				var current_url = wp.customize.previewer.previewUrl();
					current_url = current_url.includes( logincust_script.page );

				if ( ! current_url ) {
					wp.customize.previewer.send( 'logincust-url-switcher', { expanded: isExpanding } );
				}

			} else {
				// Head back to the home page, if we leave the Login ustomizer panel.
				wp.customize.previewer.send( 'logincust-back-to-home', { home_url: wp.customize.settings.url.home } );
			}
		});
	});

	wp.customize( 'login_customizer_options[logincust_bg_image]', function( setting ) {
		setting.bind( function( value ) {
			if ( value === '' ) {
				$( '#customize-control-login_customizer_options-logincust_bg_image_size' ).hide();
				$( '#customize-control-login_customizer_options-logincust_bg_size' ).hide();
				$( '#customize-control-login_customizer_options-logincust_bg_image_repeat' ).hide();
				$( '#customize-control-login_customizer_options-logincust_bg_image_position' ).hide();
			} else {
				$( '#customize-control-login_customizer_options-logincust_bg_image_size' ).show();
				if ( wp.customize( 'login_customizer_options[logincust_bg_image_size]' )._value === 'custom' ) {
					$( '#customize-control-login_customizer_options-logincust_bg_size' ).show();
				}
				$( '#customize-control-login_customizer_options-logincust_bg_image_repeat' ).show();
				$( '#customize-control-login_customizer_options-logincust_bg_image_position' ).show();
			}
		});
	});

	wp.customize( 'login_customizer_options[logincust_bg_image_size]', function( setting ) {
		setting.bind( function( value ) {
			if ( value === 'custom' ) {
				$( '#customize-control-login_customizer_options-logincust_bg_size' ).show();
			} else {
				$( '#customize-control-login_customizer_options-logincust_bg_size' ).hide();
			}
		});
	});

	wp.customize( 'login_customizer_options[logincust_logo_show]', function( setting ) {
		setting.bind( function( value ) {
			if ( value === true ) {
				$( '#customize-control-login_customizer_options-logincust_logo' ).hide();
				$( '#customize-control-login_customizer_options-logincust_logo_width' ).hide();
				$( '#customize-control-login_customizer_options-logincust_logo_height' ).hide();
				$( '#customize-control-login_customizer_options-logincust_logo_padding' ).hide();
				$( '#customize-control-login_customizer_options-logincust_logo_link' ).hide();
			}	else {
				$( '#customize-control-login_customizer_options-logincust_logo' ).show();
				$( '#customize-control-login_customizer_options-logincust_logo_width' ).show();
				$( '#customize-control-login_customizer_options-logincust_logo_height' ).show();
				$( '#customize-control-login_customizer_options-logincust_logo_padding' ).show();
				$( '#customize-control-login_customizer_options-logincust_logo_link' ).show();
			}
		});
	});

	wp.customize( 'login_customizer_options[logincust_button_height_width]', function( setting ) {
		setting.bind( function( value ) {
			if ( value === 'custom' ) {
				$( '#customize-control-login_customizer_options-logincust_button_width_size' ).show();
				$( '#customize-control-login_customizer_options-logincust_button_height_size' ).show();
			} else {
				$( '#customize-control-login_customizer_options-logincust_button_width_size' ).hide();
				$( '#customize-control-login_customizer_options-logincust_button_height_size' ).hide();
			}
		});
	});

	wp.customize( 'login_customizer_options[logincust_button_border_width]', function( setting ) {
		setting.bind( function( value ) {
			if ( value !== '0px' ) {
				$( '#customize-control-login_customizer_options-logincust_button_border' ).show();
				$( '#customize-control-login_customizer_options-logincust_button_hover_border' ).show();
			} else {
				$( '#customize-control-login_customizer_options-logincust_button_border' ).hide();
				$( '#customize-control-login_customizer_options-logincust_button_hover_border' ).hide();
			}
		});
	});
});

var materialCSS = '/* Custom CSS for Material Template */\n#login form#loginform .input,\n#login form#registerform .input,\n#login form#lostpasswordform .input {\n\tborder-bottom: 1px solid #d2d2d2;\n}\n\n.bar {\n\tposition: relative;\n\tdisplay: block;\n\twidth: 100%;\n}\n\n.bar:before, .bar:after {\n\tcontent: "";\n\theight: 2px; \n\twidth: 0;\n\tbottom: 15px; \n\tposition: absolute;\n\tbackground: #e91e63; \n\ttransition: all 0.2s ease;\n}\n\n.bar:before { left: 50%; }\n\n.bar:after { right: 50%; }\n\ninput:focus ~ .bar:before, input:focus ~ .bar:after { width: 50%; }';

var materialJS = '// Custom JS for Material Template\nfunction insertAfter( newNode, referenceNode ) {\n    referenceNode.parentNode.insertBefore( newNode, referenceNode.nextSibling );\n}\n\nvar inputFields = document.querySelectorAll( ".input" );\n\ninputFields.forEach( ( field ) => {\n\tvar bar = document.createElement("span");\n\t\tbar.setAttribute( "class", "bar" );\n\tinsertAfter( bar, field );\n});';

var allOptions = [
	'login_customizer_options[logincust_bg_color]',
	'login_customizer_options[logincust_bg_image]',
	'login_customizer_options[logincust_bg_image_size]',
	'login_customizer_options[logincust_bg_size]',
	'login_customizer_options[logincust_bg_image_repeat]',
	'login_customizer_options[logincust_bg_image_position_x]',
	'login_customizer_options[logincust_bg_image_position_y]',
	'login_customizer_options[logincust_logo_show]',
	'login_customizer_options[logincust_logo]', // Let's not change the logo for now.
	'login_customizer_options[logincust_logo_width]',
	'login_customizer_options[logincust_logo_height]',
	'login_customizer_options[logincust_logo_padding]',
	'login_customizer_options[logincust_logo_link]',
	'login_customizer_options[logincust_form_bg_image]',
	'login_customizer_options[logincust_form_bg_color]',
	'login_customizer_options[logincust_form_width]',
	'login_customizer_options[logincust_form_height]',
	'login_customizer_options[logincust_form_padding]',
	'login_customizer_options[logincust_form_radius]',
	'login_customizer_options[logincust_form_shadow_spread]',
	'login_customizer_options[logincust_form_shadow]',
	'login_customizer_options[logincust_field_remember_me]',
	'login_customizer_options[logincust_privacy_policy_link]',
	'login_customizer_options[logincust_field_width]',
	'login_customizer_options[logincust_field_font_size]',
	'login_customizer_options[logincust_field_border_width]',
	'login_customizer_options[logincust_field_border_color]',
	'login_customizer_options[logincust_field_radius]',
	'login_customizer_options[logincust_field_box_shadow]',
	'login_customizer_options[logincust_field_margin]',
	'login_customizer_options[logincust_field_padding]',
	'login_customizer_options[logincust_field_bg]',
	'login_customizer_options[logincust_field_color]',
	'login_customizer_options[logincust_field_label]',
	'login_customizer_options[logincust_field_label_font_size]',
	'login_customizer_options[logincust_button_bg]',
	'login_customizer_options[logincust_button_hover_bg]',
	'login_customizer_options[logincust_button_height_width]',
	'login_customizer_options[logincust_button_width_size]',
	'login_customizer_options[logincust_button_height_size]',
	'login_customizer_options[logincust_button_font_size]',
	'login_customizer_options[logincust_button_color]',
	'login_customizer_options[logincust_button_padding]',
	'login_customizer_options[logincust_button_border_width]',
	'login_customizer_options[logincust_button_border_radius]',
	'login_customizer_options[logincust_button_border]',
	'login_customizer_options[logincust_button_hover_border]',
	'login_customizer_options[logincust_button_shadow_spread]',
	'login_customizer_options[logincust_button_shadow]',
	'login_customizer_options[logincust_button_text_shadow]',
	'login_customizer_options[logincust_other_font_size]',
	'login_customizer_options[logincust_other_color]',
	'login_customizer_options[logincust_other_color_hover]',
	'login_customizer_options[logincust_other_css]',
	'login_customizer_options[logincust_other_js]',
];

var allValues = [
	'#F1F1F1',
	'',
	'auto',
	'',
	'no-repeat',
	'left',
	'top',
	false,
	'',	// Let's leave logo url empty as well
	'84px',
	'84px',
	'5px',
	'https://wordpress.org/',
	'',
	'#FFFFFF',
	'320px',
	'194px',
	'26px 24px 46px 25px',
	'0px',
	'3px',
	'rgba(0,0,0, 0.13)',
	false,
	false,
	'100%',
	'24px',
	'1px',
	'#DDD',
	'0px',
	false,
	'2px 6px 16px 0px',
	'3px 3px 3px 3px',
	'#FFF',
	'#333',
	'#777',
	'14px',
	'#2EA2CC',
	'#1E8CBE',
	'auto',
	'63px',
	'32px',
	'13px',
	'#FFF',
	'0px 12px 2px 12px',
	'1px',
	'3px',
	'#0074A2',
	'#0074A2',
	'0px',
	'#78C8E6',
	'#006799',
	'13px',
	'#999',
	'#2EA2CC',
	'/* Your custom CSS goes here */',
	'// Your custom JS goes here',
];

function resetOptions( options, values ) {
	options.forEach( function( option, i ) {
		wp.customize( option, function( setting ) {
			setting.set( values[i] );

            // hack to reset color.
            if( option.indexOf( 'color' ) !== -1 ){
                var li = 'customize-control-' + option.replace( /\[/g, '-' ).replace( /\]/g, '' );
                jQuery( '#' + li + ' button.wp-color-result' ).css( 'background-color', values[i] );
            }
		});
	});
}

function changeValue( option, value ) {
	wp.customize( option, function( setting ) {
		setting.set( value );
	});
}

wp.customize( 'login_customizer_options[logincust_templates_control]', function( setting ) {
	setting.bind( function( value ) {
		if ( value === 'original' ) {
			resetOptions( allOptions, allValues );
		} else if ( value === 'dark' ) {
			resetOptions( allOptions, allValues );
			changeValue( 'login_customizer_options[logincust_bg_color]', '#000' );
			changeValue( 'login_customizer_options[logincust_form_bg_color]', '#000' );
			changeValue( 'login_customizer_options[logincust_form_shadow_spread]', '0px' );
			changeValue( 'login_customizer_options[logincust_form_shadow]', 'rgba(0,0,0,0.0)' );
			changeValue( 'login_customizer_options[logincust_field_bg]', '#222' );
			changeValue( 'login_customizer_options[logincust_field_radius]', '5px' );
			changeValue( 'login_customizer_options[logincust_field_border_color]', '#2d2d2d' );
			changeValue( 'login_customizer_options[logincust_field_color]', '#777' );
			changeValue( 'login_customizer_options[logincust_field_padding]', '3px 10px 3px 10px' );
			changeValue( 'login_customizer_options[logincust_button_bg]', '#fff' );
			changeValue( 'login_customizer_options[logincust_button_hover_bg]', '#e2e2e2' );
			changeValue( 'login_customizer_options[logincust_button_color]', '#000' );
			changeValue( 'login_customizer_options[logincust_button_border_width]', '0px' );
			changeValue( 'login_customizer_options[logincust_button_shadow]', 'rgba(0,146,232,0.01)' );
			changeValue( 'login_customizer_options[logincust_button_text_shadow]', '#fff' );
			changeValue( 'login_customizer_options[logincust_other_color]', '#777' );
			changeValue( 'login_customizer_options[logincust_other_color_hover]', '#515151' );
		} else if ( value === 'material' ) {
			resetOptions( allOptions, allValues );
			changeValue( 'login_customizer_options[logincust_bg_color]', '#006eb7' );
			changeValue( 'login_customizer_options[logincust_logo]', logincust_script.url + 'Customizer/Templates/Material/assets/logo.png' );
			changeValue( 'login_customizer_options[logincust_form_radius]', '10px' );
			changeValue( 'login_customizer_options[logincust_form_shadow_spread]', '50px' );
			changeValue( 'login_customizer_options[logincust_form_shadow]', 'rgba(2,2,2,0.3)' );
			changeValue( 'login_customizer_options[logincust_field_remember_me]', true );
			changeValue( 'login_customizer_options[logincust_field_border_width]', '0px' );
			changeValue( 'login_customizer_options[logincust_field_box_shadow]', true );
			changeValue( 'login_customizer_options[logincust_button_bg]', '#e91e63' );
			changeValue( 'login_customizer_options[logincust_button_hover_bg]', '#e8004d' );
			changeValue( 'login_customizer_options[logincust_button_height_width]', 'custom' );
			changeValue( 'login_customizer_options[logincust_button_width_size]', '100%' );
			changeValue( 'login_customizer_options[logincust_button_height_size]', '40px' );
			changeValue( 'login_customizer_options[logincust_button_border_width]', '0px' );
			changeValue( 'login_customizer_options[logincust_button_shadow_spread]', '0px' );
			changeValue( 'login_customizer_options[logincust_button_shadow]', 'rgba(120,200,230,0.01)' );
			changeValue( 'login_customizer_options[logincust_button_text_shadow]', '#e91e63' );
			changeValue( 'login_customizer_options[logincust_other_color]', '#ffffff' );
			changeValue( 'login_customizer_options[logincust_other_color_hover]', '#dddddd' );
			changeValue( 'login_customizer_options[logincust_other_css]', materialCSS );
			changeValue( 'login_customizer_options[logincust_other_js]', materialJS );
		}
	});
});