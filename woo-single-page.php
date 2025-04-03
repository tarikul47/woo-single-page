<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://onlytarikul.com
 * @since             1.0.0
 * @package           Woo_Single_Page
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Single Page
 * Plugin URI:        https://onlytarikul.com
 * Description:       Take full control of your WooCommerce single product pages with Woo Single Page! This powerful plugin allows you to customize product pages and add extra addon products seamlessly.
 * Version:           1.0.0
 * Author:            Tarikul Islam
 * Author URI:        https://onlytarikul.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-single-page
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WOO_SINGLE_PAGE_VERSION', '1.0.0');
define('WOO_SINGLE_PAGE_PATH', plugin_dir_path(__FILE__));
define('WOO_SINGLE_PAGE_URL', plugin_dir_url( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-single-page-activator.php
 */
function activate_woo_single_page()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-woo-single-page-activator.php';
	Woo_Single_Page_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-single-page-deactivator.php
 */
function deactivate_woo_single_page()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-woo-single-page-deactivator.php';
	Woo_Single_Page_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woo_single_page');
register_deactivation_hook(__FILE__, 'deactivate_woo_single_page');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woo-single-page.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_single_page()
{

	$plugin = new Woo_Single_Page();
	$plugin->run();

}
run_woo_single_page();

