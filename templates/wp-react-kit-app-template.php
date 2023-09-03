<?php
/* 
* Template Name: Wp React Kit App Template
* Description: This is a custom page template for WP React Kit.
*/

use SAzizKhan\WpReactKit\ShortCodes\ReactAppShortCode;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <?php do_action('wp_react_kit_app_head', $post); ?>
</head>

<body>

    <?php
    ReactAppShortCode::get_execute($post);
    ?>

    <?php wp_footer(); ?>
</body>

</html>