<?php
## ---------------------------------------------
## BulletProof Security Setup Wizard Export
## Support: https://forum.ait-pro.com/
## Export Time: November 28, 2021 7:15 am
## Website: http://demo2.local
## WP ABSPATH: C:\xampp\htdocs9\demo2/
## ---------------------------------------------

## BPS Plugin Options

$bulletproof_security_options_auth_cookie = 'bulletproof_security_options_auth_cookie';
$bulletproof_security_options_auth_cookie_array = array(
'bps_ace' => 'On', 
'bps_ace_expiration' => '2880', 
'bps_ace_rememberme_expiration' => '20160', 
'bps_ace_user_account_exceptions' => 'ED5000', 
'bps_ace_administrator' => '1', 
'bps_ace_editor' => '1', 
'bps_ace_author' => '1', 
'bps_ace_contributor' => '1', 
'bps_ace_subscriber' => '1', 
'bps_ace_rememberme_disable' => '', 
);

if ( ! get_option( $bulletproof_security_options_auth_cookie ) ) {
foreach( $bulletproof_security_options_auth_cookie_array as $key => $value ) {
update_option('bulletproof_security_options_auth_cookie', $bulletproof_security_options_auth_cookie_array);
}

} else {

foreach( $bulletproof_security_options_auth_cookie_array as $key => $value ) {
update_option('bulletproof_security_options_auth_cookie', $bulletproof_security_options_auth_cookie_array);
}
}

$bulletproof_security_options_autolock = 'bulletproof_security_options_autolock';
$bulletproof_security_options_autolock_array = array(
'bps_root_htaccess_autolock' => 'Off', 
);

if ( ! get_option( $bulletproof_security_options_autolock ) ) {
foreach( $bulletproof_security_options_autolock_array as $key => $value ) {
update_option('bulletproof_security_options_autolock', $bulletproof_security_options_autolock_array);
}

} else {

foreach( $bulletproof_security_options_autolock_array as $key => $value ) {
update_option('bulletproof_security_options_autolock', $bulletproof_security_options_autolock_array);
}
}

$bulletproof_security_options_customcode = 'bulletproof_security_options_customcode';
$bulletproof_security_options_customcode_array = array(
'bps_customcode_one' => '', 
'bps_customcode_server_signature' => '', 
'bps_customcode_directory_index' => '', 
'bps_customcode_server_protocol' => '', 
'bps_customcode_error_logging' => '', 
'bps_customcode_deny_dot_folders' => '', 
'bps_customcode_admin_includes' => '', 
'bps_customcode_wp_rewrite_start' => '', 
'bps_customcode_request_methods' => '', 
'bps_customcode_two' => '', 
'bps_customcode_timthumb_misc' => '# TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# Remote File Inclusion (RFI) security rules
# Note: Only whitelist your additional domains or files if needed - do not whitelist hacker domains or files
RewriteCond %{QUERY_STRING} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC,OR]
RewriteCond %{THE_REQUEST} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC]
RewriteRule .* index.php [F]
# 
# Example: Whitelist additional misc files: (example\.php|another-file\.php|phpthumb\.php|thumb\.php|thumbs\.php)
RewriteCond %{REQUEST_URI} (timthumb\.php|phpthumb\.php|thumb\.php|thumbs\.php) [NC]
# Example: Whitelist additional website domains: RewriteCond %{HTTP_REFERER} ^.*(YourWebsite.com|AnotherWebsite.com).*
RewriteCond %{HTTP_REFERER} ^.*demo2.local.*
RewriteRule . - [S=1]', 
'bps_customcode_bpsqse' => '# BEGIN BPSQSE BPS QUERY STRING EXPLOITS
# The libwww-perl User Agent is forbidden - Many bad bots use libwww-perl modules, but some good bots use it too.
# Good sites such as W3C use it for their W3C-LinkChecker. 
# Use BPS Custom Code to add or remove user agents temporarily or permanently from the 
# User Agent filters directly below or to modify/edit/change any of the other security code rules below.
RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (;|&lt;|&gt;|&#039;|&quot;|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\s+|%20+\\s+|\\s+%20+|\\s+%20+\\s+)(http|https)(:/|/) [NC,OR]
RewriteCond %{THE_REQUEST} etc/passwd [NC,OR]
RewriteCond %{THE_REQUEST} cgi-bin [NC,OR]
RewriteCond %{THE_REQUEST} (%0A|%0D|\\r|\\n) [NC,OR]
RewriteCond %{REQUEST_URI} owssvr\.dll [NC,OR]
RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_REFERER} \.opendirviewer\. [NC,OR]
RewriteCond %{HTTP_REFERER} users\.skynet\.be.* [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC,OR]
RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]
RewriteCond %{QUERY_STRING} ftp\: [NC,OR]
RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR] 
RewriteCond %{QUERY_STRING} \=\|w\| [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)/self/(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*script.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^s]*s)+cript.*(&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*embed.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^e]*e)+mbed.*(&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*object.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^o]*o)+bject.*(&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*iframe.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^i]*i)+frame.*(&gt;|%3E) [NC,OR] 
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code[^(]*\([^)]*\) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} ^.*(\(|\)|&lt;|&gt;|%3c|%3e).* [NC,OR]
RewriteCond %{QUERY_STRING} ^.*(\x00|\x04|\x08|\x0d|\x1b|\x20|\x3c|\x3e|\x7f).* [NC,OR]
RewriteCond %{QUERY_STRING} (NULL|OUTFILE|LOAD_FILE) [OR]
RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|&gt;|&#039;|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{QUERY_STRING} concat[^\(]*\( [NC,OR]
RewriteCond %{QUERY_STRING} union([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} \-[sdcr].*(allow_url_include|allow_url_fopen|safe_mode|disable_functions|auto_prepend_file) [NC,OR]
RewriteCond %{QUERY_STRING} (;|&lt;|&gt;|&#039;|&quot;|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode) [NC,OR]
RewriteCond %{QUERY_STRING} (sp_executesql) [NC]
RewriteRule ^(.*)$ - [F]
# END BPSQSE BPS QUERY STRING EXPLOITS', 
'bps_customcode_deny_files' => '', 
'bps_customcode_three' => '', 
);

if ( ! get_option( $bulletproof_security_options_customcode ) ) {
foreach( $bulletproof_security_options_customcode_array as $key => $value ) {
update_option('bulletproof_security_options_customcode', $bulletproof_security_options_customcode_array);
}

} else {

foreach( $bulletproof_security_options_customcode_array as $key => $value ) {
update_option('bulletproof_security_options_customcode', $bulletproof_security_options_customcode_array);
}
}

$bulletproof_security_options_customcode_WPA = 'bulletproof_security_options_customcode_WPA';
$bulletproof_security_options_customcode_WPA_array = array(
'bps_customcode_deny_files_wpa' => '', 
'bps_customcode_one_wpa' => '', 
'bps_customcode_two_wpa' => '', 
'bps_customcode_bpsqse_wpa' => '# BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS
# WORDPRESS WILL BREAK IF ALL THE BPSQSE FILTERS ARE DELETED
# Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.
RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (;|&lt;|&gt;|&#039;|&quot;|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\s+|%20+\\s+|\\s+%20+|\\s+%20+\\s+)(http|https)(:/|/) [NC,OR]
RewriteCond %{THE_REQUEST} etc/passwd [NC,OR]
RewriteCond %{THE_REQUEST} cgi-bin [NC,OR]
RewriteCond %{THE_REQUEST} (%0A|%0D) [NC,OR]
RewriteCond %{REQUEST_URI} owssvr\.dll [NC,OR]
RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_REFERER} \.opendirviewer\. [NC,OR]
RewriteCond %{HTTP_REFERER} users\.skynet\.be.* [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC,OR]
RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]
RewriteCond %{QUERY_STRING} ftp\: [NC,OR]
RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR] 
RewriteCond %{QUERY_STRING} \=\|w\| [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)/self/(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*script.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^s]*s)+cript.*(&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\&lt;|%3C).*iframe.*(\&gt;|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|%3C)([^i]*i)+frame.*(&gt;|%3E) [NC,OR] 
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code[^(]*\([^)]*\) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} ^.*(\(|\)|&lt;|&gt;).* [NC,OR]
RewriteCond %{QUERY_STRING} (NULL|OUTFILE|LOAD_FILE) [OR]
RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} (&lt;|&gt;|&#039;|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{QUERY_STRING} concat[^\(]*\( [NC,OR]
RewriteCond %{QUERY_STRING} union([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} (;|&lt;|&gt;|&#039;|&quot;|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode) [NC,OR]
RewriteCond %{QUERY_STRING} (sp_executesql) [NC]
RewriteRule ^(.*)$ - [F]
# END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS
', 
);

if ( ! get_option( $bulletproof_security_options_customcode_WPA ) ) {
foreach( $bulletproof_security_options_customcode_WPA_array as $key => $value ) {
update_option('bulletproof_security_options_customcode_WPA', $bulletproof_security_options_customcode_WPA_array);
}

} else {

foreach( $bulletproof_security_options_customcode_WPA_array as $key => $value ) {
update_option('bulletproof_security_options_customcode_WPA', $bulletproof_security_options_customcode_WPA_array);
}
}

$bulletproof_security_options_debug = 'bulletproof_security_options_debug';
$bulletproof_security_options_debug_array = array(
'bps_debug' => 'Off', 
);

if ( ! get_option( $bulletproof_security_options_debug ) ) {
foreach( $bulletproof_security_options_debug_array as $key => $value ) {
update_option('bulletproof_security_options_debug', $bulletproof_security_options_debug_array);
}

} else {

foreach( $bulletproof_security_options_debug_array as $key => $value ) {
update_option('bulletproof_security_options_debug', $bulletproof_security_options_debug_array);
}
}

$bulletproof_security_options_email = 'bulletproof_security_options_email';
$bulletproof_security_options_email_array = array(
'bps_send_email_to' => 'edward@ait-pro.com', 
'bps_send_email_from' => 'edward@ait-pro.com', 
'bps_send_email_cc' => '', 
'bps_send_email_bcc' => '', 
'bps_login_security_email' => 'lockoutOnly', 
'bps_security_log_size' => '500KB', 
'bps_security_log_emailL' => 'email', 
'bps_dbb_log_email' => 'email', 
'bps_dbb_log_size' => '500KB', 
'bps_mscan_log_size' => '500KB', 
'bps_mscan_log_email' => 'email', 
'bps_plugin_updates_frequency' => '1Hour', 
'bps_plugin_updates_email' => 'no', 
'bps_theme_updates_frequency' => '1Hour', 
'bps_theme_updates_email' => 'no', 
);

if ( ! get_option( $bulletproof_security_options_email ) ) {
foreach( $bulletproof_security_options_email_array as $key => $value ) {
update_option('bulletproof_security_options_email', $bulletproof_security_options_email_array);
}

} else {

foreach( $bulletproof_security_options_email_array as $key => $value ) {
update_option('bulletproof_security_options_email', $bulletproof_security_options_email_array);
}
}

$bulletproof_security_options_fsp = 'bulletproof_security_options_fsp';
$bulletproof_security_options_fsp_array = array(
'bps_fsp_on_off' => 'Off', 
'bps_fsp_char_length' => '12', 
'bps_fsp_lower_case' => '1', 
'bps_fsp_upper_case' => '1', 
'bps_fsp_number' => '1', 
'bps_fsp_special_char' => '1', 
'bps_fsp_message' => 'Password must contain 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be a minimum of 12 characters long.', 
);

if ( ! get_option( $bulletproof_security_options_fsp ) ) {
foreach( $bulletproof_security_options_fsp_array as $key => $value ) {
update_option('bulletproof_security_options_fsp', $bulletproof_security_options_fsp_array);
}

} else {

foreach( $bulletproof_security_options_fsp_array as $key => $value ) {
update_option('bulletproof_security_options_fsp', $bulletproof_security_options_fsp_array);
}
}

$bulletproof_security_options_GDMW = 'bulletproof_security_options_GDMW';
$bulletproof_security_options_GDMW_array = array(
'bps_gdmw_hosting' => 'no', 
);

if ( ! get_option( $bulletproof_security_options_GDMW ) ) {
foreach( $bulletproof_security_options_GDMW_array as $key => $value ) {
update_option('bulletproof_security_options_GDMW', $bulletproof_security_options_GDMW_array);
}

} else {

foreach( $bulletproof_security_options_GDMW_array as $key => $value ) {
update_option('bulletproof_security_options_GDMW', $bulletproof_security_options_GDMW_array);
}
}

$bulletproof_security_options_gdpr = 'bulletproof_security_options_gdpr';
$bulletproof_security_options_gdpr_array = array(
'bps_gdpr_on_off' => 'Off', 
);

if ( ! get_option( $bulletproof_security_options_gdpr ) ) {
foreach( $bulletproof_security_options_gdpr_array as $key => $value ) {
update_option('bulletproof_security_options_gdpr', $bulletproof_security_options_gdpr_array);
}

} else {

foreach( $bulletproof_security_options_gdpr_array as $key => $value ) {
update_option('bulletproof_security_options_gdpr', $bulletproof_security_options_gdpr_array);
}
}

$bulletproof_security_options_hidden_plugins = 'bulletproof_security_options_hidden_plugins';
$bulletproof_security_options_hidden_plugins_array = array(
'bps_hidden_plugins_check' => 'test', 
);

if ( ! get_option( $bulletproof_security_options_hidden_plugins ) ) {
foreach( $bulletproof_security_options_hidden_plugins_array as $key => $value ) {
update_option('bulletproof_security_options_hidden_plugins', $bulletproof_security_options_hidden_plugins_array);
}

} else {

foreach( $bulletproof_security_options_hidden_plugins_array as $key => $value ) {
update_option('bulletproof_security_options_hidden_plugins', $bulletproof_security_options_hidden_plugins_array);
}
}

$bulletproof_security_options_hpf_cron = 'bulletproof_security_options_hpf_cron';
$bulletproof_security_options_hpf_cron_array = array(
'bps_hidden_plugins_cron' => 'On', 
'bps_hidden_plugins_cron_frequency' => '15', 
'bps_hidden_plugins_cron_email' => '', 
'bps_hidden_plugins_cron_alert' => '', 
);

if ( ! get_option( $bulletproof_security_options_hpf_cron ) ) {
foreach( $bulletproof_security_options_hpf_cron_array as $key => $value ) {
update_option('bulletproof_security_options_hpf_cron', $bulletproof_security_options_hpf_cron_array);
}

} else {

foreach( $bulletproof_security_options_hpf_cron_array as $key => $value ) {
update_option('bulletproof_security_options_hpf_cron', $bulletproof_security_options_hpf_cron_array);
}
}

$bulletproof_security_options_htaccess_files = 'bulletproof_security_options_htaccess_files';
$bulletproof_security_options_htaccess_files_array = array(
'bps_htaccess_files' => 'enabled', 
);

if ( ! get_option( $bulletproof_security_options_htaccess_files ) ) {
foreach( $bulletproof_security_options_htaccess_files_array as $key => $value ) {
update_option('bulletproof_security_options_htaccess_files', $bulletproof_security_options_htaccess_files_array);
}

} else {

foreach( $bulletproof_security_options_htaccess_files_array as $key => $value ) {
update_option('bulletproof_security_options_htaccess_files', $bulletproof_security_options_htaccess_files_array);
}
}

$bulletproof_security_options_htaccess_res = 'bulletproof_security_options_htaccess_res';
$bulletproof_security_options_htaccess_res_array = array(
'bps_wpadmin_restriction' => 'enabled', 
);

if ( ! get_option( $bulletproof_security_options_htaccess_res ) ) {
foreach( $bulletproof_security_options_htaccess_res_array as $key => $value ) {
update_option('bulletproof_security_options_htaccess_res', $bulletproof_security_options_htaccess_res_array);
}

} else {

foreach( $bulletproof_security_options_htaccess_res_array as $key => $value ) {
update_option('bulletproof_security_options_htaccess_res', $bulletproof_security_options_htaccess_res_array);
}
}

$bulletproof_security_options_idle_session = 'bulletproof_security_options_idle_session';
$bulletproof_security_options_idle_session_array = array(
'bps_isl' => 'On', 
'bps_isl_timeout' => '60', 
'bps_isl_logout_url' => 'http://demo2.local/wp-content/plugins/bulletproof-security/isl-logout.php', 
'bps_isl_login_url' => 'http://demo2.local/wp-login.php', 
'bps_isl_custom_message' => '', 
'bps_isl_custom_css_1' => 'background-color:#fff;line-height:normal;', 
'bps_isl_custom_css_2' => 'position:fixed;top:20%;left:0%;text-align:center;height:100%;width:100%;', 
'bps_isl_custom_css_3' => 'border:5px solid gray;background-color:#BCE2F1;', 
'bps_isl_custom_css_4' => 'font-family:Verdana, Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;', 
'bps_isl_user_account_exceptions' => 'ED5000', 
'bps_isl_administrator' => '1', 
'bps_isl_editor' => '1', 
'bps_isl_author' => '1', 
'bps_isl_contributor' => '1', 
'bps_isl_subscriber' => '1', 
'bps_isl_tinymce' => '', 
'bps_isl_uri_exclusions' => '', 
);

if ( ! get_option( $bulletproof_security_options_idle_session ) ) {
foreach( $bulletproof_security_options_idle_session_array as $key => $value ) {
update_option('bulletproof_security_options_idle_session', $bulletproof_security_options_idle_session_array);
}

} else {

foreach( $bulletproof_security_options_idle_session_array as $key => $value ) {
update_option('bulletproof_security_options_idle_session', $bulletproof_security_options_idle_session_array);
}
}

$bulletproof_security_options_login_security = 'bulletproof_security_options_login_security';
$bulletproof_security_options_login_security_array = array(
'bps_max_logins' => '3', 
'bps_lockout_duration' => '15', 
'bps_manual_lockout_duration' => '60', 
'bps_max_db_rows_display' => '', 
'bps_login_security_OnOff' => 'On', 
'bps_login_security_logging' => 'logAll', 
'bps_login_security_errors' => 'wpErrors', 
'bps_login_security_remaining' => 'On', 
'bps_login_security_pw_reset' => 'enable', 
'bps_login_security_sort' => 'descending', 
'bps_enable_lsm_woocommerce' => '', 
);

if ( ! get_option( $bulletproof_security_options_login_security ) ) {
foreach( $bulletproof_security_options_login_security_array as $key => $value ) {
update_option('bulletproof_security_options_login_security', $bulletproof_security_options_login_security_array);
}

} else {

foreach( $bulletproof_security_options_login_security_array as $key => $value ) {
update_option('bulletproof_security_options_login_security', $bulletproof_security_options_login_security_array);
}
}

$bulletproof_security_options_login_security_jtc = 'bulletproof_security_options_login_security_jtc';
$bulletproof_security_options_login_security_jtc_array = array(
'bps_tooltip_captcha_key' => 'jtc', 
'bps_tooltip_captcha_hover_text' => 'Type/Enter:  jtc', 
'bps_tooltip_captcha_title' => 'Hover or click the text box below', 
'bps_tooltip_captcha_logging' => 'Off', 
'bps_jtc_login_form' => '1', 
'bps_jtc_register_form' => '', 
'bps_jtc_lostpassword_form' => '', 
'bps_jtc_comment_form' => '', 
'bps_jtc_mu_register_form' => '', 
'bps_jtc_buddypress_register_form' => '', 
'bps_jtc_buddypress_sidebar_form' => '', 
'bps_jtc_administrator' => '', 
'bps_jtc_editor' => '', 
'bps_jtc_author' => '', 
'bps_jtc_contributor' => '', 
'bps_jtc_subscriber' => '', 
'bps_jtc_comment_form_error' => '<strong>ERROR</strong>: Incorrect JTC CAPTCHA Entered. Click your Browser back button and re-enter the JTC CAPTCHA.', 
'bps_jtc_comment_form_label' => 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;', 
'bps_jtc_comment_form_input' => 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;', 
'bps_enable_jtc_woocommerce' => '', 
'bps_jtc_custom_form_error' => '<strong>ERROR</strong>: Incorrect CAPTCHA Entered.', 
);

if ( ! get_option( $bulletproof_security_options_login_security_jtc ) ) {
foreach( $bulletproof_security_options_login_security_jtc_array as $key => $value ) {
update_option('bulletproof_security_options_login_security_jtc', $bulletproof_security_options_login_security_jtc_array);
}

} else {

foreach( $bulletproof_security_options_login_security_jtc_array as $key => $value ) {
update_option('bulletproof_security_options_login_security_jtc', $bulletproof_security_options_login_security_jtc_array);
}
}

$bulletproof_security_options_maint_mode = 'bulletproof_security_options_maint_mode';
$bulletproof_security_options_maint_mode_array = array(
'bps_maint_on_off' => 'Off', 
'bps_maint_countdown_timer' => '1', 
'bps_maint_countdown_timer_color' => 'white', 
'bps_maint_time' => '180', 
'bps_maint_retry_after' => '180', 
'bps_maint_frontend' => '1', 
'bps_maint_backend' => '', 
'bps_maint_ip_allowed' => '127.0.0.', 
'bps_maint_text' => '&lt;div id=\&quot;image-text-top\&quot; style=\&quot;position: absolute; top: -250px; left: -375px; margin: 0px 0px 0px 20px;\&quot;&gt;
&lt;h1&gt;Maintenance Mode Example&lt;/h1&gt;
&lt;span style=\&quot;margin: 0px 0px 0px 20px;\&quot;&gt;Message to display to website visitors&lt;/span&gt;

&lt;/div&gt;
&lt;div id=\&quot;image-position\&quot; style=\&quot;z-index: -1; position: absolute; top: -325px; left: -560px; background-size: auto; padding: 0px; -moz-box-shadow: 4px 4px 4px #888888; -webkit-box-shadow: 4px 4px 4px #888888; box-shadow: 4px 4px 4px #888888;\&quot;&gt;&lt;img class=\&quot;alignnone size-full wp-image-5\&quot; src=\&quot;http://demo2.local/wp-content/uploads/2021/11/ventura-coast.jpg\&quot; alt=\&quot;\&quot; width=\&quot;1612\&quot; height=\&quot;1075\&quot; /&gt;&lt;/div&gt;', 
'bps_maint_background_images' => '0', 
'bps_maint_center_images' => '0', 
'bps_maint_background_color' => 'white', 
'bps_maint_show_visitor_ip' => '1', 
'bps_maint_show_login_link' => '1', 
'bps_maint_dashboard_reminder' => '1', 
'bps_maint_log_visitors' => '1', 
'bps_maint_countdown_email' => '1', 
'bps_maint_email_to' => 'edward@ait-pro.com', 
'bps_maint_email_from' => 'edward@ait-pro.com', 
'bps_maint_email_cc' => 'edward@ait-pro.com', 
'bps_maint_email_bcc' => 'edward@ait-pro.com', 
'bps_maint_mu_entire_site' => '', 
'bps_maint_mu_subsites_only' => '', 
);

if ( ! get_option( $bulletproof_security_options_maint_mode ) ) {
foreach( $bulletproof_security_options_maint_mode_array as $key => $value ) {
update_option('bulletproof_security_options_maint_mode', $bulletproof_security_options_maint_mode_array);
}

} else {

foreach( $bulletproof_security_options_maint_mode_array as $key => $value ) {
update_option('bulletproof_security_options_maint_mode', $bulletproof_security_options_maint_mode_array);
}
}

$bulletproof_security_options_MScan = 'bulletproof_security_options_MScan';
$bulletproof_security_options_MScan_array = array(
'mscan_max_file_size' => '1000', 
'mscan_max_time_limit' => '300', 
'mscan_scan_database' => 'On', 
'mscan_scan_images' => 'Off', 
'mscan_scan_skipped_files' => 'Off', 
'mscan_scan_delete_tmp_files' => 'Off', 
'mscan_scan_frequency' => 'Off', 
'mscan_exclude_dirs' => '', 
'mscan_exclude_tmp_files' => '
', 
'mscan_file_size_limit_hidden' => '14', 
);

if ( ! get_option( $bulletproof_security_options_MScan ) ) {
foreach( $bulletproof_security_options_MScan_array as $key => $value ) {
update_option('bulletproof_security_options_MScan', $bulletproof_security_options_MScan_array);
}

} else {

foreach( $bulletproof_security_options_MScan_array as $key => $value ) {
update_option('bulletproof_security_options_MScan', $bulletproof_security_options_MScan_array);
}
}

$bulletproof_security_options_mscan_patterns = 'bulletproof_security_options_mscan_patterns';
$bulletproof_security_options_mscan_patterns_array = array(
);

if ( ! get_option( $bulletproof_security_options_mscan_patterns ) ) {
foreach( $bulletproof_security_options_mscan_patterns_array as $key => $value ) {
update_option('bulletproof_security_options_mscan_patterns', $bulletproof_security_options_mscan_patterns_array);
}

} else {

foreach( $bulletproof_security_options_mscan_patterns_array as $key => $value ) {
update_option('bulletproof_security_options_mscan_patterns', $bulletproof_security_options_mscan_patterns_array);
}
}

$bulletproof_security_options_mscan_report = 'bulletproof_security_options_mscan_report';
$bulletproof_security_options_mscan_report_array = array(
);

if ( ! get_option( $bulletproof_security_options_mscan_report ) ) {
foreach( $bulletproof_security_options_mscan_report_array as $key => $value ) {
update_option('bulletproof_security_options_mscan_report', $bulletproof_security_options_mscan_report_array);
}

} else {

foreach( $bulletproof_security_options_mscan_report_array as $key => $value ) {
update_option('bulletproof_security_options_mscan_report', $bulletproof_security_options_mscan_report_array);
}
}

$bulletproof_security_options_mu_sysinfo = 'bulletproof_security_options_mu_sysinfo';
$bulletproof_security_options_mu_sysinfo_array = array(
'bps_sysinfo_hide_display' => 'display', 
);

if ( ! get_option( $bulletproof_security_options_mu_sysinfo ) ) {
foreach( $bulletproof_security_options_mu_sysinfo_array as $key => $value ) {
update_option('bulletproof_security_options_mu_sysinfo', $bulletproof_security_options_mu_sysinfo_array);
}

} else {

foreach( $bulletproof_security_options_mu_sysinfo_array as $key => $value ) {
update_option('bulletproof_security_options_mu_sysinfo', $bulletproof_security_options_mu_sysinfo_array);
}
}

$bulletproof_security_options_mynotes = 'bulletproof_security_options_mynotes';
$bulletproof_security_options_mynotes_array = array(
'bps_my_notes' => '', 
);

if ( ! get_option( $bulletproof_security_options_mynotes ) ) {
foreach( $bulletproof_security_options_mynotes_array as $key => $value ) {
update_option('bulletproof_security_options_mynotes', $bulletproof_security_options_mynotes_array);
}

} else {

foreach( $bulletproof_security_options_mynotes_array as $key => $value ) {
update_option('bulletproof_security_options_mynotes', $bulletproof_security_options_mynotes_array);
}
}

$bulletproof_security_options_new_feature = 'bulletproof_security_options_new_feature';
$bulletproof_security_options_new_feature_array = array(
'bps_mscan_rebuild' => 'upgrade', 
);

if ( ! get_option( $bulletproof_security_options_new_feature ) ) {
foreach( $bulletproof_security_options_new_feature_array as $key => $value ) {
update_option('bulletproof_security_options_new_feature', $bulletproof_security_options_new_feature_array);
}

} else {

foreach( $bulletproof_security_options_new_feature_array as $key => $value ) {
update_option('bulletproof_security_options_new_feature', $bulletproof_security_options_new_feature_array);
}
}

$bulletproof_security_options_php_memory_limit = 'bulletproof_security_options_php_memory_limit';
$bulletproof_security_options_php_memory_limit_array = array(
'bps_php_memory_limit' => '512M', 
);

if ( ! get_option( $bulletproof_security_options_php_memory_limit ) ) {
foreach( $bulletproof_security_options_php_memory_limit_array as $key => $value ) {
update_option('bulletproof_security_options_php_memory_limit', $bulletproof_security_options_php_memory_limit_array);
}

} else {

foreach( $bulletproof_security_options_php_memory_limit_array as $key => $value ) {
update_option('bulletproof_security_options_php_memory_limit', $bulletproof_security_options_php_memory_limit_array);
}
}

$bulletproof_security_options_scrolltop = 'bulletproof_security_options_scrolltop';
$bulletproof_security_options_scrolltop_array = array(
'bps_scrolltop' => 'On', 
);

if ( ! get_option( $bulletproof_security_options_scrolltop ) ) {
foreach( $bulletproof_security_options_scrolltop_array as $key => $value ) {
update_option('bulletproof_security_options_scrolltop', $bulletproof_security_options_scrolltop_array);
}

} else {

foreach( $bulletproof_security_options_scrolltop_array as $key => $value ) {
update_option('bulletproof_security_options_scrolltop', $bulletproof_security_options_scrolltop_array);
}
}

$bulletproof_security_options_sec_log_post_limit = 'bulletproof_security_options_sec_log_post_limit';
$bulletproof_security_options_sec_log_post_limit_array = array(
'bps_security_log_post_limit' => '', 
'bps_security_log_post_none' => '1', 
'bps_security_log_post_max' => '', 
);

if ( ! get_option( $bulletproof_security_options_sec_log_post_limit ) ) {
foreach( $bulletproof_security_options_sec_log_post_limit_array as $key => $value ) {
update_option('bulletproof_security_options_sec_log_post_limit', $bulletproof_security_options_sec_log_post_limit_array);
}

} else {

foreach( $bulletproof_security_options_sec_log_post_limit_array as $key => $value ) {
update_option('bulletproof_security_options_sec_log_post_limit', $bulletproof_security_options_sec_log_post_limit_array);
}
}

$bulletproof_security_options_SLF = 'bulletproof_security_options_SLF';
$bulletproof_security_options_SLF_array = array(
'bps_slf_filter' => 'On', 
'bps_slf_filter_new' => '14', 
);

if ( ! get_option( $bulletproof_security_options_SLF ) ) {
foreach( $bulletproof_security_options_SLF_array as $key => $value ) {
update_option('bulletproof_security_options_SLF', $bulletproof_security_options_SLF_array);
}

} else {

foreach( $bulletproof_security_options_SLF_array as $key => $value ) {
update_option('bulletproof_security_options_SLF', $bulletproof_security_options_SLF_array);
}
}

$bulletproof_security_options_spinner = 'bulletproof_security_options_spinner';
$bulletproof_security_options_spinner_array = array(
'bps_spinner' => 'On', 
);

if ( ! get_option( $bulletproof_security_options_spinner ) ) {
foreach( $bulletproof_security_options_spinner_array as $key => $value ) {
update_option('bulletproof_security_options_spinner', $bulletproof_security_options_spinner_array);
}

} else {

foreach( $bulletproof_security_options_spinner_array as $key => $value ) {
update_option('bulletproof_security_options_spinner', $bulletproof_security_options_spinner_array);
}
}

$bulletproof_security_options_status_display = 'bulletproof_security_options_status_display';
$bulletproof_security_options_status_display_array = array(
'bps_status_display' => 'On', 
);

if ( ! get_option( $bulletproof_security_options_status_display ) ) {
foreach( $bulletproof_security_options_status_display_array as $key => $value ) {
update_option('bulletproof_security_options_status_display', $bulletproof_security_options_status_display_array);
}

} else {

foreach( $bulletproof_security_options_status_display_array as $key => $value ) {
update_option('bulletproof_security_options_status_display', $bulletproof_security_options_status_display_array);
}
}

$bulletproof_security_options_theme_skin = 'bulletproof_security_options_theme_skin';
$bulletproof_security_options_theme_skin_array = array(
'bps_ui_theme_skin' => 'blue', 
);

if ( ! get_option( $bulletproof_security_options_theme_skin ) ) {
foreach( $bulletproof_security_options_theme_skin_array as $key => $value ) {
update_option('bulletproof_security_options_theme_skin', $bulletproof_security_options_theme_skin_array);
}

} else {

foreach( $bulletproof_security_options_theme_skin_array as $key => $value ) {
update_option('bulletproof_security_options_theme_skin', $bulletproof_security_options_theme_skin_array);
}
}

$bulletproof_security_options_wizard_autofix = 'bulletproof_security_options_wizard_autofix';
$bulletproof_security_options_wizard_autofix_array = array(
'bps_wizard_autofix' => 'On', 
);

if ( ! get_option( $bulletproof_security_options_wizard_autofix ) ) {
foreach( $bulletproof_security_options_wizard_autofix_array as $key => $value ) {
update_option('bulletproof_security_options_wizard_autofix', $bulletproof_security_options_wizard_autofix_array);
}

} else {

foreach( $bulletproof_security_options_wizard_autofix_array as $key => $value ) {
update_option('bulletproof_security_options_wizard_autofix', $bulletproof_security_options_wizard_autofix_array);
}
}

$bulletproof_security_options_wpt_nodes = 'bulletproof_security_options_wpt_nodes';
$bulletproof_security_options_wpt_nodes_array = array(
'bps_wpt_nodes' => 'allnodes', 
);

if ( ! get_option( $bulletproof_security_options_wpt_nodes ) ) {
foreach( $bulletproof_security_options_wpt_nodes_array as $key => $value ) {
update_option('bulletproof_security_options_wpt_nodes', $bulletproof_security_options_wpt_nodes_array);
}

} else {

foreach( $bulletproof_security_options_wpt_nodes_array as $key => $value ) {
update_option('bulletproof_security_options_wpt_nodes', $bulletproof_security_options_wpt_nodes_array);
}
}

$bulletproof_security_options_zip_fix = 'bulletproof_security_options_zip_fix';
$bulletproof_security_options_zip_fix_array = array(
'bps_zip_download_fix' => 'Off', 
);

if ( ! get_option( $bulletproof_security_options_zip_fix ) ) {
foreach( $bulletproof_security_options_zip_fix_array as $key => $value ) {
update_option('bulletproof_security_options_zip_fix', $bulletproof_security_options_zip_fix_array);
}

} else {

foreach( $bulletproof_security_options_zip_fix_array as $key => $value ) {
update_option('bulletproof_security_options_zip_fix', $bulletproof_security_options_zip_fix_array);
}
}

?>
