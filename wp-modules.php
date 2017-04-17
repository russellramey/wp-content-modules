<?php
/**
* Plugin Name: WP Content Modules
* Plugin URI: http://russellramey.me/wordpress/wp-content-modules
* Description: Build pages modularly. Create simple, repeateable, blocks of content that can be used anywhere with the generated [module] shortcode.
* Version: 1.0
* Author: Russell Ramey
* Author URI: http://russellramey.me/
*/


/************************************************************************************
*** Module Styles
	Load front end css for displaying the content modules.
************************************************************************************/
// Wordpress action hook
add_action( 'wp_enqueue_scripts', 'load_module_css' );
function load_module_css() {
	// Plugin path
    $plugin_path = plugin_dir_url( __FILE__ );
    // Load styles to wp_head
    wp_enqueue_style( 'modules', $plugin_path . 'css/modules-style.css' );
}


/************************************************************************************
*** Module (Content Module)
	Post type for 'Module'. Create repeatable code blocks to use as shortcodes
************************************************************************************/
// Wordpress action hook
add_action( 'init', 'content_module', 0 );
// Register post_type: module
function content_module() {
	// Create lables for content type (admin view)
	$labels = array(
		'name'                  => _x( 'Module', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Module', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Modules', 'text_domain' ),
		'name_admin_bar'        => __( 'Modules', 'text_domain' ),
		'archives'              => __( 'Module Archives', 'text_domain' ),
		'attributes'            => __( 'Module Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Module:', 'text_domain' ),
		'all_items'             => __( 'All Modules', 'text_domain' ),
		'add_new_item'          => __( 'Add New Module', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Module', 'text_domain' ),
		'edit_item'             => __( 'Edit Module', 'text_domain' ),
		'update_item'           => __( 'Update Module', 'text_domain' ),
		'view_item'             => __( 'View Module', 'text_domain' ),
		'view_items'            => __( 'View Modules', 'text_domain' ),
		'search_items'          => __( 'Search Module', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into module', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this module', 'text_domain' ),
		'items_list'            => __( 'Modules list', 'text_domain' ),
		'items_list_navigation' => __( 'Modules list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter modules list', 'text_domain' ),
	);
    // post_type arguments
	$args = array(
		'label'                 => __( 'Module', 'text_domain' ),
		'description'           => __( 'Modular content block to be used as shortcode in other content types', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor'),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-layout',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'module', $args );
} // End register post type


/************************************************************************************
*** Module Metaboxes
	- Global options for the 'module' post type
	- REQUIRES CMB2 Metabox library
	  @link https://github.com/CMB2/CMB2
************************************************************************************/
// Check for CMB2 library
// If CMB2 class doesn't exist, include it
if( !class_exists("CMB2") ){
    require_once( dirname(__FILE__)."/lib/cmb/init.php" );
}

// Module setup metabox
add_action( 'cmb2_init', 'module_metabox_setup' );
function module_metabox_setup() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_cmb_';
	$module_setup = new_cmb2_box( array(
		'id'            => $prefix . 'module_setup',
		'title'         => __( 'Module Setup', 'cmb2' ),
		'object_types'  => array( 'module'), // Post type
	));

	// Module Width Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Outer Width',
	    'desc'             => 'Select the width of the entire module<br />- Fixed (Module will flow inline with max width of parent content)<br />- Full (Module will fill width of screen, background and all)',
	    'id'               => $prefix . 'module-width',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'auto',
	    'options'          => array(
	        'bt-module--auto' => __( 'Auto', 'cmb2' ),
	        'bt-module--full'   => __( 'Full Width', 'cmb2' ),
	    ),
	));
	// Module Width Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Inner Width',
	    'desc'             => 'Option to constrain or expand the width of the inner content beyond the default container (default content width is 1170px)',
	    'id'               => $prefix . 'module-content-width',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'auto',
        'options'          => array(
	        'auto' => __( 'Auto', 'cmb2' ),
	        'small'   => __( 'Small (768px)', 'cmb2' ),
			'medium'   => __( 'Medium (960px)', 'cmb2' ),
			'large'   => __( 'Large (1280px)', 'cmb2' ),
			'xlarge'   => __( 'X Large (1440px)', 'cmb2' ),
            'full-width'   => __( 'Full Width', 'cmb2' ),
	    ),
	));
	// Module Height Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Height',
        'desc'             => 'Select the padding (height) for the top and bottom of the module content.',
	    'id'               => $prefix . 'module-height',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'auto',
	    'options'          => array(
	        'auto' => __( 'Auto', 'cmb2' ),
	        'small'   => __( 'Small (40px)', 'cmb2' ),
			'medium'   => __( 'Medium (80px)', 'cmb2' ),
			'large'   => __( 'Large (120px)', 'cmb2' ),
			'xlarge'   => __( 'X Large (200px)', 'cmb2' ),
	    ),
	));
    // Module Spacing Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Spacing',
	    'desc'             => 'Select the margin (spacing) for the top and bottom of the module.',
	    'id'               => $prefix . 'module-margin',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'options'          => array(
			'small' => __( 'Small (40px)', 'cmb2' ),
	        'medium'   => __( 'Medium (80px)', 'cmb2' ),
            'large' => __( 'Large (120px)', 'cmb2' ),
	    ),
	));
}

// Module background metabox
add_action( 'cmb2_init', 'module_metabox_background' );
function module_metabox_background() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_cmb_';
	$module_background = new_cmb2_box( array(
		'id'            => $prefix . 'module_background',
		'title'         => __( 'Module Background', 'cmb2' ),
		'object_types'  => array( 'module'), // Post type

	));

	// Module background color 1
	$module_background->add_field( array(
		'name' => 'Background Color',
		'description' => ' First color option (leave blank for default)',
		'id'   => $prefix . 'module-background-color',
		'type' => 'colorpicker',
	));
	// Module background image
	$module_background->add_field( array(
		'name' => 'Background Image',
		'description' => 'Use and image as the background<br />If image is added, it will be used as background and not the colors above',
		'id'   => $prefix . 'module-background-image',
		'type'  => 'file',
	));
	// Module Width Selector
	$module_background->add_field( array(
	    'name'             => 'Background Video Source',
	    'desc'             => 'Select the source of the video ID. This is required to source the correct video API for the ID above.',
	    'id'               => $prefix . 'module-background-video-source',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'options'          => array(
	        'youtube' => __( 'YouTube', 'cmb2' ),
	        'vimeo'   => __( 'Vimeo', 'cmb2' ),
	    ),
	));
	// Module background image
	$module_background->add_field( array(
		'name' => 'Background Video ID',
		'description' => 'Use the youtube or viemo video ID here.',
		'id'   => $prefix . 'module-background-video',
		'type'  => 'text',
	));
}

// Module overlay metabox
add_action( 'cmb2_init', 'module_metabox_overlay' );
function module_metabox_overlay() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_cmb_';
	$module_overlay = new_cmb2_box( array(
		'id'            => $prefix . 'module-overlay',
		'title'         => __( 'Module Overlay', 'cmb2' ),
		'object_types'  => array( 'module'), // Post type
	));

	// Module Overlay color 1
	$module_overlay->add_field( array(
		'name' => 'Overlay Color 1',
		'description' => ' First color option (leave blank for default)',
		'id'   => $prefix . 'module-overlay-color-one',
		'type' => 'colorpicker',
	));
	// Module Overlay color 2
	$module_overlay->add_field( array(
		'name' => 'Overlay Color 2',
		'description' => ' Second color to make gradient (if none - first color will be used as overlay)',
		'id'   => $prefix . 'module-overlay-color-two',
		'type' => 'colorpicker',
	));
	// Module Overlay Opacity
	$module_overlay->add_field( array(
	    'name'             => 'Overlay Opacity',
	    'desc'             => 'Select the opacity of the overlay color/gradient (default is 50%)',
	    'id'               => $prefix . 'module-overlay-opacity',
	    'type'             => 'select',
	    'show_option_none' => false,
		'default'          => '5',
	    'options'          => array(
	        '99' => __( '100%', 'cmb2' ),
	        '9'   => __( '90%', 'cmb2' ),
			'8'   => __( '80%', 'cmb2' ),
			'7'   => __( '70%', 'cmb2' ),
			'6'   => __( '60%', 'cmb2' ),
			'5'   => __( '50%', 'cmb2' ),
			'4'   => __( '40%', 'cmb2' ),
			'3'   => __( '30%', 'cmb2' ),
			'2'   => __( '20%', 'cmb2' ),
			'1'   => __( '10%', 'cmb2' ),
	    ),
	));
	// Module Overlay direction
	$module_overlay->add_field( array(
	    'name'             => 'Overlay Direction',
	    'desc'             => 'Select the overlay gradient direction. Gradient flows from color 1 to color 2',
	    'id'               => $prefix . 'module-overlay-direction',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'right',
	    'options'          => array(
	        'right' => __( 'Right', 'cmb2' ),
	        'left'   => __( 'Left', 'cmb2' ),
			'top'   => __( 'Top', 'cmb2' ),
			'bottom'   => __( 'Bottom', 'cmb2' ),
	    ),
	));
}

// Module shortcode metabox
add_action( 'add_meta_boxes', 'add_module_output' );
function add_module_output() {
	add_meta_box('module_output_code', 'How to use', 'module_output_code', 'module', 'side', 'default');
}
// The Event Location Metabox
function module_output_code() {
	global $post;
	echo '<p style="font-size:16px;">[module title="' . $post->post_name  . '"]</p>';
	echo '<p><i>Copy this shortcode and paste it into any content type editor (ex. post or page)</i></p>';
}


/************************************************************************************
*** Module Shortcode
	Shortcode to display content module.

	[module title=""]
************************************************************************************/
// Wordpress action hook
add_shortcode( 'module', 'module_insert_func' );
function module_insert_func( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => ''
	), $atts ) );
	ob_start(); ?>

		<?php
		// Find correct module
		$module_args = array(
				'post_type' => 'module',
				'name' => $title,
				'posts_per_page' => 1
			);
		// Get new query
		$module = new WP_Query($module_args);
		// If module found, show
		if($module->have_posts()) {
		$module->the_post();

		// Get Module Meta
		// Setup
	    $module_width = get_post_meta( get_the_ID(), '_cmb_module-width', true );
	    $module_height = get_post_meta( get_the_ID(), '_cmb_module-height', true );
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
	    $module_classes = array('bt-module', $module_width);
	    // OUTPUT HTML BELOW
	    ?>

	    <div id="module-<?php the_ID(); ?>">
	    <div <?php post_class($module_classes); //WP Post Classes ?>>
	        <?php
	        // If Video or image or both
	        if($module_background_image != '' || $module_background_video_src != '')  {?>
	            <div class="module-wallpaper" style="background: url(<?php echo $module_background_image; ?>)"></div>
	            <?php
	            // If video and src is YouTube
	            if($module_background_video_src === 'youtube'){ ?>
	                <div class="module-video">
	                    <div class="video youtube" data-id="<?php echo $module_background_video; ?>"></div>
	                </div>
	            <?php } else if($module_background_video_src === 'vimeo') { ?>
                    <div class="module-video">
	                    <div class="video vimeo" data-id="<?php echo $module_background_video; ?>"></div>
	                </div>
                <?php } ?>
	            <div class="module-overlay" style="background:linear-gradient(to <?php echo $module_overlay_direction; ?>, <?php echo $module_overlay_color_one; ?>, <?php echo $module_overlay_color_two; ?>); opacity:.<?php echo $module_overlay_opacity; ?>;"></div>


	        <?php } else { ?>
	            <div class="module-wallpaper" style="background:<?php echo $module_background_color; ?>"></div>
	            <div class="module-overlay" style="background:linear-gradient(to <?php echo $module_overlay_direction; ?>, <?php echo $module_overlay_color_one; ?>, <?php echo $module_overlay_color_two; ?>); opacity:.<?php echo $module_overlay_opacity; ?>;"></div>
	        <?php } ?>

	        <div class="module-content <?php echo 'module-' . $module_height . ' ' . $module_content_width; ?> ">
	            <?php the_content(); ?>
	        </div>
	      </div>
	    </div>

		<?php
		// Reset Query
		wp_reset_query();
		wp_reset_postdata();

		// Clean output
		$content_module = ob_get_clean();
		// Return module
		return $content_module;
	}
}
