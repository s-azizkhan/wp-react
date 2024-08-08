<?php

namespace SAzizKhan\WpReactKit\Features;

defined('ABSPATH') || exit;

/**
 * Class ReactInjection
 *
 * @link github.com/s-azizkhan
 * @since 1.0.0
 * @package SAzizKhan\WpReactKit\Features
 * @author S.Aziz Khan <biseswari.jena@logicwind.com>
 */
class ReactInjection
{
    public function __construct()
    {
    }

    public function run()
    {
        $this->actions_init();
    }

    /**
     * Register the actions
     *
     * @return void
     */
    public function actions_init()
    {
        # Added 'inject_id' box
        add_action('add_meta_boxes', [$this, 'add_injection_meta_box']);
        # To save the value entered in the text input field
        add_action('save_post', [$this, 'save_inject_id_value']);

        # Inject files to the page
        add_action('wp_react_kit_app_head', [$this, 'enquire_files']);
    }

    function enquire_files($post)
    {
        $selected_files = $post->__get('selected_wp_react_kit_file_path');
        if (!is_array($selected_files)) {
            return;
        }
        $build_folder = ReactBuildManager::get_build_dir();
        $build_url = ReactBuildManager::get_build_url();
        // loop through the selected files & create full url
        for ($i = 0; $i < count($selected_files); $i++) {
            $full_path = $build_folder . $selected_files[$i];
            if (!file_exists($full_path)) {
                continue;
            }
            $selected_files[$i] = $build_url . $selected_files[$i];
        }

        // if array is empty do nothing
        if (empty($selected_files)) {
            return;
        }
        wp_enqueue_script(WP_REACT_KIT_TEXTDOMAIN); // enqueue the main public script
        wp_enqueue_script('react');
        wp_enqueue_script('react-dom');

        for ($i = 0; $i < count($selected_files); $i++) {
            // check is file JS or CSS
            if (strpos($selected_files[$i], '.js') !== false) {
                wp_enqueue_script('wp_react_kit_injection-' . $i, $selected_files[$i], [], WP_REACT_KIT_VERSION, true);
            } else if (strpos($selected_files[$i], '.css') !== false) {
                wp_enqueue_style('wp_react_kit_injection-' . $i, $selected_files[$i], [], WP_REACT_KIT_VERSION, 'all');
            }
        }
    }

    /**
     * Added 'inject_id' box.
     */
    function add_injection_meta_box()
    {
        $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
        // Check if the page template is 'wp-react-kit-app-template.php'
        //if ($page_template === 'wp-react-kit-app-template.php') {
        add_meta_box(
            'wp_react_kit_injection',
            'Wp React Kit Injection',
            array($this, 'render_react_injection_page_meta_box'),
            'page',
            'side',
            'default'
        );
        //}
    }

    /**
     * Define the callback function 'render_react_injection_page_meta_box' to render the text input field inside the meta box.
     */
    function render_react_injection_page_meta_box($post)
    {

        //// Check if the selected page template is 'wp-react-kit-app-template.php'
        //$page_template = get_post_meta($post->ID, '_wp_page_template', true);

        //if ($page_template === 'wp-react-kit-app-template.php') {
        $react_inject_id = get_post_meta($post->ID, 'wp_react_kit_inject_id', true);
        // Retrieve the selected build files (if any) for the current post
        $selected_files = get_post_meta($post->ID, 'selected_wp_react_kit_file_path', true);
        if (!is_array($selected_files)) {
            $selected_files = array();
        }
        // Retrieve the list of files in the "build" directory
        $build_folder = ReactBuildManager::get_build_dir(); // Replace with the actual path
        $wp_react_kit_file_path = list_files_recursively($build_folder, array('js', 'css'), 'dist');

?>
        <div>

            <div>
                <label for="wp_react_kit_inject_id">Inject HTML ID:</label>
                <input class="placeholder-shown:border-gray-500" placeholder="Enter you html inject ID" type="text" id=iwp_react_kit_inject_id" name="wp_react_kit_inject_id" value="<?php echo esc_attr($react_inject_id); ?>" />
            </div>

            <div>
                <label for="wp_react_kit_file_path">
                    Inject HTML ID:
                    <select style="width: 50%" class="select2 js-states form-control" id="wp_react_kit_file_path" name="wp_react_kit_file_path[]" multiple="multiple">
                        <?php

                        foreach ($wp_react_kit_file_path as $file) {
                            // Convert the file path to a relative path inside the "build" folder
                            $relative_path = str_replace($build_folder . '/', '', $file);

                            echo '<option value="' . esc_attr($relative_path) . '" ';
                            if (in_array($relative_path, $selected_files)) {
                                echo 'selected';
                            }
                            echo '>' . esc_html($relative_path) . '</option>';
                        }

                        echo '</select>';
                        ?>
                        <script>
                            jQuery(function($) {
                                // Convert the select element into a jQuery UI multiselect widget
                                $('.select2').select2({
                                    placeholder: "Select assets",
                                    theme: "classic",
                                    allowClear: true,
                                });
                            });
                        </script>
                </label>
            </div>
        </div>
<?php
        //}
    }

    /**
     * To save the value entered in the text input field.
     */
    function save_inject_id_value($post_id)
    {
        if (array_key_exists('wp_react_kit_inject_id', $_POST)) {
            update_post_meta(
                $post_id,
                'wp_react_kit_inject_id',
                sanitize_text_field($_POST['wp_react_kit_inject_id'])
            );
        }

        $this->save_files_path($post_id);
    }

    // Save the selected build files when the page is updated
    function save_files_path($post_id)
    {
        if (isset($_POST['wp_react_kit_file_path']) && is_array($_POST['wp_react_kit_file_path'])) {
            // Sanitize and save the selected build files as post meta data
            $selected_files = array_map('sanitize_text_field', $_POST['wp_react_kit_file_path']);
            update_post_meta($post_id, 'selected_wp_react_kit_file_path', $selected_files);
        } else {
            // If no files are selected, remove the post meta data
            delete_post_meta($post_id, 'selected_wp_react_kit_file_path');
        }
    }
}
