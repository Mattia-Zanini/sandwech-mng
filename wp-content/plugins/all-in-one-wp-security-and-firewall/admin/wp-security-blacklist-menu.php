<?php
if (!defined('ABSPATH')) {
    exit;//Exit if accessed directly
}

/**
 * AIOWPSecurity_Blacklist_Menu class for banning ips and user agents.
 *
 * @access public
 */
class AIOWPSecurity_Blacklist_Menu extends AIOWPSecurity_Admin_Menu {

	/**
	 * Blacklist menu slug
	 *
	 * @var string
	 */
	private $menu_page_slug = AIOWPSEC_BLACKLIST_MENU_SLUG;

	/**
	 * Specify all the tabs of this menu
	 *
	 * @var array
	 */
	protected $menu_tabs;

	/**
	 * Specify all the tabs handler methods
	 *
	 * @var array
	 */
	protected $menu_tabs_handler = array(
		'ban-users' => 'render_ban_users',
	);
	
	/**
	 * Construct adds menu for blacklist
	 */
	public function __construct() {
		$this->render_menu_page();
	}

	/**
	 * Set menu tabs name.
	 */
	private function set_menu_tabs() {
		$this->menu_tabs = array(
			'ban-users' => __('Ban users', 'all-in-one-wp-security-and-firewall'),
		);
	}

	/**
	 * Renders our tabs of this menu as nav items
	 */
	private function render_menu_tabs() {
		$current_tab = $this->get_current_tab();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ($this->menu_tabs as $tab_key => $tab_caption) {
		    $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
		    echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}

    /**
     * The menu rendering goes here
     */
    private function render_menu_page() {
        echo '<div class="wrap">';
        echo '<h2>' . __('Blacklist manager', 'all-in-one-wp-security-and-firewall') . '</h2>';//Interface title
        $this->set_menu_tabs();
        $tab = $this->get_current_tab();
        $this->render_menu_tabs();
        ?>
        <div id="poststuff"><div id="post-body">
        <?php
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array($this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }
    
    /**
     * Renders ban user tab for blacklist IPs and user agents
     *
     * @global $aio_wp_security
     * @global $aiowps_feature_mgr
     * @global $aiowps_firewall_config
     */
    private function render_ban_users() {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        global $aiowps_firewall_config;
        $result = 1;
        if (isset($_POST['aiowps_save_blacklist_settings'])) {
        	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'aiowpsec-blacklist-settings-nonce')) {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for save blacklist settings.", 4);
                die('Nonce check failed for save blacklist settings.');
            }
			$aiowps_enable_blacklisting = isset($_POST["aiowps_enable_blacklisting"]) ? '1' : '';
			$aiowps_banned_ip_addresses = $aio_wp_security->configs->get_value('aiowps_banned_ip_addresses');
			$aiowps_banned_user_agents = $aio_wp_security->configs->get_value('aiowps_banned_user_agents');
			if ('' == $aiowps_enable_blacklisting && empty($aiowps_banned_ip_addresses) && empty($aiowps_banned_user_agents) && (!empty($_POST['aiowps_banned_ip_addresses']) || !empty($_POST['aiowps_banned_user_agents']))) {
				$result = -1;
                $this->show_msg_error('You must check the enable IP or user agent blacklisting.', 'all-in-one-wp-security-and-firewall');
            } else if ('1' == $aiowps_enable_blacklisting && empty($_POST['aiowps_banned_ip_addresses']) && empty($_POST['aiowps_banned_user_agents'])) {
                $this->show_msg_error('You must submit at least one IP address or one user agent value.', 'all-in-one-wp-security-and-firewall');
            } else {
                if ('1' == $aiowps_enable_blacklisting && !empty($_POST['aiowps_banned_ip_addresses'])) {
                    $ip_addresses = stripslashes($_POST['aiowps_banned_ip_addresses']);
                    $ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($ip_addresses);
                    $payload = AIOWPSecurity_Utility_IP::validate_ip_list($ip_list_array, 'blacklist');
                    if (1 == $payload[0]) {
                        //success case
                        $list = $payload[1];
                        $banned_ip_data = implode("\n", $list);
                        $aio_wp_security->configs->set_value('aiowps_banned_ip_addresses', $banned_ip_data);
                        $_POST['aiowps_banned_ip_addresses'] = ''; //Clear the post variable for the banned address list
                    } else {
                        $result = -1;
                        $error_msg = $payload[1][0];
                        $this->show_msg_error($error_msg);
                    }
                } else {
                    $aio_wp_security->configs->set_value('aiowps_banned_ip_addresses', ''); //Clear the IP address config value
                }

                if ('1' == $aiowps_enable_blacklisting && !empty($_POST['aiowps_banned_user_agents'])) {
                    $result = $result * $this->validate_user_agent_list(stripslashes($_POST['aiowps_banned_user_agents']));
                } else {
                    //clear the user agent list
                    $aio_wp_security->configs->set_value('aiowps_banned_user_agents', '');
                    $aiowps_firewall_config->set_value('aiowps_blacklist_user_agents', array());
                }

                if (1 == $result) {
                    $aio_wp_security->configs->set_value('aiowps_enable_blacklisting', $aiowps_enable_blacklisting);
                    $aio_wp_security->configs->save_config(); //Save the configuration

                    //Recalculate points after the feature status/options have been altered
                    $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
                    
                    $write_result = AIOWPSecurity_Utility_Htaccess::write_to_htaccess(); //now let's write to the .htaccess file
                    
                    if ($write_result) {
                    	$this->show_msg_settings_updated();
                    } else {
                    	$this->show_msg_error(__('The plugin was unable to write to the .htaccess file. Please edit the file manually.', 'all-in-one-wp-security-and-firewall'));
                    	$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Blacklist_Menu - The plugin was unable to write to the .htaccess file.");
                    }
                }
            }
        }
        ?>
        <h2><?php _e('Ban IPs or user agents', 'all-in-one-wp-security-and-firewall')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>' . __('The All In One WP Security Blacklist feature gives you the option of banning certain host IP addresses or ranges and also user agents.', 'all-in-one-wp-security-and-firewall').'
            <br />' . __('This feature will deny total site access for users which have IP addresses or user agents matching those which you have configured in the settings below.', 'all-in-one-wp-security-and-firewall').'
            <br />' . __('Black-listed visitors will be blocked as soon as WordPress loads, preventing them from gaining any further access.', 'all-in-one-wp-security-and-firewall').'
            </p>';
            ?>
        </div>
		<?php
		if (!defined('AIOWPSECURITY_NOADS_B') || !AIOWPSECURITY_NOADS_B) {
		?>
			<div class="aio_grey_box">
			<?php
				$premium_plugin_link = '<strong><a href="https://aiosplugin.com/" target="_blank">' . htmlspecialchars(__('All In One WP Security & Firewall Premium', 'all-in-one-wp-security-and-firewall')) . '</a></strong>';
				$info_msg = sprintf(__('You may also be interested in %s.', 'all-in-one-wp-security-and-firewall'), $premium_plugin_link);
				$info_msg2 = sprintf(__('This plugin adds a number of extra features including %s and %s.', 'all-in-one-wp-security-and-firewall'), '<strong>' . __('smart 404 blocking', 'all-in-one-wp-security-and-firewall') . '</strong>', '<strong>' . __('country IP blocking', 'all-in-one-wp-security-and-firewall') . '</strong>');
				
				echo '<p>' .
						$info_msg .
						'<br />' .
						$info_msg2 .
					'</p>';
			?>
			</div>
		<?php
		} 
		?>
        <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('IP hosts and user agent blacklist settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        $aiowps_feature_mgr->output_feature_details_badge("blacklist-manager-ip-user-agent-blacklisting");
        ?>
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-blacklist-settings-nonce'); ?>
        <div class="aio_orange_box">
            <p>
            <?php
            $read_link = '<a href="https://aiosplugin.com/important-note-on-intermediate-and-advanced-features" target="_blank">' . __('must read this message', 'all-in-one-wp-security-and-firewall') . '</a>';
            echo sprintf(__('This feature can lock you out of admin if it doesn\'t work correctly on your site. You %s before activating this feature.', 'all-in-one-wp-security-and-firewall'), $read_link);
            ?>
            </p>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable IP or user agent blacklisting', 'all-in-one-wp-security-and-firewall')?>:</th>
                <td>
                <input id="aiowps_enable_blacklisting" name="aiowps_enable_blacklisting" type="checkbox"<?php checked($aio_wp_security->configs->get_value('aiowps_enable_blacklisting')); ?> value="1"/>
                <label for="aiowps_enable_blacklisting" class="description"><?php _e('Check this if you want to enable the banning (or blacklisting) of selected IP addresses and/or user agents specified in the settings below', 'all-in-one-wp-security-and-firewall'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="aiowps_banned_ip_addresses"><?php _e('Enter IP addresses:', 'all-in-one-wp-security-and-firewall')?></label></th>
                <td>
                    <textarea id="aiowps_banned_ip_addresses" name="aiowps_banned_ip_addresses" rows="5" cols="50"><?php echo (-1 == $result) ? esc_textarea(wp_unslash($_POST['aiowps_banned_ip_addresses'])) : esc_textarea($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses')); ?></textarea>
                    <br />
                    <span class="description"><?php _e('Enter one or more IP addresses or IP ranges.', 'all-in-one-wp-security-and-firewall');?></span>
                    <?php $aio_wp_security->include_template('info/ip-address-ip-range-info.php');?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="aiowps_banned_user_agents"><?php _e('Enter user agents:', 'all-in-one-wp-security-and-firewall')?></label></th>
                <td>
                	<textarea id="aiowps_banned_user_agents" name="aiowps_banned_user_agents" rows="5" cols="50"><?php echo (-1 == $result) ? esc_textarea(wp_unslash($_POST['aiowps_banned_user_agents'])) : esc_textarea($aio_wp_security->configs->get_value('aiowps_banned_user_agents')); ?></textarea>
                    <br />
                    <span class="description">
                    <?php _e('Enter one or more user agent strings.', 'all-in-one-wp-security-and-firewall');?></span>
                    <span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
                    <div class="aiowps_more_info_body">
                            <?php
                            echo '<p class="description">' . __('Each user agent string must be on a new line.', 'all-in-one-wp-security-and-firewall') . '</p>';
                            echo '<p class="description">' . __('Example 1 - A single user agent string to block:', 'all-in-one-wp-security-and-firewall') . '</p>';
                            echo '<p class="description">SquigglebotBot</p>';
                            echo '<p class="description">' . __('Example 2 - A list of more than 1 user agent strings to block', 'all-in-one-wp-security-and-firewall') . '</p>';
                            echo '<p class="description">baiduspider<br />SquigglebotBot<br />SurveyBot<br />VoidEYE<br />webcrawl.net<br />YottaShopping_Bot</p>';
                            ?>
                    </div>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save settings', 'all-in-one-wp-security-and-firewall'), 'primary', 'aiowps_save_blacklist_settings');?>
        </form>
        </div></div>
        <?php
    }

    /**
     * Validates posted user agent list and set, save as config.
     *
     * @param string $banned_user_agents
     *
     * @global $aio_wp_security
     * @global $aiowps_firewall_config
     */
    private function validate_user_agent_list($banned_user_agents) {
        global $aio_wp_security, $aiowps_firewall_config;
        @ini_set('auto_detect_line_endings', true);
        $submitted_agents = explode("\n", $banned_user_agents);
    	$agents = array();
    	if (!empty($submitted_agents)) {
            foreach ($submitted_agents as $agent) {
            	if (!empty($agent)) {
            		$text = sanitize_text_field($agent);
            		$agents[] = $text;
            	}
            }
    	}

        if (sizeof($agents) > 1) {
            sort( $agents );
            $agents = array_unique($agents, SORT_STRING);
        }

        $banned_user_agent_data = implode("\n", $agents);
        $aio_wp_security->configs->set_value('aiowps_banned_user_agents', $banned_user_agent_data);
        $aiowps_firewall_config->set_value('aiowps_blacklist_user_agents', $agents);
        $_POST['aiowps_banned_user_agents'] = ''; //Clear the post variable for the banned address list
        return 1;
    }
} //end class
