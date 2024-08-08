<?php

namespace SAzizKhan\WpReactKit\Features;

use WC_Admin_Settings;

defined('ABSPATH') || exit;

/**
 * Class ReactPageSetup
 * @link github.com/s-azizkhan
 * @since 1.0.0
 * @version 0.1.4
 * @package SAzizKhan\WpReactKit\Feature
 */
class ReactPageSetup
{
    /**
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     * @var string $prefix
     */
    public static $prefix = 'lwmc';

    private $page_name = 'React Page Setup';
    public $setting_slug;
    private $page_slug;

    /**
     * Define the static variable to store the option names
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public static $optionNames = array(
        'react_design_select_page_id',
        'react_payment_success_page_id',
        'react_profile_info_page_id',
        'react_thank_you_page_id',
        'react_custom_design_page_id',
        'react_template_design_page_id',
        'react_template_design_confirmation_page_id'
    );

    /**
     * Action constructor.
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public function __construct()
    {  
    }

    public function run()
    {
        $this->setting_slug = self::$prefix . '_react_page_setup';
        $this->page_slug = self::$prefix . '-react-page-setup';
        $this->actions_init();
    }

    /**
     * Register the actions
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     * @return void
     */
    public function actions_init()
    {
        # Add menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        # Handle form requests.
        add_action('admin_init', array($this, 'handle_form_requests'));
        # Handle register fields
        add_action('admin_init', array($this, 'register_settings_fields'));
    }
    
    /**
     * Add react page setup page in admin
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     * @return void
     */
    public function add_admin_menu()
    {
        add_submenu_page('lwmc-settings', $this->page_name, $this->page_name, 'manage_woocommerce', $this->page_slug, array($this, 'render_settings_page'));
    }

    /**
     * Render Settings page
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public function render_settings_page()
    {
        include get_stylesheet_directory() . '/app/views/react-page-setup.php';
    }

    /**
     * Handle form submission request
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public function handle_form_requests()
    {
        if (isset($_POST['option_page']) && $_POST['option_page'] == $this->setting_slug) {
            return $this->configure_admin();
        }
    }

    /**
     * configure admin settings
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public function configure_admin()
    {
        $url = admin_url('admin.php?page=lwmc-react-page-setup');

        foreach (self::$optionNames as $optionName) {
            $fullOptionName = self::$prefix .'_'. $optionName;
            if (isset($_POST[$fullOptionName])) {
                $optionValue = sanitize_text_field($_POST[$fullOptionName]);
                if(get_post_type($optionValue) === "page"){
                    update_option($fullOptionName, $optionValue);
                }
            }
        }

        # Redirect to swap role config page
        wp_redirect($url);
        exit;
    }

    /**
     * Register a setting fields
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */
    public function register_settings_fields()
    {
        // Add a section to the setting
        add_settings_section(
            $this->setting_slug . '_configuration_section',
            __('Page Setup', 'woocommerce'),
            array($this, 'display_setting_description'),
            $this->setting_slug
        );

        $fields = array(
            array(
                'name' => __('Design Select Page URL', 'woocommerce'),
                'id' => 'design_select_page_id',
                'description' => __('Design Select Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Payment Success Page URL', 'woocommerce'),
                'id' => 'payment_success_page_id',
                'description' => __('Payment Success Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Profile Info Page URL', 'woocommerce'),
                'id' => 'profile_info_page_id',
                'description' => __('Profile Info Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Thank You Page URL', 'woocommerce'),
                'id' => 'thank_you_page_id',
                'description' => __('Thank You Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Custom Design Page URL', 'woocommerce'),
                'id' => 'custom_design_page_id',
                'description' => __('Custom Design Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Template Design Page URL', 'woocommerce'),
                'id' => 'template_design_page_id',
                'description' => __('Template Design Page URL.', 'woocommerce'),
            ),
            array(
                'name' => __('Template Design Confirmation Page URL', 'woocommerce'),
                'id' => 'template_design_confirmation_page_id',
                'description' => __('Template Design Confirmation Page URL.', 'woocommerce'),
            )
        );
        
        foreach ($fields as $field) {
            $args = array(
                'type' => 'select',
                'id' => self::$prefix . '_react' . '_' . $field['id'],
                'name' => self::$prefix . '_react' . '_' . $field['id'],
                'required' => true,
                'get_options_list' => '',
                'value_type' => 'normal',
                'wp_data' => 'option',
                'description' => __($field['name'], 'woocommerce'),
            );

            // Add a field to the setting
            add_settings_field(
                self::$prefix . '_react' . '_' . $field['id'],
                // __($field['name'], 'woocommerce'),
                '',
                array($this, 'render_settings_field'),
                $this->setting_slug,
                $this->setting_slug . '_configuration_section',
                $args
            );
        }

        // Register a setting
        register_setting(
            $this->setting_slug,
            $this->setting_slug . '_configuration'
        );
    }

    /**
     * Render the settings field.
     * @since 1.0.0
     * @version 0.1.4
     * @param array $args The field arguments.
     */
    function render_settings_field($args)
    {
        $settings = array(
            array(
                'name'              => $args['description'],
                'id'                => $args['id'],
                'type'              => 'single_select_page',
                'selected'          => '',
                'sort_column'       => 'menu_order',
                'sort_order'        => 'ASC',
                'echo'              => false,
                'post_status'       => 'publish',
                'class'             => 'wc-page-search',
            ),
		);
		$settings = apply_filters( 'woocommerce_settings_pages', $settings );
		if ( wc_site_is_https() ) {
			unset( $settings['unforce_ssl_checkout'], $settings['force_ssl_checkout'] );
		}
        WC_Admin_Settings::output_fields( $settings );
    }

    /**
     * Display description of the page setup configuration
     * @since 1.0.0
     * @version 0.1.4
     * @access public
     */  
    public function display_setting_description()
    {
        echo "<p>". _e('Manage and save all page links in one place', 'woocommerce')."</p>";
    }

    /**
     * Retrieves the URL value of a specific option key.
     * @since 1.0.0
     * @version 0.1.4
     * @param string $key The key of the option to retrieve the URL value for.
     * @return string The URL value of the option.
    */  
    public static function get_react_app_page_url($key)
    {
        $optionName = self::$prefix .'_'.$key;
        $optionValue = get_option($optionName);
        $page_id = absint($optionValue);
        if ($page_id > 0 && 'publish' === get_post_status($page_id)) {
            $url = get_permalink($page_id);
            return $url;
        }
        return null;
    }

    /**
     * Retrieves all URLs associated with specific option names and returns them as an array.
     * @since 1.0.0
     * @version 0.1.4
     * @return array An array of URLs with option names as keys and their corresponding URLs as values.
    */  
    public static function get_react_app_page_urls()
    {
        $optionUrls = array();
        foreach (self::$optionNames as $optionName) {
            $url = self::get_react_app_page_url($optionName);
            $optionUrls[$optionName] = $url;
        }
        return $optionUrls;
    }


}
