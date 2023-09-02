<?php

namespace SAzizKhan\WpReactKit\Features;

use ZipArchive;

defined('ABSPATH') || exit;

/**
 * Class ReactBuildManager
 *
 * @version 1.0.0
 * @since 1.0.0
 * @package SAzizKhan\WpReactKit\Features
 * @author S.Aziz Khan <sakatazizkhan1@gmail.com>
 */
class ReactBuildManager
{
    private $unzip_folder_path;
    private $build_folder_name;

    public $page_name;
    public $setting_slug;
    public $page_title;


    /**
     * Construct function.
     * 
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct()
    {
    }

    /**
     * Initialize the ReactBuildManager system
     *
     * @return void
     * @version 1.0.1
     * @since 1.0.0
     */
    public function run()
    {
        $this->unzip_folder_path = WP_REACT_KIT_PLUGIN_ROOT . "/public/react-src/";
        $this->page_name = __('React Build Manager');
        $this->page_title = __('React Build Manager');
        $this->setting_slug = WP_REACT_KIT_TEXTDOMAIN . '-build-manager';
        $this->build_folder_name = 'build';

        # Add menu
        add_action('wp_react_kit_admin_settings_menu', array($this, 'add_admin_submenu'));

        $this->actions_init();
    }

    /**
     * Register the actions
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function actions_init()
    {
        # Handle form requests.
        add_action('admin_init', array($this, 'handle_form_requests'));
        # Hook into the admin_notices action
        add_action('admin_notices', array($this, 'display_error_notice'));
    }

    /**
     * Add the config page in admin
     *
     * @version 1.0.0
     * @since 1.0.0
     * @return void
     */
    public function add_admin_submenu(CoreAdminMenu $admin_menu)
    {
        add_submenu_page($admin_menu->menu_slug, $this->page_title, $this->page_name, 'manage_options', $this->setting_slug, array($this, 'render_settings_page'));
    }

    /**
     * Render Settings page
     * 
     * @version 1.0.0
     * @since 1.0.0
     */
    public function render_settings_page()
    {
        include_once WP_REACT_KIT_PLUGIN_ROOT . '/core/views/react-build-manager.php';
        return true;
    }

    /**
     * Handle form submission request
     * 
     * @return void
     * @version 1.0.0
     * @since 1.0.0
     */
    public function handle_form_requests()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'wp_react_kit_build_manager') {
            return $this->configure_admin();
        }
    }

    /**
     * configure React build
     * 
     * @version 1.0.0
     * @since 1.0.0
     */
    public function configure_admin()
    {
        $url = admin_url("admin.php?page=$this->setting_slug");

        $file = $_FILES["wp_react_kit-build-manager-file"];
        if (isset($file)) {

            // Need to require these files
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed = ['zip'];
            if (in_array($file_extension, $allowed)) {
                $upload = wp_handle_upload($file, array('test_form' => false));
                // Check for errors during the file upload
                if (!isset($upload['error'])) {
                    $media_ids = get_option('wp_react_kit_build_media_ids', array());
                    $file_path = $upload['file'];
                    // Create an attachment post object
                    $attachment = array(
                        'post_mime_type' => $upload['type'],
                        'post_title'     => sanitize_file_name($file['name']),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );
                    // Insert the attachment into the media library
                    $id = wp_insert_attachment($attachment, $file_path);

                    $zip_file_path = get_attached_file($id);
                    $unzip_status = $this->unzip_build_file($zip_file_path);
                    if (!$unzip_status) {
                        // delete the media post
                        wp_delete_attachment($id);
                    } else {

                        // Store the array of media IDs in the plugin's options or database table
                        $media_ids[] = $id;
                        update_option('wp_react_kit_build_media_ids', $media_ids);
                        update_option('wp_react_kit_active_build', $id);
                    }
                    /*
                    // Delete the ZIP file after extraction
                    if (unlink($file_path)) {
                        // ZIP file deleted successfully! 
                    } else {
                        // Failed to delete the ZIP file
                    }
                    */
                }
            } else {
                // Set the error message transient
                set_transient('error_message', 'Please upload file having extensions .zip only.', 10); // Set for 10 seconds
            }
        }

        # Redirect to config page
        wp_redirect($url);
        exit;
    }
    /**
     * Unzips a file in the plugin directory.
     *
     * @param string $zip_file_path The path of the ZIP file to be unzipped.
     * @throws Exception If the file fails to unzip.
     * @version 1.0.0
     * @since 1.0.0
     */
    public function unzip_build_file($zip_file_path)
    {
        $folderName = $this->build_folder_name . '/';
        $extractFolder = $this->unzip_folder_path . '/' . $folderName;
        // Create a new ZipArchive instance
        $zip = new ZipArchive();

        if ($zip->open($zip_file_path) === true) {
            $hasBuildFolder = false;  // Initialize a flag
            // Iterate through the entries in the .zip file
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                // Check if the entry name is "build" (case-insensitive)
                if (str_contains($filename, $this->build_folder_name)) {
                    $hasBuildFolder = true;
                    break;  // No need to continue searching
                }
            }

            // we not allowed to unzip if the build folder does not exist
            if (!$hasBuildFolder) {
                // Set the error message transient
                set_transient('error_message', 'Opps! Build folder not found in the zip file, upload the zip file which contains the build folder', 10);
                $zip->close();
                return false;
            }

            $this->clean_old_build();
            // Ensure the target folder exists
            if (!is_dir($extractFolder)) {
                mkdir($extractFolder, 0755, true);
            }
            // Extract the contents of the ZIP file to the specified folder
            for ($i = 0; $i < $zip->numFiles; $i++) {
                // Check if the file or folder name starts with "build/"
                if (strpos($filename, $folderName) === 0) {
                    // Extract only the contents of the "build" folder
                    $extractPath = $extractFolder . substr($filename, 6);  // Remove "build/" prefix
                    if (substr($filename, -1) == '/') {
                        // Create a directory
                        mkdir($extractPath, 0755, true);
                    } else {
                        // Extract the file
                        copy("zip://$zip_file_path#$filename", $extractPath);
                    }
                }
            }

            //$zip->extractTo($this->unzip_folder_path);
            $zip->close();
            // Unzipped successfully!
            return true;
        } else {
            azdebugg('Failed to unzip the file.');
        }
    }


    /**
     * Renames the old build folder to a new folder with a timestamp appended to the name.
     *
     * @return void
     * @version 1.0.0
     * @since 1.0.0
     */
    function clean_old_build()
    {
        $main_path = $this->unzip_folder_path;
        $old_folder_name = $this->build_folder_name;

        $old_folder_path = $main_path . '/' . $old_folder_name;

        if (is_dir($old_folder_path)) {
            rmdir($old_folder_path);
            return true;
        }
    }

    /**
     * Display the error notice if the error message transient is set.
     *
     * @return void
     * @version 1.0.0
     * @since 1.0.0
     */
    public function display_error_notice()
    {
        // Check if the error message transient is set
        if (get_transient('error_message')) {
            // Display the error notice
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . esc_html(get_transient('error_message')) . '</p>';
            echo '</div>';
            // Delete the error message transient
            delete_transient('error_message');
        }
    }
}
