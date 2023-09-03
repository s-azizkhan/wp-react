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
        add_action('add_meta_boxes', [$this, 'add_inject_id']);
        # To save the value entered in the text input field
        add_action('save_post', [$this, 'save_inject_id_value']);
    }

    /**
     * Added 'inject_id' box.
     */
    function add_inject_id()
    {
        $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
        // Check if the page template is 'wp-react-kit-app-template.php'
        if ($page_template === 'wp-react-kit-app-template.php') {
            add_meta_box(
                'wp_react_kit_injection',
                'Wp React Kit Injection',
                array($this, 'render_react_injection_page_meta_box'),
                'page',
                'side',
                'default'
            );
        }
    }

    /**
     * Define the callback function 'render_react_injection_page_meta_box' to render the text input field inside the meta box.
     */
    function render_react_injection_page_meta_box($post)
    {

        // Check if the selected page template is 'wp-react-kit-app-template.php'
        $page_template = get_post_meta($post->ID, '_wp_page_template', true);

        if ($page_template === 'wp-react-kit-app-template.php') {
            $react_inject_id = get_post_meta($post->ID, 'wp_react_kit_inject_id', true);
            // Retrieve the selected build files (if any) for the current post
            $selected_files = get_post_meta($post->ID, 'selected_wp_react_kit_file_path', true);
            if (!is_array($selected_files)) {
                $selected_files = array();
            }
            // Retrieve the list of files in the "build" directory
            $build_folder = ReactBuildManager::get_build_dir(); // Replace with the actual path
            $wp_react_kit_file_path = list_files_recursively($build_folder, array('js', 'css'));

?>
            <div>

                <div>
                    <label for="wp_react_kit_inject_id">FIle Path:</label>
                    <input type="text" id=iwp_react_kit_inject_id" name="wp_react_kit_inject_id" value="<?php echo esc_attr($react_inject_id); ?>" />
                </div>

                <div>
                    <label for="wp_react_kit_file_path">File Path:</label>
                    <?php
                    echo '<select class="select2" id="wp_react_kit_file_path" name="wp_react_kit_file_path[]" mulitiple>';
                    echo '<option value="">Select a file</option>';

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
                </div>
            </div>
<?php
        }
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
