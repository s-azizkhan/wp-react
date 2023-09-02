<?php

namespace SAzizKhan\WpReactKit\ShortCodes;

defined('ABSPATH') || exit;

/**
 * Class ShortcodeInit
 *
 * @link github.com/s-azizkhan
 * @since 1.1.0
 * @package SAzizKhan\WpReactKit\ShortCodes
 * @author S.Aziz Khan <sakatazizkhan1@gmail.com>
 */
class ShortcodeInit
{
    /**
     * Loads the shortcodes.
     *
     * This method creates new instances of shortcode handlers.
     */
    public function load()
    {
        foreach ($this->get_shortcodes() as $short_code => $handler_class) {
            (new $handler_class())->load();
        }
    }

    /**
     * Gets the shortcodes.
     *
     * This method returns a dictionary of shortcodes, where the key is the shortcode name and the value is the class name for the shortcode.
     *
     * @return array A dictionary of shortcodes.
     */
    public function get_shortcodes()
    {
        // Create a dictionary of shortcodes.
        $shortcodes = [];

        // Add the shortcodes to the dictionary.
        $shortcodes['wp_react_kit_app'] = ReactAppShortCode::class;
        // Return the dictionary of shortcodes.
        return $shortcodes;
    }
}

