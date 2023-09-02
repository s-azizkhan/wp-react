<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/s-azizkhan
 * @since             1.0.0
 * @package           Wp_React
 *
 * @wordpress-plugin
 * Plugin Name:       WP React
 * Plugin URI:        https://github.com/s-azizkhan/wp-react
 * Description:       "WP React" is a WordPress plugin designed to seamlessly integrate ReactJS into your WordPress website. It allows you to easily add, deploy, and manage React components, enhancing the interactivity and user experience of your site
 * Version:           1.0.0
 * Author:            Aziz Khan
 * Author URI:        https://github.com/s-azizkhan
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-react
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_REACT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-react-activator.php
 */
function activate_wp_react() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-react-activator.php';
	Wp_React_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-react-deactivator.php
 */
function deactivate_wp_react() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-react-deactivator.php';
	Wp_React_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_react' );
register_deactivation_hook( __FILE__, 'deactivate_wp_react' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-react.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_react() {

	$plugin = new Wp_React();
	$plugin->run();

}
run_wp_react();
