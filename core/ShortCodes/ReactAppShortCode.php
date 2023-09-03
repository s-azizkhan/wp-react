<?php

namespace SAzizKhan\WpReactKit\ShortCodes;

defined('ABSPATH') || exit;


/**
 * Class ReactAppShortCode
 *
 * @link github.com/s-azizkhan
 * @since 1.1.0
 * @package SAzizKhan\WpReactKit\ShortCodes
 * @author S.Aziz Khan <sakatazizkhan1@gmail.com>
 */
class ReactAppShortCode  extends WpReactKitShortCode
{

    /**
     * ReactAppShortCode constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        parent::__construct('wp_react_kit_app');
    }

    protected function get_default_attributes()
    {
        return [
            'inject_id' => 'root',
        ];
    }

    /**
     * Handle the wrk_react_app shortcode.
     *
     * @param array $attr ShortCode attributes.
     * @return string HTML content.
     */
    public function handle_shortcode($atts, $content, $tag)
    {
        $attributes = $this->getAttributes($atts);
        $html = '';
        $html .= "<div id='{$attributes['inject_id']}'></div>";
        // Render the output
        return $this->renderOutput($html);
    }

    public static function get_execute($post)
    {
        $inject_id = $post->__get('wp_react_kit_inject_id');
        $code = '[wp_react_kit_app inject_id="' . $inject_id . '"]';

        do_shortcode($code);
        return;
    }
}
