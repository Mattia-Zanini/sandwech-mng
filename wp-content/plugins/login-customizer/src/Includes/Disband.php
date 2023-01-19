<?php

/**
 * Plugin deactivation box
 *  
 * @package 		LoginCustomizer\Includes
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 */
namespace LoginCustomizer\Includes;

class Disband{

	function __construct() {
				
		$deactivate_nonce = wp_create_nonce( 'login-customizer-deactivate-nonce' ); ?>

		<style>
			.login-customizer-hidden{
				overflow: hidden;
			}
			.login-customizer-popup-overlay .login-customizer-internal-message{
				margin: 3px 0 3px 22px;
				display: none;
			}
			.login-customizer-reason-input{
				margin: 3px 0 3px 22px;
				display: none;
			}
			.login-customizer-reason-input input[type="text"]{
				width: 100%;
				display: block;
			}
			.login-customizer-popup-overlay{
				background: rgba(0,0,0, .8);
				position: fixed;
				top:0;
				left: 0;
				height: 100%;
				width: 100%;
				z-index: 1000;
				overflow: auto;
				visibility: hidden;
				opacity: 0;
				transition: opacity 0.3s ease-in-out;
				display: flex;
				justify-content: center;
				align-items: center;
			}
			.login-customizer-popup-overlay.login-customizer-active{
				opacity: 1;
				visibility: visible;
			}
			.login-customizer-serveypanel{
				width: 600px;
				background: #fff;
				margin: 0 auto 0;
				border-radius: 3px;
			}
			.login-customizer-popup-header{
				background: #f1f1f1;
				padding: 20px;
				border-bottom: 1px solid #ccc;
			}
			.login-customizer-popup-header h2{
				margin: 0;
				text-transform: uppercase;
			}
			.login-customizer-popup-body{
				padding: 10px 20px;
			}
			.login-customizer-popup-footer{
				background: #f9f3f3;
				padding: 10px 20px;
				border-top: 1px solid #ccc;
			}
			.login-customizer-popup-footer:after{
				content:"";
				display: table;
				clear: both;
			}
			.action-btns{
				float: right;
			}
			.login-customizer-anonymous{

				display: none;
			}
			.attention, .error-message {
				color: red;
				font-weight: 600;
				display: none;
			}
			.login-customizer-spinner{
				display: none;
			}
			.login-customizer-spinner img{
				margin-top: 3px;
			}
			.login-customizer-pro-message{
				padding-left: 24px;
				color: red;
				font-weight: 600;
				display: none;
			}
			.login-customizer-popup-header{
				background: none;
				padding: 18px 15px;
				-webkit-box-shadow: 0 0 8px rgba(0,0,0,.1);
				box-shadow: 0 0 8px rgba(0,0,0,.1);
				border: 0;
			}
			.login-customizer-popup-body h3{
				margin-top: 0;
				margin-bottom: 30px;
				font-weight: 700;
				font-size: 15px;
				color: #495157;
				line-height: 1.4;
				text-tranform: uppercase;
			}
			.login-customizer-reason{
				font-size: 13px;
				color: #6d7882;
				margin-bottom: 15px;
			}
			.login-customizer-reason input[type="radio"]{
				margin-right: 15px;
			}
			.login-customizer-popup-body{
				padding: 30px 30px 0;
			}
			.login-customizer-popup-footer{
				background: none;
				border: 0;
				padding: 29px 39px 39px;
			}
		</style>
		<div class="login-customizer-popup-overlay">
		<div class="login-customizer-serveypanel">
			<form action="#" method="post" id="login-customizer-deactivate-form">
			<div class="login-customizer-popup-header">
			<h2><?php _e( 'Quick feedback about Login Customizer', 'login-customizer' ); ?></h2>
			</div>
			<div class="login-customizer-popup-body">
			<h3><?php _e( 'If you have a moment, please let us know why you are deactivating:', 'login-customizer' ); ?></h3>
			<input type="hidden" class="login-customizer_deactivate_nonce" name="login-customizer_deactivate_nonce" value="<?php echo $deactivate_nonce; ?>">
			<ul id="login-customizer-reason-list">
				<li class="login-customizer-reason login-customizer-reason-pro" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="pro">
					</span>
					<span><?php _e( "I upgraded to Login Customizer Pro", 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-pro-message"><?php _e( 'No need to deactivate this Login Customizer Core version. Pro version works as an add-on with Core version.', 'login-customizer' ); ?></div>
				</li>
				<li class="login-customizer-reason" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="1">
					</span>
					<span><?php _e( 'I only needed the plugin for a short period', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				</li>
				<li class="login-customizer-reason has-input" data-input-type="textfield">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="2">
					</span>
					<span><?php _e( 'I found a better plugin', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				<div class="login-customizer-reason-input"><span class="message error-message "><?php _e( 'Kindly tell us the Plugin name.', 'login-customizer' ); ?></span><input type="text" name="better_plugin" placeholder="What's the plugin's name?"></div>
				</li>
				<li class="login-customizer-reason" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="3">
					</span>
					<span><?php _e( 'The plugin broke my site', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				</li>
				<li class="login-customizer-reason" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="4">
					</span>
					<span><?php _e( 'The plugin suddenly stopped working', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				</li>
				<li class="login-customizer-reason" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="5">
					</span>
					<span><?php _e( 'I no longer need the plugin', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				</li>
				<li class="login-customizer-reason" data-input-type="" data-input-placeholder="">
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="6">
					</span>
					<span><?php _e( "It's a temporary deactivation. I'm just debugging an issue.", 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				</li>
				<li class="login-customizer-reason has-input" data-input-type="textfield" >
				<label>
					<span>
					<input type="radio" name="login-customizer-selected-reason" value="7">
					</span>
					<span><?php _e( 'Other', 'login-customizer' ); ?></span>
				</label>
				<div class="login-customizer-internal-message"></div>
				<div class="login-customizer-reason-input"><span class="message error-message "><?php _e( 'Kindly tell us the reason so we can improve.', 'login-customizer' ); ?></span><input type="text" name="other_reason" placeholder="Kindly tell us the reason so we can improve."></div>
				</li>
			</ul>
			</div>
			<div class="login-customizer-popup-footer">
			<label class="login-customizer-anonymous"><input type="checkbox" /><?php _e( 'Anonymous feedback', 'login-customizer' ); ?></label>
				<input type="button" class="button button-secondary button-skip login-customizer-popup-skip-feedback" value="<?php _e( 'Skip & Deactivate', 'login-customizer'); ?>" >
			<div class="action-btns">
				<span class="login-customizer-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
				<input type="submit" class="button button-secondary button-deactivate login-customizer-popup-allow-deactivate" value="<?php _e( 'Submit & Deactivate', 'login-customizer'); ?>" disabled="disabled">
				<a href="#" class="button button-primary login-customizer-popup-button-close"><?php _e( 'Cancel', 'login-customizer' ); ?></a>

			</div>
			</div>
		</form>
			</div>
		</div>


		<script>
			(function( $ ) {

				$(function() {

					var pluginSlug = 'login-customizer';
					// Code to fire when the DOM is ready.

					$(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .deactivate', function(e){
					e.preventDefault();
					$('.login-customizer-popup-overlay').addClass('login-customizer-active');
					$('body').addClass('login-customizer-hidden');
					});
					$(document).on('click', '.login-customizer-popup-button-close', function () {
					close_popup();
					});
					$(document).on('click', ".login-customizer-serveypanel,tr[data-slug='" + pluginSlug + "'] .deactivate",function(e){
						e.stopPropagation();
					});

					$(document).click(function(){
					close_popup();
					});
					$('.login-customizer-reason label').on('click', function(){
					if($(this).find('input[type="radio"]').is(':checked')){
						//$('.login-customizer-anonymous').show();
						$(this).next().next('.login-customizer-reason-input').show().end().end().parent().siblings().find('.login-customizer-reason-input').hide();
					}
					});
					$('input[type="radio"][name="login-customizer-selected-reason"]').on('click', function(event) {
					$(".login-customizer-popup-allow-deactivate").removeAttr('disabled');
					$(".login-customizer-popup-skip-feedback").removeAttr('disabled');
					$('.message.error-message').hide();
					$('.login-customizer-pro-message').hide();
					});

					$('.login-customizer-reason-pro label').on('click', function(){
					if($(this).find('input[type="radio"]').is(':checked')){
						$(this).next('.login-customizer-pro-message').show().end().end().parent().siblings().find('.login-customizer-reason-input').hide();
						$(this).next('.login-customizer-pro-message').show()
						$('.login-customizer-popup-allow-deactivate').attr('disabled', 'disabled');
						$('.login-customizer-popup-skip-feedback').attr('disabled', 'disabled');
					}
					});
					$(document).on('submit', '#login-customizer-deactivate-form', function(event) {

						event.preventDefault();

						var _reason =  $('input[type="radio"][name="login-customizer-selected-reason"]:checked').val();
						var _reason_details = '';
						var deactivate_nonce = $('.login-customizer_deactivate_nonce').val();

						if ( _reason == 2 ) {
							_reason_details = $("input[type='text'][name='better_plugin']").val();
						} else if ( _reason == 7 ) {
							_reason_details = $("input[type='text'][name='other_reason']").val();
						}

						if ( ( _reason == 7 || _reason == 2 ) && _reason_details == '' ) {
							$('.message.error-message').show();
							return ;
						}
						
						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
							action        : 'login_customizer_deactivate',
							reason        : _reason,
							reason_detail : _reason_details,
							security      : deactivate_nonce
							},
							beforeSend: function(){
							$(".login-customizer-spinner").show();
							$(".login-customizer-popup-allow-deactivate").attr("disabled", "disabled");
							}
						})
						.done(function() {
							$(".login-customizer-spinner").hide();
							$(".login-customizer-popup-allow-deactivate").removeAttr("disabled");
							window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
						});

					});

					$('.login-customizer-popup-skip-feedback').on('click', function(e){
						// e.preventDefault();
						window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
					})

					function close_popup() {
						$('.login-customizer-popup-overlay').removeClass('login-customizer-active');
						$('#login-customizer-deactivate-form').trigger("reset");
						$(".login-customizer-popup-allow-deactivate").attr('disabled', 'disabled');
						$(".login-customizer-reason-input").hide();
						$('body').removeClass('login-customizer-hidden');
						$('.message.error-message').hide();
						$('.login-customizer-pro-message').hide();
					}
				});

			})( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
		</script> 
		<?php
	}
}