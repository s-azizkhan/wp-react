<?php

namespace SAzizKhan\WpReactKit\ShortCodes;

defined('ABSPATH') || exit;

/**
 * Abstract class for WordPress shortcode.
 * 
 * @link github.com/s-azizkhan
 * @since 3.6.0
 * @author S.Aziz Khan <sakatazizkhan1@gmail.com>
 */
abstract class WpReactKitShortCode
{
    /**
     * Run the shortcode or not
     *
     * @var bool
     */
    protected $run = true;

    /**
     * The shortcode ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The shortcode prefix.
     *
     * @var string
     */
    protected $prefix = WP_REACT_KIT_SHORTNAME . '_';

    /**
     * Constructor.
     *
     * @param string $id The shortcode ID.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the shortcode ID.
     *
     * @return string
     */
    public function get_id()
    {
        return $this->prefix . $this->id;
    }

    /**
     * Register the shortcode.
     *
     * @wp-hook init
     */
    public function load()
    {
        if ($this->run) {
            add_shortcode($this->get_id(), [$this, 'handle_shortcode']);
        }
    }

    /**
     * Method for retrieving the shortcode attributes
     *
     */
    protected function getAttributes($atts)
    {
        $default_atts = $this->get_default_attributes(); // Get default attributes

        // Merge default and user-defined attributes
        $attributes = shortcode_atts($default_atts, $atts, $this->get_id());

        return $attributes;
    }

    /**
     * Method for rendering the shortcode output
     *
     * @param {*} $content
     * @return void
     */
    protected function renderOutput($content)
    {
        // Implement your shortcode rendering logic here
        // You can use the $content and $attributes variables

        // Example: Return the rendered output as HTML

        // Start output buffering.
        ob_start();

        echo $content;
        // Return the output buffer contents.
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Handle the shortcode.
     *
     * @param array $attr The shortcode attributes.
     * @return string
     */
    abstract function handle_shortcode($atts, $content, $tag);

    /**
     * Abstract method for defining default attributes
     *
     * @return void
     */
    abstract protected function get_default_attributes();
}
