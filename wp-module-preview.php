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

    // Get Module Meta
    // Setup
    $module_width = get_post_meta( get_the_ID(), '_cmb_module-width', true );
    $module_height = get_post_meta( get_the_ID(), '_cmb_module-height', true );
    $module_margin = get_post_meta( get_the_ID(), '_cmb_module-margin', true );
    $module_content_width = get_post_meta( get_the_ID(), '_cmb_module-content-width', true );
    // Background
    $module_background_image = get_post_meta( get_the_ID(), '_cmb_module-background-image', true );
    $module_background_video = get_post_meta( get_the_ID(), '_cmb_module-background-video', true );
    $module_background_video_src = get_post_meta( get_the_ID(), '_cmb_module-background-video-source', true );
    $module_background_color = get_post_meta( get_the_ID(), '_cmb_module-background-color', true );
    // Overlay
    $module_overlay_color_one = get_post_meta( get_the_ID(), '_cmb_module-overlay-color-one', true );
    $module_overlay_color_two = get_post_meta( get_the_ID(), '_cmb_module-overlay-color-two', true );
    $module_overlay_opacity = get_post_meta( get_the_ID(), '_cmb_module-overlay-opacity', true );
    $module_overlay_direction = get_post_meta( get_the_ID(), '_cmb_module-overlay-direction', true );


    // Module classes
    $module_classes = array('wp-module', $module_width);
    // OUTPUT HTML BELOW
    ?>

    <div id="module-<?php the_ID(); ?>" class="module-margin--<?php echo $module_margin; ?>">
        <div <?php post_class($module_classes); //WP Post Classes ?>>
            <div class="module-wallpaper" style="background:<?php echo $module_background_color; ?>; background: url(<?php echo the_post_thumbnail_url('full'); ?>)"></div>

            <?php
            // If Video
            if($module_background_video_src != '')  { ?>
                <div class="module-video">
                    <div class="video <?php echo $module_background_video_src; ?>" data-id="<?php echo $module_background_video; ?>"></div>
                </div>
            <?php } ?>

            <div class="module-overlay" style="background:#<?php echo $module_overlay_color_one; ?>; background:linear-gradient(to <?php echo $module_overlay_direction; ?>, <?php echo $module_overlay_color_one; ?>, <?php echo $module_overlay_color_two; ?>); opacity:.<?php echo $module_overlay_opacity; ?>;"></div>

            <div class="module-content <?php echo 'module-content--height-' . $module_height . ' module-content--width-' . $module_content_width; ?> ">
                <?php the_content(); ?>
                <?php  ?>
            </div>

        </div>
    </div>

</div>

<?php wp_footer(); //WP Footer ?>
</body>
</html>
