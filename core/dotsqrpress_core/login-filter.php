<?php
/**
 * @package DOTSQRPress
 * @version 1.0
 */
/*
Plugin Name: DOTSQRPress Login Filter
Contributors: @gallettigr
Plugin URI: http://dotsqr.co
Description: Extended security plugin for DOTSQRPress to filter and block login tries. Increases WP security.
Author: @gallettigr
Version: 1.2
Author URI: http://www.twitter.com/gallettigr
Textdomain: dotsqrpress_core
Domain Path: /languages/
*/

# INIT & DEFINITIONS
define('DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS', 'REMOTE_ADDR');
define('DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS', 'HTTP_X_FORWARDED_FOR');
define('DOTSQRPRESS_LOGIN_FILTER_LOCKOUT_NOTIFY_ALLOWED', 'log,email');
$dotsqrpress_login_filter =
	array(
		  'client_type' => DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS
		  , 'allowed_retries' => 5
		  , 'lockout_duration' => 1200 // 20 minutes
		  , 'allowed_lockouts' => 3
		  , 'long_duration' => 86400 // 24 hours
		  , 'valid_duration' => 43200 // 12 hours
		  , 'cookies' => true
		  , 'lockout_notify' => 'log'
		  , 'notify_email_after' => 4
		  );

$dotsqrpress_login_filter_show_errorss = false;
$dotsqrpress_login_filter_user_lockedout = false;
$dotsqrpress_login_filter_nonempty_credentials = false;

add_action('plugins_loaded', 'dotsqrpress_login_filter_plugin_setup', 99999);
function dotsqrpress_login_filter_plugin_setup() {
	$domain = 'dotsqrpress_core';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
		return $loaded;
	} else {
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	dotsqrpress_login_filter_setup_options();
	add_action('wp_login_failed', 'dotsqrpress_login_filter_failed');
	if (dotsqrpress_login_filter_option('cookies')) {
		dotsqrpress_login_filter_handle_cookies();
		add_action('auth_cookie_bad_username', 'dotsqrpress_login_filter_failed_cookie');

		global $wp_version;

		if (version_compare($wp_version, '3.0', '>=')) {
			add_action('auth_cookie_bad_hash', 'dotsqrpress_login_filter_failed_cookie_hash');
			add_action('auth_cookie_valid', 'dotsqrpress_login_filter_valid_cookie', 10, 2);
		} else {
			add_action('auth_cookie_bad_hash', 'dotsqrpress_login_filter_failed_cookie');
		}
	}
	add_filter('wp_authenticate_user', 'dotsqrpress_login_filter_wp_authenticate_user', 99999, 2);
	add_filter('shake_error_codes', 'dotsqrpress_login_filter_failure_shake');
	add_action('login_head', 'dotsqrpress_login_filter_add_error_message');
	//add_action('login_errors', 'dotsqrpress_login_filter_fixup_error_messages');
	add_action('admin_menu', 'dotsqrpress_login_filter_admin_menu');
	add_action('wp_authenticate', 'dotsqrpress_login_filter_track_credentials', 10, 2);
}

function dotsqrpress_login_filter_option($option_name) {
	global $dotsqrpress_login_filter;
	if (isset($dotsqrpress_login_filter[$option_name])) {
		return $dotsqrpress_login_filter[$option_name];
	} else {
		return null;
	}
}

function dotsqrpress_login_filter_get_address($type_name = '') {
	$type = $type_name;
	if (empty($type)) {
		$type = dotsqrpress_login_filter_option('client_type');
	}
	if (isset($_SERVER[$type])) {
		return $_SERVER[$type];
	}
	if ( empty($type_name) && $type == DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS
		 && isset($_SERVER[DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS])) {

		return $_SERVER[DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS];
	}

	return '';
}


function is_dotsqrpress_login_filter_ip_whitelisted($ip = null) {
	if (is_null($ip)) {
		$ip = dotsqrpress_login_filter_get_address();
	}
	$whitelisted = apply_filters('dotsqrpress_login_filter_whitelist_ip', false, $ip);

	return ($whitelisted === true);
}

function is_dotsqrpress_login_filter_ok() {
	$ip = dotsqrpress_login_filter_get_address();
	if (is_dotsqrpress_login_filter_ip_whitelisted($ip)) {
		return true;
	}
	$lockouts = get_option('dotsqrpress_login_filter_lockouts');
	return (!is_array($lockouts) || !isset($lockouts[$ip]) || time() >= $lockouts[$ip]);
}

function dotsqrpress_login_filter_wp_authenticate_user($user, $password) {
	if (is_wp_error($user) || is_dotsqrpress_login_filter_ok() ) {
		return $user;
	}

	global $dotsqrpress_login_filter_show_errorss;
	$dotsqrpress_login_filter_show_errorss = true;

	$error = new WP_Error();
	$error->add('too_many_retries', dotsqrpress_login_filter_error_msg());
	return $error;
}

function dotsqrpress_login_filter_failure_shake($error_codes) {
	$error_codes[] = 'too_many_retries';
	return $error_codes;
}

function dotsqrpress_login_filter_handle_cookies() {
	if (is_dotsqrpress_login_filter_ok()) {
		return;
	}
	dotsqrpress_login_filter_clear_auth_cookie();
}

function dotsqrpress_login_filter_failed_cookie_hash($cookie_elements) {
	dotsqrpress_login_filter_clear_auth_cookie();

	extract($cookie_elements, EXTR_OVERWRITE);
	$user = get_user_by('login', $username);
	if (!$user) {
		dotsqrpress_login_filter_failed($username);
		return;
	}

	$previous_cookie = get_user_meta($user->ID, 'dotsqrpress_login_filter_previous_cookie', true);
	if ($previous_cookie && $previous_cookie == $cookie_elements) {
		return;
	}
	if ($previous_cookie)
		update_user_meta($user->ID, 'dotsqrpress_login_filter_previous_cookie', $cookie_elements);
	else
		add_user_meta($user->ID, 'dotsqrpress_login_filter_previous_cookie', $cookie_elements, true);

	dotsqrpress_login_filter_failed($username);
}

function dotsqrpress_login_filter_valid_cookie($cookie_elements, $user) {
	if (get_user_meta($user->ID, 'dotsqrpress_login_filter_previous_cookie')) {
		delete_user_meta($user->ID, 'dotsqrpress_login_filter_previous_cookie');
	}
}

function dotsqrpress_login_filter_failed_cookie($cookie_elements) {
	dotsqrpress_login_filter_clear_auth_cookie();
	dotsqrpress_login_filter_failed($cookie_elements['username']);
}

function dotsqrpress_login_filter_clear_auth_cookie() {
	wp_clear_auth_cookie();

	if (!empty($_COOKIE[AUTH_COOKIE])) {
		$_COOKIE[AUTH_COOKIE] = '';
	}
	if (!empty($_COOKIE[SECURE_AUTH_COOKIE])) {
		$_COOKIE[SECURE_AUTH_COOKIE] = '';
	}
	if (!empty($_COOKIE[LOGGED_IN_COOKIE])) {
		$_COOKIE[LOGGED_IN_COOKIE] = '';
	}
}

function dotsqrpress_login_filter_failed($username) {
	$ip = dotsqrpress_login_filter_get_address();
	$lockouts = get_option('dotsqrpress_login_filter_lockouts');
	if (!is_array($lockouts)) {
		$lockouts = array();
	}
	if(isset($lockouts[$ip]) && time() < $lockouts[$ip]) {
		return;
	}
	$retries = get_option('dotsqrpress_login_filter_retries');
	$valid = get_option('dotsqrpress_login_filter_retries_valid');
	if (!is_array($retries)) {
		$retries = array();
		add_option('dotsqrpress_login_filter_retries', $retries, '', 'no');
	}
	if (!is_array($valid)) {
		$valid = array();
		add_option('dotsqrpress_login_filter_retries_valid', $valid, '', 'no');
	}
	if (isset($retries[$ip]) && isset($valid[$ip]) && time() < $valid[$ip]) {
		$retries[$ip] ++;
	} else {
		$retries[$ip] = 1;
	}
	$valid[$ip] = time() + dotsqrpress_login_filter_option('valid_duration');
	if($retries[$ip] % dotsqrpress_login_filter_option('allowed_retries') != 0) {
		dotsqrpress_login_filter_cleanup($retries, null, $valid);
		return;
	}
	$whitelisted = is_dotsqrpress_login_filter_ip_whitelisted($ip);
	$retries_long = dotsqrpress_login_filter_option('allowed_retries')
		* dotsqrpress_login_filter_option('allowed_lockouts');

	if ($whitelisted) {
		if ($retries[$ip] >= $retries_long) {
			unset($retries[$ip]);
			unset($valid[$ip]);
		}
	} else {
		global $dotsqrpress_login_filter_user_lockedout;
		$dotsqrpress_login_filter_user_lockedout = true;

		if ($retries[$ip] >= $retries_long) {
			$lockouts[$ip] = time() + dotsqrpress_login_filter_option('long_duration');
			unset($retries[$ip]);
			unset($valid[$ip]);
		} else {
			$lockouts[$ip] = time() + dotsqrpress_login_filter_option('lockout_duration');
		}
	}
	dotsqrpress_login_filter_cleanup($retries, $lockouts, $valid);
	dotsqrpress_login_filter_notify($username);

	$total = get_option('dotsqrpress_login_filter_lockouts_total');
	if ($total === false || !is_numeric($total)) {
		add_option('dotsqrpress_login_filter_lockouts_total', 1, '', 'no');
	} else {
		update_option('dotsqrpress_login_filter_lockouts_total', $total + 1);
	}
}

function dotsqrpress_login_filter_cleanup($retries = null, $lockouts = null, $valid = null) {
	$now = time();
	$lockouts = !is_null($lockouts) ? $lockouts : get_option('dotsqrpress_login_filter_lockouts');
	if (is_array($lockouts)) {
		foreach ($lockouts as $ip => $lockout) {
			if ($lockout < $now) {
				unset($lockouts[$ip]);
			}
		}
		update_option('dotsqrpress_login_filter_lockouts', $lockouts);
	}

	$valid = !is_null($valid) ? $valid : get_option('dotsqrpress_login_filter_retries_valid');
	$retries = !is_null($retries) ? $retries : get_option('dotsqrpress_login_filter_retries');
	if (!is_array($valid) || !is_array($retries)) {
		return;
	}

	foreach ($valid as $ip => $lockout) {
		if ($lockout < $now) {
			unset($valid[$ip]);
			unset($retries[$ip]);
		}
	}

	foreach ($retries as $ip => $retry) {
		if (!isset($valid[$ip])) {
			unset($retries[$ip]);
		}
	}

	update_option('dotsqrpress_login_filter_retries', $retries);
	update_option('dotsqrpress_login_filter_retries_valid', $valid);
}

function is_dotsqrpress_login_filter_multisite() {
	return function_exists('get_site_option') && function_exists('is_multisite') && is_multisite();
}

function dotsqrpress_login_filter_notify_email($user) {
	$ip = dotsqrpress_login_filter_get_address();
	$whitelisted = is_dotsqrpress_login_filter_ip_whitelisted($ip);

	$retries = get_option('dotsqrpress_login_filter_retries');
	if (!is_array($retries)) {
		$retries = array();
	}
	if ( isset($retries[$ip])
		 && ( ($retries[$ip] / dotsqrpress_login_filter_option('allowed_retries'))
			  % dotsqrpress_login_filter_option('notify_email_after') ) != 0 ) {
		return;
	}
	if (!isset($retries[$ip])) {
		$count = dotsqrpress_login_filter_option('allowed_retries')
			* dotsqrpress_login_filter_option('allowed_lockouts');
		$lockouts = dotsqrpress_login_filter_option('allowed_lockouts');
		$time = round(dotsqrpress_login_filter_option('long_duration') / 3600);
		$when = sprintf(_n('%d hour', '%d hours', $time, 'dotsqrpress_core'), $time);
	} else {
		$count = $retries[$ip];
		$lockouts = floor($count / dotsqrpress_login_filter_option('allowed_retries'));
		$time = round(dotsqrpress_login_filter_option('lockout_duration') / 60);
		$when = sprintf(_n('%d minute', '%d minutes', $time, 'dotsqrpress_core'), $time);
	}

	$blogname = is_dotsqrpress_login_filter_multisite() ? get_site_option('site_name') : get_option('blogname');

	if ($whitelisted) {
		$subject = sprintf(__("[%s] Failed login tries from whitelisted IP"
				      , 'dotsqrpress_core')
				   , $blogname);
	} else {
		$subject = sprintf(__("[%s] Too many failed login tries"
				      , 'dotsqrpress_core')
				   , $blogname);
	}

	$message = sprintf(__("%d failed login tries (%d lockout(s)) from IP: %s"
			      , 'dotsqrpress_core') . "\r\n\r\n"
			   , $count, $lockouts, $ip);
	if ($user != '') {
		$message .= sprintf(__("Last user tried: %s", 'dotsqrpress_core')
				    . "\r\n\r\n" , $user);
	}
	if ($whitelisted) {
		$message .= __("IP was NOT blocked because of external whitelist.", 'dotsqrpress_core');
	} else {
		$message .= sprintf(__("IP was blocked for %s", 'dotsqrpress_core'), $when);
	}

	$admin_email = is_dotsqrpress_login_filter_multisite() ? get_site_option('admin_email') : get_option('admin_email');

	@wp_mail($admin_email, $subject, $message);
}

function dotsqrpress_login_filter_notify_log($user) {
	$log = $option = get_option('dotsqrpress_login_filter_logged');
	if (!is_array($log)) {
		$log = array();
	}
	$ip = dotsqrpress_login_filter_get_address();
	if (isset($log[$ip])) {
		if (isset($log[$ip][$user])) {
			$log[$ip][$user]++;
		} else {
			$log[$ip][$user] = 1;
		}
	} else {
		$log[$ip] = array($user => 1);
	}

	if ($option === false) {
		add_option('dotsqrpress_login_filter_logged', $log, '', 'no'); /* no autoload */
	} else {
		update_option('dotsqrpress_login_filter_logged', $log);
	}
}

function dotsqrpress_login_filter_notify($user) {
	$args = explode(',', dotsqrpress_login_filter_option('lockout_notify'));

	if (empty($args)) {
		return;
	}

	foreach ($args as $mode) {
		switch (trim($mode)) {
		case 'email':
			dotsqrpress_login_filter_notify_email($user);
			break;
		case 'log':
			dotsqrpress_login_filter_notify_log($user);
			break;
		}
	}
}

function dotsqrpress_login_filter_error_msg() {
	$ip = dotsqrpress_login_filter_get_address();
	$lockouts = get_option('dotsqrpress_login_filter_lockouts');
	$msg = __('<strong>ERROR</strong>: Too many failed login tries.', 'dotsqrpress_core') . ' ';

	if (!is_array($lockouts) || !isset($lockouts[$ip]) || time() >= $lockouts[$ip]) {
		/* Huh? No timeout active? */
		$msg .=  __('Please try again later.', 'dotsqrpress_core');
		return $msg;
	}

	$when = ceil(($lockouts[$ip] - time()) / 60);
	if ($when > 60) {
		$when = ceil($when / 60);
		$msg .= sprintf(_n('Please try again in %d hour.', 'Please try again in %d hours.', $when, 'dotsqrpress_core'), $when);
	} else {
		$msg .= sprintf(_n('Please try again in %d minute.', 'Please try again in %d minutes.', $when, 'dotsqrpress_core'), $when);
	}

	return $msg;
}

function dotsqrpress_login_filter_retries_remaining_msg() {
	$ip = dotsqrpress_login_filter_get_address();
	$retries = get_option('dotsqrpress_login_filter_retries');
	$valid = get_option('dotsqrpress_login_filter_retries_valid');

	if (!is_array($retries) || !is_array($valid)) {
		return '';
	}
	if (!isset($retries[$ip]) || !isset($valid[$ip]) || time() > $valid[$ip]) {
		return '';
	}
	if (($retries[$ip] % dotsqrpress_login_filter_option('allowed_retries')) == 0 ) {
		return '';
	}
	$remaining = max((dotsqrpress_login_filter_option('allowed_retries') - ($retries[$ip] % dotsqrpress_login_filter_option('allowed_retries'))), 0);
	return sprintf(_n("<strong>%d</strong> more try remaining.", "<strong>%d</strong> more tries remaining.", $remaining, 'dotsqrpress_core'), $remaining);
}

function dotsqrpress_login_filter_get_message() {
	if (is_dotsqrpress_login_filter_ip_whitelisted()) {
		return '';
	}
	if (!is_dotsqrpress_login_filter_ok()) {
		return dotsqrpress_login_filter_error_msg();
	}

	return dotsqrpress_login_filter_retries_remaining_msg();
}

function should_dotsqrpress_login_filter_show_msg() {
	if (isset($_GET['key'])) {
		return false;
	}

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	return ( $action != 'lostpassword' && $action != 'retrievepassword'
			 && $action != 'resetpass' && $action != 'rp'
			 && $action != 'register' );
}

function dotsqrpress_login_filter_fixup_error_messages($content) {
	global $dotsqrpress_login_filter_user_lockedout, $dotsqrpress_login_filter_nonempty_credentials, $dotsqrpress_login_filter_show_errorss;

	if (!should_dotsqrpress_login_filter_show_msg()) {
		return $content;
	}

	if (!is_dotsqrpress_login_filter_ok() && !$dotsqrpress_login_filter_user_lockedout) {
		return dotsqrpress_login_filter_error_msg();
	}

	$msgs = explode("<br />\n", $content);

	if (strlen(end($msgs)) == 0) {
		/* remove last entry empty string */
		array_pop($msgs);
	}

	$count = count($msgs);
	$my_warn_count = $dotsqrpress_login_filter_show_errorss ? 1 : 0;

	if ($dotsqrpress_login_filter_nonempty_credentials && $count > $my_warn_count) {
		/* Replace error message, including ours if necessary */
		$content = __('<strong>ERROR</strong>: Incorrect username or password.', 'dotsqrpress_core') . "<br />\n";
		if ($dotsqrpress_login_filter_show_errorss) {
			$content .= "<br />\n" . dotsqrpress_login_filter_get_message() . "<br />\n";
		}
		return $content;
	} elseif ($count <= 1) {
		return $content;
	}

	$new = '';
	while ($count-- > 0) {
		$new .= array_shift($msgs) . "<br />\n";
		if ($count > 0) {
			$new .= "<br />\n";
		}
	}

	return $new;
}

function dotsqrpress_login_filter_add_error_message() {
	global $error, $dotsqrpress_login_filter_show_errorss;

	if (!should_dotsqrpress_login_filter_show_msg() || $dotsqrpress_login_filter_show_errorss) {
		return;
	}

	$msg = dotsqrpress_login_filter_get_message();

	if ($msg != '') {
		$dotsqrpress_login_filter_show_errorss = true;
		$error .= $msg;
	}

	return;
}

function dotsqrpress_login_filter_track_credentials($user, $password) {
	global $dotsqrpress_login_filter_nonempty_credentials;

	$dotsqrpress_login_filter_nonempty_credentials = (!empty($user) && !empty($password));
}

function dotsqrpress_login_filter_guess_proxy() {
	return isset($_SERVER[DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS])
		? DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS : DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS;
}

function dotsqrpress_login_filter_get_option($option, $var_name) {
	$a = get_option($option);

	if ($a !== false) {
		global $dotsqrpress_login_filter;

		$dotsqrpress_login_filter[$var_name] = $a;
	}
}

function dotsqrpress_login_filter_setup_options() {
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_client_type', 'client_type');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_allowed_retries', 'allowed_retries');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_lockout_duration', 'lockout_duration');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_valid_duration', 'valid_duration');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_cookies', 'cookies');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_lockout_notify', 'lockout_notify');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_allowed_lockouts', 'allowed_lockouts');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_long_duration', 'long_duration');
	dotsqrpress_login_filter_get_option('dotsqrpress_login_filter_notify_email_after', 'notify_email_after');

	dotsqrpress_login_filter_sanitize_variables();
}

function dotsqrpress_login_filter_update_options() {
	update_option('dotsqrpress_login_filter_client_type', dotsqrpress_login_filter_option('client_type'));
	update_option('dotsqrpress_login_filter_allowed_retries', dotsqrpress_login_filter_option('allowed_retries'));
	update_option('dotsqrpress_login_filter_lockout_duration', dotsqrpress_login_filter_option('lockout_duration'));
	update_option('dotsqrpress_login_filter_allowed_lockouts', dotsqrpress_login_filter_option('allowed_lockouts'));
	update_option('dotsqrpress_login_filter_long_duration', dotsqrpress_login_filter_option('long_duration'));
	update_option('dotsqrpress_login_filter_valid_duration', dotsqrpress_login_filter_option('valid_duration'));
	update_option('dotsqrpress_login_filter_lockout_notify', dotsqrpress_login_filter_option('lockout_notify'));
	update_option('dotsqrpress_login_filter_notify_email_after', dotsqrpress_login_filter_option('notify_email_after'));
	update_option('dotsqrpress_login_filter_cookies', dotsqrpress_login_filter_option('cookies') ? '1' : '0');
}

function dotsqrpress_login_filter_sanitize_simple_int($var_name) {
	global $dotsqrpress_login_filter;

	$dotsqrpress_login_filter[$var_name] = max(1, intval(dotsqrpress_login_filter_option($var_name)));
}

function dotsqrpress_login_filter_sanitize_variables() {
	global $dotsqrpress_login_filter;

	dotsqrpress_login_filter_sanitize_simple_int('allowed_retries');
	dotsqrpress_login_filter_sanitize_simple_int('lockout_duration');
	dotsqrpress_login_filter_sanitize_simple_int('valid_duration');
	dotsqrpress_login_filter_sanitize_simple_int('allowed_lockouts');
	dotsqrpress_login_filter_sanitize_simple_int('long_duration');

	$dotsqrpress_login_filter['cookies'] = !!dotsqrpress_login_filter_option('cookies');

	$notify_email_after = max(1, intval(dotsqrpress_login_filter_option('notify_email_after')));
	$dotsqrpress_login_filter['notify_email_after'] = min(dotsqrpress_login_filter_option('allowed_lockouts'), $notify_email_after);

	$args = explode(',', dotsqrpress_login_filter_option('lockout_notify'));
	$args_allowed = explode(',', DOTSQRPRESS_LOGIN_FILTER_LOCKOUT_NOTIFY_ALLOWED);
	$new_args = array();
	foreach ($args as $a) {
		if (in_array($a, $args_allowed)) {
			$new_args[] = $a;
		}
	}
	$dotsqrpress_login_filter['lockout_notify'] = implode(',', $new_args);

	if ( dotsqrpress_login_filter_option('client_type') != DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS
		 && dotsqrpress_login_filter_option('client_type') != DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS ) {
		$dotsqrpress_login_filter['client_type'] = DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS;
	}
}

function dotsqrpress_login_filter_admin_menu() {
	global $wp_version;

	if (version_compare($wp_version, '3.0', '>=')) {
	    add_options_page('Limit Login Attempts', 'Limit Login Attempts', 'manage_options', 'dotsqrpress_core', 'dotsqrpress_login_filter_option_page');
	    return;
	}
	if (function_exists("get_current_site")) {
	    add_submenu_page('wpmu-admin.php', 'Limit Login Attempts', 'Limit Login Attempts', 9, 'dotsqrpress_core', 'dotsqrpress_login_filter_option_page');
	    return;
	}
	add_options_page('Limit Login Attempts', 'Limit Login Attempts', 9, 'dotsqrpress_core', 'dotsqrpress_login_filter_option_page');
}

function dotsqrpress_login_filter_show_log($log) {
	if (!is_array($log) || count($log) == 0) {
		return;
	}
	echo('<tr><th scope="col">' . _x("IP", "Internet address", 'dotsqrpress_core') . '</th><th scope="col">' . __('Tried to log in as', 'dotsqrpress_core') . '</th></tr>');
	foreach ($log as $ip => $arr) {
		echo('<tr><td class="limit-login-ip">' . $ip . '</td><td class="limit-login-max">');
		$first = true;
		foreach($arr as $user => $count) {
			$count_desc = sprintf(_n('%d lockout', '%d lockouts', $count, 'dotsqrpress_core'), $count);
			if (!$first) {
				echo(', ' . $user . ' (' .  $count_desc . ')');
			} else {
				echo($user . ' (' .  $count_desc . ')');
			}
			$first = false;
		}
		echo('</td></tr>');
	}
}

function dotsqrpress_login_filter_option_page()	{
	dotsqrpress_login_filter_cleanup();

	if (!current_user_can('manage_options')) {
		wp_die('Sorry, but you do not have permissions to change settings.');
	}
	if (count($_POST) > 0) {
		check_admin_referer('dotsqrpress-login-filter-options');
	}
	if (isset($_POST['clear_log'])) {
		delete_option('dotsqrpress_login_filter_logged');
		echo '<div id="message" class="updated fade"><p>'
			. __('Cleared IP log', 'dotsqrpress_core')
			. '</p></div>';
	}
	if (isset($_POST['reset_total'])) {
		update_option('dotsqrpress_login_filter_lockouts_total', 0);
		echo '<div id="message" class="updated fade"><p>'
			. __('Reset lockout count', 'dotsqrpress_core')
			. '</p></div>';
	}
	if (isset($_POST['reset_current'])) {
		update_option('dotsqrpress_login_filter_lockouts', array());
		echo '<div id="message" class="updated fade"><p>'
			. __('Cleared current lockouts', 'dotsqrpress_core')
			. '</p></div>';
	}
	if (isset($_POST['update_options'])) {
		global $dotsqrpress_login_filter;

		$dotsqrpress_login_filter['client_type'] = $_POST['client_type'];
		$dotsqrpress_login_filter['allowed_retries'] = $_POST['allowed_retries'];
		$dotsqrpress_login_filter['lockout_duration'] = $_POST['lockout_duration'] * 60;
		$dotsqrpress_login_filter['valid_duration'] = $_POST['valid_duration'] * 3600;
		$dotsqrpress_login_filter['allowed_lockouts'] = $_POST['allowed_lockouts'];
		$dotsqrpress_login_filter['long_duration'] = $_POST['long_duration'] * 3600;
		$dotsqrpress_login_filter['notify_email_after'] = $_POST['email_after'];
		$dotsqrpress_login_filter['cookies'] = (isset($_POST['cookies']) && $_POST['cookies'] == '1');

		$v = array();
		if (isset($_POST['lockout_notify_log'])) {
			$v[] = 'log';
		}
		if (isset($_POST['lockout_notify_email'])) {
			$v[] = 'email';
		}
		$dotsqrpress_login_filter['lockout_notify'] = implode(',', $v);

		dotsqrpress_login_filter_sanitize_variables();
		dotsqrpress_login_filter_update_options();
		echo '<div id="message" class="updated fade"><p>'
			. __('Options changed', 'dotsqrpress_core')
			. '</p></div>';
	}

	$lockouts_total = get_option('dotsqrpress_login_filter_lockouts_total', 0);
	$lockouts = get_option('dotsqrpress_login_filter_lockouts');
	$lockouts_now = is_array($lockouts) ? count($lockouts) : 0;

	$cookies_yes = dotsqrpress_login_filter_option('cookies') ? ' checked ' : '';
	$cookies_no = dotsqrpress_login_filter_option('cookies') ? '' : ' checked ';

	$client_type = dotsqrpress_login_filter_option('client_type');
	$client_type_direct = $client_type == DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS ? ' checked ' : '';
	$client_type_proxy = $client_type == DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS ? ' checked ' : '';

	$client_type_guess = dotsqrpress_login_filter_guess_proxy();

	if ($client_type_guess == DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS) {
		$client_type_message = sprintf(__('It appears the site is reached directly (from your IP: %s)','dotsqrpress_core'), dotsqrpress_login_filter_get_address(DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS));
	} else {
		$client_type_message = sprintf(__('It appears the site is reached through a proxy server (proxy IP: %s, your IP: %s)','dotsqrpress_core'), dotsqrpress_login_filter_get_address(DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS), dotsqrpress_login_filter_get_address(DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS));
	}
	$client_type_message .= '<br />';

	$client_type_warning = '';
	if ($client_type != $client_type_guess) {
		$faq = 'http://github.com/gallettigr/dotsqrpress';

		$client_type_warning = '<br /><br />' . sprintf(__('<strong>Current setting appears to be invalid</strong>. Please make sure it is correct. For further informations and issues please visit <a href="%s" title="DOTSQRPRESS on GitHub">project page</a>','dotsqrpress_core'), $faq);
	}

	$v = explode(',', dotsqrpress_login_filter_option('lockout_notify'));
	$log_checked = in_array('log', $v) ? ' checked ' : '';
	$email_checked = in_array('email', $v) ? ' checked ' : '';
	?>
	<div class="wrap">
	  <h2><?php echo __('Limit Login Attempts Settings','dotsqrpress_core'); ?></h2>
	  <h3><?php echo __('Statistics','dotsqrpress_core'); ?></h3>
	  <form action="options-general.php?page=dotsqrpress-login-filter" method="post">
		<?php wp_nonce_field('dotsqrpress-login-filter-options'); ?>
	    <table class="form-table">
		  <tr>
			<th scope="row" valign="top"><?php echo __('Total lockouts','dotsqrpress_core'); ?></th>
			<td>
			  <?php if ($lockouts_total > 0) { ?>
			  <input name="reset_total" value="<?php echo __('Reset Counter','dotsqrpress_core'); ?>" type="submit" />
			  <?php echo sprintf(_n('%d lockout since last reset', '%d lockouts since last reset', $lockouts_total, 'dotsqrpress_core'), $lockouts_total); ?>
			  <?php } else { echo __('No lockouts yet','dotsqrpress_core'); } ?>
			</td>
		  </tr>
		  <?php if ($lockouts_now > 0) { ?>
		  <tr>
			<th scope="row" valign="top"><?php echo __('Active lockouts','dotsqrpress_core'); ?></th>
			<td>
			  <input name="reset_current" value="<?php echo __('Restore Lockouts','dotsqrpress_core'); ?>" type="submit" />
			  <?php echo sprintf(__('%d IP is currently blocked from trying to log in','dotsqrpress_core'), $lockouts_now); ?>
			</td>
		  </tr>
		  <?php } ?>
		</table>
	  </form>
	  <h3><?php echo __('Options','dotsqrpress_core'); ?></h3>
	  <form action="options-general.php?page=dotsqrpress-login-filter" method="post">
		<?php wp_nonce_field('dotsqrpress-login-filter-options'); ?>
	    <table class="form-table">
		  <tr>
			<th scope="row" valign="top"><?php echo __('Lockout','dotsqrpress_core'); ?></th>
			<td>
			  <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('allowed_retries')); ?>" name="allowed_retries" /> <?php echo __('allowed retries','dotsqrpress_core'); ?> <br />
			  <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('lockout_duration')/60); ?>" name="lockout_duration" /> <?php echo __('minutes lockout','dotsqrpress_core'); ?> <br />
			  <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('allowed_lockouts')); ?>" name="allowed_lockouts" /> <?php echo __('lockouts increase lockout time to','dotsqrpress_core'); ?> <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('long_duration')/3600); ?>" name="long_duration" /> <?php echo __('hours','dotsqrpress_core'); ?> <br />
			  <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('valid_duration')/3600); ?>" name="valid_duration" /> <?php echo __('hours until retries are reset','dotsqrpress_core'); ?>
			</td>
		  </tr>
		  <tr>
			<th scope="row" valign="top"><?php echo __('Site connection','dotsqrpress_core'); ?></th>
			<td>
			  <?php echo $client_type_message; ?>
			  <label>
				<input type="radio" name="client_type"
					   <?php echo $client_type_direct; ?> value="<?php echo DOTSQRPRESS_LOGIN_FILTER_DIRECT_LOGINS; ?>" />
					   <?php echo __('Direct connection','dotsqrpress_core'); ?>
			  </label>
			  <label>
				<input type="radio" name="client_type"
					   <?php echo $client_type_proxy; ?> value="<?php echo DOTSQRPRESS_LOGIN_FILTER_REMOTE_LOGINS; ?>" />
				  <?php echo __('From behind a reversy proxy','dotsqrpress_core'); ?>
			  </label>
			  <?php echo $client_type_warning; ?>
			</td>
		  </tr>
		  <tr>
			<th scope="row" valign="top"><?php echo __('Handle cookie login','dotsqrpress_core'); ?></th>
			<td>
			  <label><input type="radio" name="cookies" <?php echo $cookies_yes; ?> value="1" /> <?php echo __('Yes','dotsqrpress_core'); ?></label> <label><input type="radio" name="cookies" <?php echo $cookies_no; ?> value="0" /> <?php echo __('No','dotsqrpress_core'); ?></label>
			</td>
		  </tr>
		  <tr>
			<th scope="row" valign="top"><?php echo __('Notify on lockout','dotsqrpress_core'); ?></th>
			<td>
			  <input type="checkbox" name="lockout_notify_log" <?php echo $log_checked; ?> value="log" /> <?php echo __('Log IP','dotsqrpress_core'); ?><br />
			  <input type="checkbox" name="lockout_notify_email" <?php echo $email_checked; ?> value="email" /> <?php echo __('Email to admin after','dotsqrpress_core'); ?> <input type="text" size="3" maxlength="4" value="<?php echo(dotsqrpress_login_filter_option('notify_email_after')); ?>" name="email_after" /> <?php echo __('lockouts','dotsqrpress_core'); ?>
			</td>
		  </tr>
		</table>
		<p class="submit">
		  <input name="update_options" value="<?php echo __('Change Options','dotsqrpress_core'); ?>" type="submit" />
		</p>
	  </form>
	  <?php
		$log = get_option('dotsqrpress_login_filter_logged');

		if (is_array($log) && count($log) > 0) {
	  ?>
	  <h3><?php echo __('Lockout log','dotsqrpress_core'); ?></h3>
	  <form action="options-general.php?page=dotsqrpress-login-filter" method="post">
		<?php wp_nonce_field('dotsqrpress-login-filter-options'); ?>
		<input type="hidden" value="true" name="clear_log" />
		<p class="submit">
		  <input name="submit" value="<?php echo __('Clear Log','dotsqrpress_core'); ?>" type="submit" />
		</p>
	  </form>
	  <style type="text/css" media="screen">
		.limit-login-log th {
			font-weight: bold;
		}
		.limit-login-log td, .limit-login-log th {
			padding: 1px 5px 1px 5px;
		}
		td.limit-login-ip {
			font-family:  "Courier New", Courier, monospace;
			vertical-align: top;
		}
		td.limit-login-max {
			width: 100%;
		}
	  </style>
	  <div class="limit-login-log">
		<table class="form-table">
		  <?php dotsqrpress_login_filter_show_log($log); ?>
		</table>
	  </div>
	  <?php
		}
	  ?>
	</div>
	<?php
}
?>
