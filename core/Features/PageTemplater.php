<?php

namespace SAzizKhan\WpReactKit\Features;

use SAzizKhan\WpReactKit\ClassLoader;

/**
 * Class PageTemplater
 *
 * @link github.com/s-azizkhan
 * @since 1.1.0
 * @package SAzizKhan\WpReactKit\Features
 * @author S.Aziz Khan <sakatazizkhan1@gmail.com>
 */
class PageTemplater extends ClassLoader
{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    public function run()
    {
        add_action('plugins_loaded', [$this, 'template_init']);
    }

    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new PageTemplater();
        }

        return self::$instance;
    }

    /**
     * Converts a filename to a human-readable format.
     *
     * @param string $filename The filename to convert.
     * @return string The human-readable filename.
     */
    function convert_filename_to_human_readable($filename) {
        // Remove file extension
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        // replace -template prefix
        $filename = str_replace('-template', '', $filename);
    
        // Replace hyphens with spaces and capitalize each word
        $human_readable = ucwords(str_replace('-', ' ', $filename));
    
        return $human_readable;
    }

    /**
     * Retrieves a list of files in the templates directory.
     *
     * @return array|false The list of files in the templates directory, or false if the directory is not found or not readable.
     */
    function list_files_in_templates()
    {
        $directory_path = WP_REACT_KIT_PLUGIN_ROOT . '/templates';
        // Ensure the directory exists and is readable
        if (is_dir($directory_path) && is_readable($directory_path)) {
            // Get the list of files in the directory
            $files = scandir($directory_path);

            // Remove "." and ".." from the list (current directory and parent directory)
            $files = array_values(array_diff($files, array('.', '..')));

            // add the template name to the template by looping through the array
            foreach ($files as $key => $value) {
                unset($files[$key]);
                $files[$value] = $this->convert_filename_to_human_readable($value);
            }

            return $files;
        } else {
            return false; // Directory not found or not readable
        }
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    public function template_init()
    {

        $this->templates = array();


        // Add a filter to the attributes metabox to inject template into the cache.
        if (version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {

            // 4.6 and older
            add_filter(
                'page_attributes_dropdown_pages_args',
                array($this, 'register_project_templates')
            );
        } else {

            // Add a filter to the wp 4.7 version attributes metabox
            add_filter(
                'theme_page_templates',
                array($this, 'add_new_template')
            );
        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
            'wp_insert_post_data',
            array($this, 'register_project_templates')
        );


        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter(
            'template_include',
            array($this, 'view_project_template')
        );

        // Add our templates to this array.
        //$this->templates = array(
        //    'wp-react-kit-app-template.php' => WP_REACT_KIT_NAME, // main template
        //);
        $this->templates = $this->list_files_in_templates();
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     *
     */
    public function add_new_template($posts_templates)
    {
        $posts_templates = array_merge($posts_templates, $this->templates);
        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates($atts)
    {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cache_key, 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, $this->templates);

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add($cache_key, $templates, 'themes', 1800);

        return $atts;
    }

    /**
     * Checks if the template is assigned to the page
     */
    public function view_project_template($template)
    {
        // Return the search template if we're searching (instead of the template for the first result)
        if (is_search()) {
            return $template;
        }

        // Get global post
        global $post;

        // Return template if post is empty
        if (!$post) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if (!isset($this->templates[get_post_meta(
            $post->ID,
            '_wp_page_template',
            true
        )])) {
            return $template;
        }

        // Allows filtering of file path
        $filepath = apply_filters('wp_react_kit_page_templater_dir_path', WP_REACT_KIT_PLUGIN_ROOT . 'templates/');

        $file =  $filepath . get_post_meta(
            $post->ID,
            '_wp_page_template',
            true
        );

        // Just to be safe, we check if the file exist first
        if (file_exists($file)) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;
    }
}
