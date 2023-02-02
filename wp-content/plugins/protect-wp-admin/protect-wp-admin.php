<?php
/**
Plugin Name: Protect WP-Admin
Plugin URI: https://www.wp-experts.in/
Description: Give extra protection to your site admin and make secure your website against hackers!!
Author: WP Experts Team
Author URI: https://www.wp-experts.in/
Version: 3.8 
*/

/*** WP Experts Team Copyright 2017-2020  (email : raghunath.0087@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
***/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Initialize "Protect WP-Admin" plugin admin menu 
 * @create new menu
 * @create plugin settings page
 */
add_action('admin_menu','init_pwa_admin_menu');
if(!function_exists('init_pwa_admin_menu')):
function init_pwa_admin_menu(){
	add_options_page('Protect WP-Admin','Protect WP-Admin','manage_options','pwa-settings','init_pwa_admin_option_page');
}
endif;
           
/**
* hook to add link under adminmenu bar
*/	
add_action( 'admin_bar_menu', 'toolbar_link_to_pwa', 999 );		 
function toolbar_link_to_pwa( $wp_admin_bar ) {
	$user = wp_get_current_user();
	if (!current_user_can('administrator') && is_admin()) return;
	
	$args = array(
		'id'    => 'pwa_menu_bar',
		'title' => 'Protect WP Admin',
		'href'  => admin_url('options-general.php?page=pwa-settings'),
		'meta'  => array( 'class' => 'pwa-toolbar-page' )
	);
	$wp_admin_bar->add_node( $args );
	//second lavel
	$wp_admin_bar->add_node( array(
		'id'    => 'pwa-second-sub-item',
		'parent' => 'pwa_menu_bar',
		'title' => 'Settings',
		'href'  => admin_url('options-general.php?page=pwa-settings'),
		'meta'  => array(
			'title' => __('Settings'),
			'target' => '_self',
			'class' => 'pwa_menu_item_class'
		),
	));
}
/** Define Action to register "Protect WP-Admin" Options */
add_action('admin_init','init_pwa_options_fields');
/** Register "Protect WP-Admin" options */
if(!function_exists('init_pwa_options_fields')):
function init_pwa_options_fields(){
	register_setting('pwa_setting_options','pwa_active', 'sanitize_text_field' );
	register_setting('pwa_setting_options','pwa_rewrite_text', 'sanitize_text_field' );	
	register_setting('pwa_setting_options','pwa_restrict', 'sanitize_text_field' );	
	register_setting('pwa_setting_options','pwa_logout', 'sanitize_text_field' );
	register_setting('pwa_setting_options','pwa_allow_custom_users', 'sanitize_text_field' );
	register_setting('pwa_setting_options','pwa_logo_path', 'sanitize_url' );
	register_setting('pwa_setting_options','pwa_login_page_bg_color', 'sanitize_text_field' );
	register_setting('pwa_setting_options','pwa_login_page_color', 'sanitize_text_field' );
} 
endif;

/** Add settings link to plugin list page in admin */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'pwa_action_links' );
if(!function_exists('pwa_action_links')):
function pwa_action_links( $links ) {
   $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=pwa-settings') .'">Settings</a> | <a href="http://www.wp-experts.in/products/protect-wp-admin-pro">GO PRO</a>';
   return ($links);
}
endif;
/** Options Form HTML for "Protect WP-Admin" plugin */
if(!function_exists('init_pwa_admin_option_page')):
function init_pwa_admin_option_page(){ 
	        if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}
		if (get_option('permalink_structure') ){ $permalink_structure_val='yes'; }else{$permalink_structure_val='no';}
	?>
	<div style="width: 80%; padding: 10px; margin: 10px;"> 
	<h1>Protect WP-Admin Settings</h1>
  <!-- Start Options Form -->
	<form action="options.php" method="post" id="pwa-settings-form-admin">
	<input type="hidden"  id="check_permalink" value="<?php echo esc_attr($permalink_structure_val);?>">	
	<div id="pwa-tab-menu"><a id="pwa-general" class="pwa-tab-links active" >General</a> <a  id="pwa-admin-style" class="pwa-tab-links">Login Page Style</a><a  id="pwa-support" class="pwa-tab-links">Support & Our other plugin</a> </div>
	<hr>
	<div class="pwa-setting">
		<!-- General Setting -->	
	<div class="first pwa-tab" id="div-pwa-general">
	<h2>General Settings</h2>
	<table cellpadding="10">
	<tr>
	<td valign="top" width="50%">
		
	<p><input type="checkbox" id="pwa_active" name="pwa_active" value='1' <?php if(get_option('pwa_active')!=''){ echo esc_attr(' checked="checked"'); }?>/> <label><strong>Enable</strong></label></p>
	<p id="adminurl"><label><strong>New Admin Slug:</strong></label><br><input  onkeyup="this.value=this.value.replace(/[^a-z]/g,'');"  type="text" id="pwa_rewrite_text" size="20" name="pwa_rewrite_text" value="<?php echo esc_attr(get_option('pwa_rewrite_text')); ?>"  placeholder="myadmin" size="30"><br><i>Don't use any special character.</i></p>
	<?php 
		$getPwaOptions=get_pwa_setting_options();
		if((isset($getPwaOptions['pwa_active']) && '1'==$getPwaOptions['pwa_active']) && (isset($getPwaOptions['pwa_rewrite_text']) && $getPwaOptions['pwa_rewrite_text']!='')){
		echo ('<p><a href="'.site_url($getPwaOptions['pwa_rewrite_text'].'?preview=1').'" target="_blank" style="border: 1px solid #ff0000;text-decoration: none;color: #ff0000;font-size: 18px;vertical-align: middle;padding: 10px 20px;" target="_blank">Preview Of New Admin URL</a></blink></strong></p><em><strong>Note:</strong>Please check new admin url before logout.</em><br>');

		}
	?>
	<hr>
	
	<h2>Advance Settings</h2>

	<p><input type="checkbox" id="pwa_restrict" name="pwa_restrict" value='1' <?php if(get_option('pwa_restrict')!=''){ echo esc_attr(' checked="checked"'); }?>/> <label>Restrict registered non-admin users from wp-admin :</label></p>
	<p><label>Allow access to non-admin users:<br></label><input type="text" id="pwa_allow_custom_users" name="pwa_allow_custom_users" value="<?php echo esc_attr(get_option('pwa_allow_custom_users')); ?>"  placeholder="1,2,3"> <br>(<i>Add comma seprated ids</i>)</p>
	
	</td>
	<td valign="top" style="border-left:2px solid #ccc; padding-left:10px;">
		<div class="offer-announcement" style="display:none;"><h2><i class="wpexperts dashicons-before dashicons-megaphone"></i><a href="https://www.wp-experts.in/products/protect-wp-admin-pro">FLAT 20% DISCOUNT ON PLUGIN ADD-ON</a></h2><em class="tagline">No Coupon Code Required. Hurry! Limited Time Offer!</em></div>
		<h3>Pro Addon Features:</h3>
		<ol class="hand right-click twocolumn">
		<li>Rename wordpress wp-admin URL</li>
		<li>Enable Login Tracker</li>
		<li>Set Number of Login Attempt</li>
		<li>Change username of any existing user</li>
		<li>Define login page logo URL</li>
		<li>Manage login page style from admin</li>
		<li>Define custom redirect url for default wp-admin url</li>
		<li>Change wordpress admin URL</li>
		<li>Track user login history.</li>
		<li>Faster support</li>
		</ol><br>
		<h2><a href="https://www.wp-experts.in/products/protect-wp-admin-pro" target="_blank" style="background: #0472aa; padding: 10px 20px; margin: 10px 0px; text-decoration: none; color: #fff; font-size: 24px; "><strong>Click here to download add-on</strong></a></h2>
		</td>
	</tr>
	</table>

	</div>
	<!-- Admin Style -->
	<div class="last author pwa-tab" id="div-pwa-admin-style">
	<h2>Admin Login Page Style Settings</h2>
	<p id="adminurl"><label>Login Page Logo:</label><br><input type="text" id="pwa_logo_path" name="pwa_logo_path" value="<?php echo esc_attr(get_option('pwa_logo_path')); ?>"  placeholder="Add Custom Logo Image Path" size="30"> <input data-id="pwa_logo_path" type="button" value="Upload Image" class="upload_image"/>(<i>Change WordPress Default Login Logo </i>)</p>
	<p id="adminurl"><label>Background Color: </label><input type="text" id="pwa_login_page_bg_color" name="pwa_login_page_bg_color" value="<?php echo esc_attr(get_option('pwa_login_page_bg_color')); ?>"  size="30" class="color-field"></p>
	<p id="adminurl1"><label>Text Color: </label><input type="text" id="pwa_login_page_color" name="pwa_login_page_color" value="<?php echo esc_attr(get_option('pwa_login_page_color')); ?>"  size="30" class="color-field"></p>
	</div>
	<!-- Support -->
	<div class="last author pwa-tab" id="div-pwa-support">
	<h2>Plugin Support</h2>
	<table>
	<tr>
	<td width="30%"><p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A" target="_blank" style="font-size: 17px; font-weight: bold;"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" title="Donate for this plugin"></a></p>
	
	<p><strong>Plugin Author:</strong><br><a href="http://www.wp-experts.in" target="_blank">WP-Experts.In Team</a></p>
	<p><a href="mailto:raghunath.0087@gmail.com" target="_blank" class="contact-author">Contact Author</a></p>
   </td>
	<td>		
		<p><strong>Our Other Plugins:</strong><br>
	  <ol>
					<li><a href="https://wordpress.org/plugins/custom-share-buttons-with-floating-sidebar" target="_blank">Custom Share Buttons With Floating Sidebar</a></li>
					<li><a href="https://wordpress.org/plugins/seo-manager/" target="_blank">SEO Manager</a></li>
							<li><a href="https://wordpress.org/plugins/protect-wp-admin/" target="_blank">Protect WP-Admin</a></li>
							<li><a href="https://wordpress.org/plugins/wp-sales-notifier/" target="_blank">WP Sales Notifier</a></li>
							<li><a href="https://wordpress.org/plugins/wp-tracking-manager/" target="_blank">WP Tracking Manager</a></li>
							<li><a href="https://wordpress.org/plugins/wp-categories-widget/" target="_blank">WP Categories Widget</a></li>
							<li><a href="https://wordpress.org/plugins/wp-protect-content/" target="_blank">WP Protect Content</a></li>
							<li><a href="https://wordpress.org/plugins/wp-version-remover/" target="_blank">WP Version Remover</a></li>
							<li><a href="https://wordpress.org/plugins/wp-posts-widget/" target="_blank">WP Post Widget</a></li>
							<li><a href="https://wordpress.org/plugins/wp-importer" target="_blank">WP Importer</a></li>
							<li><a href="https://wordpress.org/plugins/wp-csv-importer/" target="_blank">WP CSV Importer</a></li>
							<li><a href="https://wordpress.org/plugins/wp-testimonial/" target="_blank">WP Testimonial</a></li>
							<li><a href="https://wordpress.org/plugins/wc-sales-count-manager/" target="_blank">WooCommerce Sales Count Manager</a></li>
							<li><a href="https://wordpress.org/plugins/wp-social-buttons/" target="_blank">WP Social Buttons</a></li>
							<li><a href="https://wordpress.org/plugins/wp-youtube-gallery/" target="_blank">WP Youtube Gallery</a></li>
							<li><a href="https://wordpress.org/plugins/tweets-slider/" target="_blank">Tweets Slider</a></li>
							<li><a href="https://wordpress.org/plugins/rg-responsive-gallery/" target="_blank">RG Responsive Slider</a></li>
							<li><a href="https://wordpress.org/plugins/cf7-advance-security" target="_blank">Contact Form 7 Advance Security WP-Admin</a></li>
							<li><a href="https://wordpress.org/plugins/wp-easy-recipe/" target="_blank">WP Easy Recipe</a></li>
					</ol>
		</p></td>
		<td><p style="font-size:16px;">Want to know about all features of addon? Watch given below video</p>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/sXywBe0XWy0?rel=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></td>
	</tr>
	</table>

	</div>

	</div>
	<span class="submit-btn"><?php echo get_submit_button('Save Settings','button-primary','submit','','');?></span>
		
		<p ><strong style="color:red;" >Important!:</strong> Don't forget to preview new admin url after update new admin slug.</p>	

    <?php settings_fields('pwa_setting_options'); ?>
	</form>

<!-- End Options Form -->
	</div>

<?php
}
endif;
/** add js into admin footer */
// better use get_current_screen(); or the global $current_screen
if (isset($_GET['page']) && $_GET['page'] == 'pwa-settings') {
   add_action('admin_enqueue_scripts','init_pwa_admin_scripts');
}
if(!function_exists('init_pwa_admin_scripts')):
function init_pwa_admin_scripts()
{
wp_register_style( 'pwa_admin_style', plugins_url( 'css/pwa-admin-min.css',__FILE__ ) );
wp_enqueue_style( 'pwa_admin_style' );

wp_register_script('pwa-script', plugins_url('/js/pwa.js',__FILE__ ), array('jquery','media-upload','thickbox','wp-color-picker'));
wp_enqueue_script('pwa-script');
wp_enqueue_style( 'wp-color-picker' ); 
wp_enqueue_style('thickbox');

/* check .htaccess file writeable or not*/
$csbwfsHtaccessfilePath = getcwd()."/.htaccess";
$csbwfsHtaccessfilePath = str_replace('/wp-admin/','/',$csbwfsHtaccessfilePath);

if(file_exists($csbwfsHtaccessfilePath)){
	if(is_writable($csbwfsHtaccessfilePath))
	  { $htaccessWriteable="1";}
	  else 
	   { $htaccessWriteable="0";}
}else
{
	$htaccessWriteable="0";
	}
$localHostIP=$_SERVER['REMOTE_ADDR'];
$pwaActive=get_option('pwa_active');
$url = admin_url('options-permalink.php');

wp_localize_script( 'pwa-script', 'pwa_admin_object',
				array( 
					'st' => $pwaActive,
					'ip' => $localHostIP,
					'ht' => $htaccessWriteable,
					'ur' => $url,
				)
			);
}
endif;

// Add Check if permalinks are set on plugin activation
register_activation_hook( __FILE__, 'is_permalink_activate' );
if(!function_exists('is_permalink_activate')):
function is_permalink_activate() {
    //add notice if user needs to enable permalinks
    if (! get_option('permalink_structure') )
        add_action('admin_notices', 'permalink_structure_admin_notice');
}
endif;
if(!function_exists('permalink_structure_admin_notice')):
function permalink_structure_admin_notice(){
    echo ('<div id="message" class="error"><p>Please Make sure to enable <a href="options-permalink.php">Permalinks</a>.</p></div>');
}
endif;
/** register_install_hook */
if( function_exists('register_install_hook') ){
register_uninstall_hook(__FILE__,'init_install_pwa_plugins'); 
}
//flush the rewrite
if(!function_exists('init_install_pwa_plugins')):
function init_install_pwa_plugins(){
	  flush_rewrite_rules();
}
endif; 
/** register_uninstall_hook */
/** Delete exits options during disable the plugins */
if( function_exists('register_uninstall_hook') ){
   register_uninstall_hook(__FILE__,'flush_rewrite_rules');
   register_uninstall_hook(__FILE__,'init_uninstall_pwa_plugins');   
}
//Delete all options after uninstall the plugin
if(!function_exists('init_uninstall_pwa_plugins')):
function init_uninstall_pwa_plugins(){
	delete_option('pwa_active');
	delete_option('pwa_rewrite_text');	
	delete_option('pwa_restrict');	
	delete_option('pwa_logout');
	delete_option('pwa_allow_custom_users');
	delete_option('pwa_logo_path');
	delete_option('pwa_login_page_bg_color');
	delete_option('pwa_login_page_color');
}
endif;
require dirname(__FILE__).'/pwa-class.php';
/** register_deactivation_hook */
/** Delete exits options during deactivation the plugins */
if( function_exists('register_deactivation_hook') ){
   register_deactivation_hook(__FILE__,'init_deactivation_pwa_plugins');  
}

//Delete all options after uninstall the plugin
if(!function_exists('init_deactivation_pwa_plugins')):
function init_deactivation_pwa_plugins(){
	delete_option('pwa_active');
	delete_option('pwa_logout');
	remove_action('init', 'init_pwa_admin_rewrite_rules' );
	flush_rewrite_rules();
}
endif;
/** register_activation_hook */
/** Delete exits options during disable the plugins */
if( function_exists('register_activation_hook') ){
   register_activation_hook(__FILE__,'init_activation_pwa_plugins');    
}
//Delete all options after uninstall the plugin
if(!function_exists('init_activation_pwa_plugins')):
function init_activation_pwa_plugins(){
	delete_option('pwa_logout');
   	flush_rewrite_rules();
}
endif;

add_action('admin_init','pwa_flush_rewrite_rules');
//flush_rewrite_rules after update value
if(!function_exists('pwa_flush_rewrite_rules')):
function pwa_flush_rewrite_rules(){
	if(isset($_POST['option_page']) && $_POST['option_page']=='pwa_setting_options' && $_POST['pwa_active']==''){
		flush_rewrite_rules();
	}
}
endif;
/*
* call hooks action on update
* @upgrader_process_complete
*/
add_action( 'upgrader_process_complete', 'pwa_upgrade_function',10, 2);
 
function pwa_upgrade_function( $upgrader_object, $options ) {
   
    $current_plugin_path_name = plugin_basename( __FILE__ );
 
    if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
       foreach($options['plugins'] as $each_plugin) {
          if ($each_plugin==$current_plugin_path_name) {
              
             		add_action('init', 'init_pwa_admin_rewrite_rules' );
                	flush_rewrite_rules();

          }
       }
    }
}
?>
