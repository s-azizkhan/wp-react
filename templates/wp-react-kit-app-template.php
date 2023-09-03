<?php
/* 
* Template Name: Wp React Kit App Template
* Description: This is a custom page template for WP React Kit.
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body>

    <?php
    $inject_id = $post->__get(WP_REACT_KIT_SHORTNAME . '_inject_id');
    do_shortcode('[wp_react_kit_app injectId="' . $inject_id . '"]');
    ?>

    <?php wp_footer(); ?>
</body>

</html>