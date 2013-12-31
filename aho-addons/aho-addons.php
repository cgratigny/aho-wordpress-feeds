<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Aho_Addons
 * @author    Brandon Hansen <bh@jesusculture.com>
 * @license   GPL-2.0+
 * @copyright Abundant Harvest Organics
 *
 * @wordpress-plugin
 * Plugin Name:       Aho Addons
 * Plugin URI:        https://my.abundantharvestorganics.com/addons.json
 * Description:       Display the addons for the given week
 * Version:           1.0.0
 * Author:            Brandon Hansen
 * Author URI:        https://my.abundantharvestorganics.com
 * Text Domain:       aho-addons
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/ready4god2513/aho-addons
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/aho_addons.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Aho_Addons with the name of the class defined in
 *   `class-plugin-name.php`
 */
register_activation_hook( __FILE__, array( 'Aho_Addons', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Aho_Addons', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Aho_Addons with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'Aho_Addons', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Aho_Addons_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/aho_addons_admin.php' );
	add_action( 'plugins_loaded', array( 'Aho_Addons_Admin', 'get_instance' ) );

}
