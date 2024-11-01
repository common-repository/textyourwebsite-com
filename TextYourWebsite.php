<?php
/**
 * Plugin Name: TextYourWebsite.com
 * Description: Update your website with a text message!
 * Version: 1.1
 * Author: textyourwebsite
 * Author URI: https://www.textyourwebsite.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define( 'textyourwebsite_PLUGIN_FILE', __FILE__ );

/**
 * Loads the action plugin
 */
require_once dirname( textyourwebsite_PLUGIN_FILE ) . '/includes/textyourwebsite_Main.php';

textyourwebsite_Main::instance();

register_activation_hook( textyourwebsite_PLUGIN_FILE, array( 'textyourwebsite_Main', 'activate' ) );

register_deactivation_hook( textyourwebsite_PLUGIN_FILE, array( 'textyourwebsite_Main', 'deactivate' ) );

register_uninstall_hook( textyourwebsite_PLUGIN_FILE, array( 'textyourwebsite_Main', 'uninstall' ) );