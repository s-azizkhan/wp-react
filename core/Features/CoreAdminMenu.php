<?php

namespace SAzizKhan\WpReactKit\Features;

use SAzizKhan\WpReactKit\ClassLoader;

defined('ABSPATH') || exit;

class CoreAdminMenu extends ClassLoader
{
    public $menu_slug =  WP_REACT_KIT_TEXTDOMAIN. '-settings';

    public function run()
    {
        add_action('admin_menu', [$this, 'register_menu']);
    }

    public function register_menu()
    {
        add_menu_page(
            $this->plugin_name,            // Page Title
            $this->plugin_name,            // Menu Title
            'manage_options',          // Capability (who can access)
            $this->menu_slug,   // Menu Slug (unique identifier)
            [$this, 'render_settings_page'], // Callback function to display the page
            'dashicons-admin-plugins',  // Icon URL or Dashicons class
            60
        );

        do_action('wp_react_kit_admin_settings_menu', $this);
    }

    function render_settings_page()
    {
        // Content of our plugin's admin page
        echo '<div class="wrap">';
        echo '<h1>Say Hello to WP React Kit Settings 🌟</h1>';
        // Add our settings form or content here
        echo '</div>';
    }
}
