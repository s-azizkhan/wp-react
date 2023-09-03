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
 * @package           Wp_React_Kit
 *
 * @wordpress-plugin
 * Plugin Name:       WP React Kit
 * Plugin URI:        https://github.com/s-azizkhan/wp-react-kit
 * Description:       WP React Kit empowers your WordPress website with the dynamic capabilities of ReactJS. Easily integrate and manage interactive React components, enhancing your site's user experience with advanced interactivity and responsiveness.
 * Version:           1.0.0
 * Author:            Aziz Khan
 * Author URI:        https://github.com/s-azizkhan
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-react-kit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die('We\'re sorry, but you can not directly access this file.');
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_REACT_KIT_VERSION', '1.0.0');

define('WP_REACT_KIT_SHORTNAME', 'wrk');
define('WP_REACT_KIT_TEXTDOMAIN', 'wp-react-kit');
define('WP_REACT_KIT_NAME', 'WP React Kit');
define('WP_REACT_KIT_PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('WP_REACT_KIT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_REACT_KIT_PLUGIN_ABSOLUTE', __FILE__);
define('WP_REACT_KIT_MIN_PHP_VERSION', '7.4');
define('WP_REACT_KIT_WP_VERSION', '6.3');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-react-kit-activator.php
 */
function activate_wp_react_kit()
{
	add_action(
		'init',
		static function () {
			load_plugin_textdomain(WP_REACT_KIT_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
		}
	);

	if (version_compare(PHP_VERSION, WP_REACT_KIT_MIN_PHP_VERSION, '<=')) {
		add_action(
			'admin_init',
			static function () {
				deactivate_plugins(plugin_basename(__FILE__));
			}
		);
		add_action(
			'admin_notices',
			static function () {
				echo wp_kses_post(
					sprintf(
						'<div class="notice notice-error"><p>%s</p></div>',
						__('"WP React Kit" requires PHP 7.4 or newer.', WP_REACT_KIT_TEXTDOMAIN)
					)
				);
			}
		);

		// Return early to prevent loading the plugin.
		return;
	}

	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-react-kit-activator.php';
	Wp_React_Kit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-react-kit-deactivator.php
 */
function deactivate_wp_react_kit()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-react-kit-deactivator.php';
	Wp_React_Kit_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_react_kit');
register_deactivation_hook(__FILE__, 'deactivate_wp_react_kit');

$wp_react_kit_libraries = require WP_REACT_KIT_PLUGIN_ROOT . 'vendor/autoload.php'; //phpcs:ignore

$requirements = new \Micropackage\Requirements\Requirements(
	'Wp React Kit',
	array(
		'php'            => WP_REACT_KIT_MIN_PHP_VERSION,
		'php_extensions' => array('mbstring'),
		'wp'             => WP_REACT_KIT_WP_VERSION,
		//'plugins'            => array(
		//	array('file' => 'hello-dolly/hello.php', 'name' => 'Hello Dolly', 'version' => '1.5')
		//),
	)
);

if (!$requirements->satisfied()) {
	$requirements->print_notice();

	return;
}
// Documentation to integrate GitHub, GitLab or BitBucket https://github.com/YahnisElsts/plugin-update-checker/blob/master/README.md
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker('https://github.com/s-azizkhan/wp-react-kit', __FILE__, 'wp-react-kit');
//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-react-kit.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_react_kit()
{

	$plugin = new Wp_React_Kit();
	$plugin->run();

	(new \SAzizKhan\WpReactKit\WpReactKitInit())->run();
}
run_wp_react_kit();
