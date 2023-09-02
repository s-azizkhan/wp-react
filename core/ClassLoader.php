<?php

namespace SAzizKhan\WpReactKit;

defined('ABSPATH') || exit;

abstract class ClassLoader
{
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    protected $plugin_domain;

    public function init()
    {
        if (defined('WP_REACT_KIT_VERSION')) {
            $this->version = WP_REACT_KIT_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_domain = WP_REACT_KIT_TEXTDOMAIN;

        $this->plugin_name = 'Wp React Kit';
    }
    public function load()
    {
        $this->init();
        
        $this->run();
    }
    abstract protected function run();
}
