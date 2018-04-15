<!DOCTYPE html>
<html <?php language_attributes(); // WP Lang attribute ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); //WP Charset ?>" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); // WP Style sheet directory ?>" />
    <?php wp_head(); //WP header ?>
</head>
<body <?php body_class(); // WP body classes?>>
<div class="container" style="max-width:1170px; margin:0 auto;">

    <?php
    // Get current module
    the_post();

    // Get all meta data
    $meta = get_post_meta(get_the_ID());
    // For each entry get the value if available
    foreach ( $meta as $key => $value ) {
        ${$key} = $value[0];
    }

    // Module classes
    $module_classes = array('wp-module', isset($_module_width) ? $_module_width : 'wp-module--auto');
    // OUTPUT HTML BELOW
    ?>

    <div id="module-<?php the_ID(); ?>" class="<?php echo isset($_module_margin) ? $_module_margin : null; ?> <?php echo isset($_module_text_color) ? $_module_text_color : null; ?>">
        <div <?php post_class($module_classes); //WP Post Classes ?>>
            <div class="module-wallpaper <?php echo isset($_module_background_image_format) ? $_module_background_image_format : null; ?>" style="background:<?php echo isset($_module_background_color) ? $_module_background_color : '#ffffff;'; ?>; <?php if(get_the_post_thumbnail_url($post->ID)) { echo 'background: url(' . get_the_post_thumbnail_url($post->ID) . ')'; } ?>"></div>

            <?php
            // If Video
            if(isset($_module_background_video_source) ? $_module_background_video_source != '' : null )  { ?>
                <div class="module-video">
                    <div id="video-<?php echo $_module_video_id; ?>" class="video <?php echo $_module_background_video_source; ?>" data-id="<?php echo $_module_video_id; ?>"></div>
                </div>
            <?php } ?>

            <div class="module-overlay" style="background:<?php echo isset($_module_overlay_color_1) ? $_module_overlay_color_1 : ''; ?>; background:linear-gradient(to <?php echo isset($_module_overlay_direction) ? $_module_overlay_direction : ''; ?>, <?php echo isset($_module_overlay_color_1) ? $_module_overlay_color_1 : ''; ?>, <?php echo isset($_module_overlay_color_2) ? $_module_overlay_color_2 : ''; ?>); opacity:.<?php echo isset($_module_overlay_opacity) ? $_module_overlay_opacity : ''; ?>;"></div>
            
            <div class="module-content <?php echo isset($_module_padding) ? $_module_padding : null; ?> <?php echo isset($_module_content_width) ? $_module_content_width : null; ?>">
                <?php the_content(); ?>
            </div>

        </div>
    </div>

</div>

<?php wp_footer(); //WP Footer ?>
</body>
</html>
