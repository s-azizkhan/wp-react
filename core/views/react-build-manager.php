<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ReactBuildManager configuration html
 * 
 * @version 1.0.0
 * @since 1.0.0
 */

?>
<div class="wrap">
    <h2>
        <?php _e('Manege React Build', WP_REACT_KIT_TEXTDOMAIN); ?>
    </h2>
    <div class="wrk-build-container">
        <div class="wrk-build-left">
            <h3>
                <?php _e('Manege React Builds', WP_REACT_KIT_TEXTDOMAIN); ?>
            </h3>
            <p>
                <?php _e('Mange multiple React Builds here.', WP_REACT_KIT_TEXTDOMAIN); ?>
            </p>
            <form method="post" action="<?php echo  admin_url('admin-post.php',); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="wp_react_kit_build_manager">
                <!-- Map the meta keys & file header -->
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="wp_react_kit-build-manager-file"><?php _e('Build File* (zip) ', WP_REACT_KIT_TEXTDOMAIN); ?></label>
                            </th>
                            <td>
                                <input required type="file" name="wp_react_kit-build-manager-file" id="wp_react_kit-build-manager-file">
                            </td>
                        </tr>

                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo  __('Save Changes', WP_REACT_KIT_TEXTDOMAIN); ?>">
                </p>
            </form>

            <div class="wrap">
                <h2>Uploaded Builds</h2>
                <ul>
                    <?php
                    $active_build = get_option('wp_react_kit_active_build', ''); // Retrieve the active build identifier
                    $media_ids = get_option('wp_react_kit_build_media_ids', array());
                    rsort($media_ids);

                    foreach ($media_ids as $media_id) {
                        $file_info = get_post($media_id);

                        if ($file_info) {
                            $file_name = $file_info->post_title;
                            $file_type = $file_info->post_mime_type;
                            $file_date = $file_info->post_date;

                            // Get the user who uploaded the file
                            $uploaded_by = get_userdata($file_info->post_author);
                            $uploaded_by_name = $uploaded_by ? $uploaded_by->display_name  : 'Unknown';

                            // Check if this build is the active one
                            $is_active_build = ($active_build == $media_id);

                    ?>

                            <li class="mb-3">
                                <?php echo esc_html($file_name); ?> (<?php echo esc_html($file_type); ?>)
                                <br>
                                Uploaded on: <?php echo esc_html($file_date); ?>
                                <br>
                                Uploaded by: <?php echo esc_html($uploaded_by_name); ?> < <?php echo esc_html($uploaded_by->user_email); ?>>
                                    <br>
                                    <?php if ($is_active_build) : ?>
                                        <span class="active-build"><strong>
                                                Active Build
                                            </strong>
                                        </span>
                                    <?php else : ?>
                                        <a href="?page=your-plugin-page&set_active_build=<?php echo esc_attr($media_id); ?>">Set as Active</a>
                                    <?php endif; ?>

                                    <br>
                                    <a href="<?php echo esc_url(get_edit_post_link($media_id)); ?>">Edit</a> |
                                    <a href="<?php echo esc_url(wp_get_attachment_url($media_id)); ?>" class="delete-file" data-media-id="<?php echo esc_attr($media_id); ?>">Download</a>
                            </li>
                            <hr>

                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>

        </div>
    </div>
</div>