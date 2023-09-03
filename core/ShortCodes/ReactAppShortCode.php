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
            'injectId' => false,
            'filePath' => 'index.js',
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
        //$id = $attributes['id'];
        $html = '<h1>React App</h1>';

        // Set the template slug.
        //$slug = "template-parts/content/content";

        //// Set the template name based on the shortcode attributes.
        //if (!$id) {
        //    $id = "react-app";
        //    $template_name = "{$slug}-react-app.php";
        //} else {
        //    $id = "react-app-{$id}";
        //    $template_name = "{$slug}-{$id}.php";
        //}

        //// Remove any spaces from the ID and template name.
        //$id = str_replace(' ', '', $id);
        //$template_name = str_replace(' ', '', $template_name);


        //// Check if template file exists.
        //if (!file_exists(STYLESHEETPATH . '/' . $template_name)) {
        //    // Log error and output div with id attribute.
        //    error_log("File not found with $id");
        //    $html_id = $id;
        //    $html .= "<div id='{$html_id}'></div>";
        //} else {
        //    // Log error and output div with id attribute.
        //    $html .= get_template_part($slug, $id);
        //}
        // Render the output
        return $this->renderOutput($html);
    }
}
