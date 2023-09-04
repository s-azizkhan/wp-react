<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/s-azizkhan
 * @since      1.0.0
 *
 * @package    Wp_React_Kit
 * @subpackage Wp_React_Kit/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_React_Kit
 * @subpackage Wp_React_Kit/public
 * @author     Aziz Khan <sakatazizkhan1@gmail.com>
 */
class Wp_React_Kit_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_React_Kit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_React_Kit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-react-kit-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_React_Kit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_React_Kit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// check if the current template is wp-react-kit-app-template.php then enqueue the script
		wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-react-kit-public.js', array('jquery'), $this->version, false);

		// localize the plugin's resources url
		wp_localize_script($this->plugin_name, 'wpReactKit', [
			'nonce' => wp_create_nonce('wp_react_kit_nonce'),
			'pluginUrl' => WP_REACT_KIT_PLUGIN_URL
		]);
	}
	// Disable emojis
	function disable_wp_emojicons()
	{
		// Remove the emoji scripts and styles
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');

		// Remove the TinyMCE emojis
		add_filter('tiny_mce_plugins', [$this, 'disable_emojicons_tinymce']);
	}

	function disable_emojicons_tinymce($plugins)
	{
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		} else {
			return array();
		}
	}
}
