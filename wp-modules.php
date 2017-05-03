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
    wp_enqueue_style( 'modules', $plugin_path . 'css/module-styles.css' );
}
add_action( 'admin_enqueue_scripts', 'load_module_admin_css' );
function load_module_admin_css() {
	// Plugin path
    $plugin_path = plugin_dir_url( __FILE__ );
    // Load styles to wp_head
    wp_enqueue_style( 'modules', $plugin_path . 'css/module-admin-styles.css' );
    // Get color picker styles (defualt WP)
    wp_enqueue_style( 'wp-color-picker');
    // Get color picker function (defualt WP)
    wp_enqueue_script( 'wp-color-picker');
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
		'featured_image'        => __( 'Background Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set background image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove background image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as background image', 'text_domain' ),
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
		'supports'              => array( 'title', 'editor', 'thumbnail'),
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
		'show_in_rest'          => true,
	);
	register_post_type( 'module', $args );
} // End register post type
add_filter( 'admin_post_thumbnail_html', 'add_featured_image_instruction');
function add_featured_image_instruction( $content ) {
    return $content .= '<p>Add a backgound image for the module here. This image will also function as the fallback background for the video background option.</p>';
}

/************************************************************************************
*** Module Metaboxes
	- Global options for the 'module' post type
	- REQUIRES CMB2 Metabox library
	  @link https://github.com/CMB2/CMB2
************************************************************************************/
// Check for CMB2 library
// If CMB2 class doesn't exist, include it
/*if( !class_exists('CMB2') ){
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
	    'desc'             => 'Select the width of the entire module<br />- Auto (Module will flow inline with max width of parent container)<br />- Full Width (Module will fill width of viewport, background and all)',
	    'id'               => $prefix . 'module_width',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'auto',
	    'options'          => array(
	        'wp-module--auto' => __( 'Auto', 'cmb2' ),
	        'wp-module--full'   => __( 'Full Width', 'cmb2' ),
	    ),
	));
	// Module Width Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Content Width',
	    'desc'             => 'Option to constrain or expand the width of the inner content beyond the default container (default content width is 1170px)
                               <br />- Auto (Module content will fill same width as "Module Outer Width" above)
                               <br />- Small (Module content max width of 768px)
                               <br />- Medium (Module content max width of 960px)
                               <br />- Large (Module content max width of 1280px)
                               <br />- X Large (Module content max width of 1440px)',
	    'id'               => $prefix . 'module_content_width',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'auto',
        'options'          => array(
	        'auto' => __( 'Auto', 'cmb2' ),
	        'small'   => __( 'Small (768px)', 'cmb2' ),
			'medium'   => __( 'Medium (960px)', 'cmb2' ),
			'large'   => __( 'Large (1280px)', 'cmb2' ),
			'xlarge'   => __( 'X Large (1440px)', 'cmb2' ),
	    ),
	));
	// Module Height Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Padding',
        'desc'             => 'Select the padding for the <b>top</b> and <b>bottom</b> of the module content.',
	    'id'               => $prefix . 'module_height',
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
	    'name'             => 'Module Margin',
	    'desc'             => 'Select the margin for the <b>top</b> and <b>bottom</b> of the module.',
	    'id'               => $prefix . 'module_margin',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'options'          => array(
			'small' => __( 'Small (40px)', 'cmb2' ),
	        'medium'   => __( 'Medium (80px)', 'cmb2' ),
            'large' => __( 'Large (120px)', 'cmb2' ),
	    ),
	));
	 // Module Spacing Selector
	$module_setup->add_field( array(
	    'name'             => 'Module Text Color',
	    'desc'             => 'Select the default text color for the module.<br />(You can overirde text colors using the editor styles above - this option is used to set the base color.)',
	    'id'               => $prefix . 'module_text_color',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'black',
	    'options'          => array(
	        'black'   => __( 'Dark', 'cmb2' ),
	        'white' => __( 'Light', 'cmb2' ),
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
		'description' => 'Choose the background color of the module',
		'id'   => $prefix . 'module_background_color',
		'type' => 'colorpicker',
	));
	// Module background image
	$module_background->add_field( array(
		'name' => 'Background Image',
		'description' => 'Use and image as the background<br />If image is added, it will be used as background and not the colors above<br />This image will also be used as the backup background for video backgrounds below',
		'id'   => $prefix . 'module_background_image',
		'type'  => 'file',
	));
	// Module Width Selector
	$module_background->add_field( array(
	    'name'             => 'Background Video Source',
	    'desc'             => 'If you want to use a background video, select the source of the video ID.<br />This is required to source the correct video API for the ID above, if no source is choosen the video will not be shown - even with a supplied ID',
	    'id'               => $prefix . 'module_background_video_source',
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
		'description' => 'Use the youtube or viemo video ID here.<br />Use the background image field above to set a fallback image for devices that do not support background videos (tablets and mobile)',
		'id'   => $prefix . 'module_background_video',
		'type'  => 'text',
	));
}

// Module overlay metabox
add_action( 'cmb2_init', 'module_metabox_overlay' );
function module_metabox_overlay() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_cmb_';
	$module_overlay = new_cmb2_box( array(
		'id'            => $prefix . 'module_overlay',
		'title'         => __( 'Module Overlay', 'cmb2' ),
		'object_types'  => array( 'module'), // Post type
	));

	// Module Overlay color 1
	$module_overlay->add_field( array(
		'name' => 'Overlay Color 1',
		'description' => ' First color option (leave blank for default)',
		'id'   => $prefix . 'module_overlay_color_one',
		'type' => 'colorpicker',
	));
	// Module Overlay color 2
	$module_overlay->add_field( array(
		'name' => 'Overlay Color 2',
		'description' => ' Second color to make gradient (if none - first color will be used as overlay)',
		'id'   => $prefix . 'module_overlay_color_two',
		'type' => 'colorpicker',
	));
	// Module Overlay Opacity
	$module_overlay->add_field( array(
	    'name'             => 'Overlay Opacity',
	    'desc'             => 'Select the opacity of the overlay color/gradient (default is 50%)',
	    'id'               => $prefix . 'module_overlay_opacity',
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
	    'id'               => $prefix . 'module_overlay_direction',
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
}*/


// Module Setup
add_action("add_meta_boxes", "wp_content_module_setup");
function wp_content_module_setup() {
    // Add setup box action
    add_meta_box("wp_content_module_setup", "Module Setup", "wp_content_module_setup_markup", "module", "normal", "high", null);

    // Markup
    function wp_content_module_setup_markup($object) {
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    // Get all meta data
    $meta = get_post_meta(get_the_ID());
    // For each entry get the value if available
    foreach ( $meta as $key => $value ) {
        ${$key} = $value[0];
    }

    // Start input markup
    ?>
    <script>
    jQuery(document).ready(function($){
        $('.color-picker').wpColorPicker();
    });
    </script>
    <div class="wp-module--setup clearfix">
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module outter width</p>
            </div>
            <div class="wp-module--meta-field-input">
                <select name="_module_outer_width">
                    <?php
                    // Set select options
                    $option_values = array(
                        "fixed" => "Auto",
                        "full-width" => "Full Width",
                    );
                    // Get each key/value pair of select
                    foreach($option_values as $metaKey => $metaValue) {
                        if($metaKey === $_module_outer_width) { ?>
                            <option selected value="<?php echo $metaKey; ?>"><?php echo $metaValue; ?></option>
                        <?php
                        } else { ?>
                            <option value="<?php echo $metaKey; ?>"><?php echo $metaValue; ?></option>
                    <?php } } ?>
                </select>
            <p class="wp-module--meta-field-desc">Select the width of the entire module<br />- Auto (Module will flow inline with max width of parent container)<br />- Full Width (Module will fill width of viewport, background and all)</p>
            </div>
        </div>
    </div>
    <?php }
}

// Save All Metadata
add_action("save_post", "wp_content_module_meta_save", 10, 3);
function wp_content_module_meta_save($post_id, $post, $update) {
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
    if(!current_user_can("edit_post", $post_id))
        return $post_id;
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    // If is not Content Module
    if("module" != $post->post_type)
        return $post_id;

    // Get each meta option value
    foreach($_POST as $key => $value) {
        if (strpos($key, '_module_') === 0 && isset($key)) {

            // Get meta value and sanatize
            $userInput = sanitize_text_field($value);
            // Update meta value in DB
            update_post_meta($post_id, $key, $userInput);

        }
    }
}

// Dispaly shortcode to copy
add_action( 'add_meta_boxes', 'add_module_output' );
function add_module_output() {
	add_meta_box('module_output_code', 'How to use', 'module_output_code', 'module', 'side', 'default');

    // The Event Location Metabox
    function module_output_code() {
    	global $post;
    	echo '<p style="font-size:16px;">[module title="' . $post->post_name  . '"]</p>';
    	echo '<p><i>Copy this shortcode and paste it into any content type editor (ex. post or page)</i></p>';
    }
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
	), $atts )); ob_start(); ?>

		<?php
		// Find correct module
		$module_args = array(
				'post_type' => 'module',
				'name' => $title,
				'posts_per_page' => 1
			);
		// Get new query
		$module = new WP_Query($module_args);
		// If module found
		if($module->have_posts()) {
		$module->the_post();


        // Get all meta data
        $meta = get_post_meta(get_the_ID());
        // For each entry get the value if available
        foreach ( $meta as $key => $value ) {
            ${$key} = $value[0];
        }
	    // Module classes
	    $module_classes = array('wp-module', $_cmb_module_width);
	    // OUTPUT HTML BELOW
	    ?>

	    <div id="module-<?php the_ID(); ?>" class="module-margin--<?php echo isset($_cmb_module_margin) ? $_cmb_module_margin : ''; ?> module-text--<?php echo isset($_cmb_module_text_color) ? $_cmb_module_text_color : ''; ?>">
    	    <div <?php post_class($module_classes); //WP Post Classes ?>>
                <div class="module-wallpaper" style="background:<?php echo isset($_cmb_module_background_color) ? $_cmb_module_background_color : '#ffffff'; ?>; <?php echo isset($_cmb_module_background_image) ? 'background: url(' . $_cmb_module_background_image . ')' : ''; ?>"></div>

                <?php
    	        // If Video
    	        if(isset($_cmb_module_background_video_source))  { ?>
                    <div class="module-video">
                        <div class="video <?php echo $_cmb_module_background_video_source; ?>" data-id="<?php echo $_cmb_module_background_video; ?>"></div>
                    </div>
                <?php } ?>

                <div class="module-overlay" style="background:#<?php echo isset($_cmb_module_overlay_color_one) ? $_cmb_module_overlay_color_one : ''; ?>; background:linear-gradient(to <?php echo isset($_cmb_module_overlay_direction) ? $_cmb_module_overlay_direction : ''; ?>, <?php echo isset($_cmb_module_overlay_color_one) ? $_cmb_module_overlay_color_one : ''; ?>, <?php echo isset($_cmb_module_overlay_color_two) ? $_cmb_module_overlay_color_two : ''; ?>); opacity:.<?php echo isset($_cmb_module_overlay_opacity) ? $_cmb_module_overlay_opacity : ''; ?>;"></div>

    	        <div class="module-content <?php echo 'module-content--height-' . $_cmb_module_height . ' module-content--width-' . $_cmb_module_content_width; ?> ">
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


/************************************************************************************
*** Javascript
	Use YouTube and Viemo APIs to build player dynamicly.
    Load this script in wp_footer
************************************************************************************/
// Add action hook
add_action( 'wp_footer', 'load_module_scripts' );
// Load javascript
function load_module_scripts() {
    // Global posts
    global $post;
    // If is post, is module, and has shortcode [module]
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'module') || get_post_type() === 'module' ) {
    ?>
    <script src="https://www.youtube.com/iframe_api"></script>
    <script type="text/javascript">
        function onYouTubeIframeAPIReady() {
            // Get list of all player containers
            var players = document.querySelectorAll('.youtube');
            // For each player, create YT player
            for (var p = 0; p < players.length; p++){
                // New player
                new YT.Player(players[p], {
                    // YouTube Video ID
                    videoId: players[p].dataset.id,
                    // Player options
                    playerVars: {
                        // Auto-play the video on load
                        playlist: players[p].dataset.id,
                        autoplay: 1,
                        // Show related videos
                        rel: 0,
                        // Show pause/play buttons in player
                        controls: 0,
                        // Hide the video title
                        showinfo: 0,
                        // Hide the Youtube Logo
                        modestbranding: 1,
                        // Run the video in a loop
                        loop: 1,
                        // Hide the full screen button
                        fs: 0,
                        // Hide closed captions
                        cc_load_policty: 0,
                        // Hide the Video Annotations
                        iv_load_policy: 3,
                        // Hide video controls when playing
                        autohide: 0,
                        html5: 1
                    },
                    events: {
                        onReady: function(e) {
                        e.target.mute();
                        }
                    }

                });
            }
        }
    </script>
    <script src="https://player.vimeo.com/api/player.js"></script>
    <script>
        // Get list of all player containers
        var players = document.querySelectorAll('.vimeo');
        // For each player found
        for (var p = 0; p < players.length; p++){
            // Set player options
            var options = {
                // Video ID
                id: players[p].dataset.id,
                // Loop video
                loop: true,
                // Autoplay
                autoplay: true
            };
            // Create player
            var player = new Vimeo.Player(players[p], options);
            // Mute player
            player.setVolume(0);
        }
    </script>

<?php } }


/************************************************************************************
*** Single View (Preview)
	Set custom single view template for Module post type.
************************************************************************************/
// Add action hook
add_filter( 'single_template', 'module_preview_template' );
// Register function
function module_preview_template($single_template) {
    // Global posts
     global $post;
    // If Module post type
    if ($post->post_type == 'module' ) {
        $single_template = dirname( __FILE__ ) . '/wp-module-preview.php';
    }
    // Return template
    return $single_template;
    // Reset post data
    wp_reset_postdata();
}

/************************************************************************************
*** REST api
	Allow meta data to be sent with REST api calls (allows modules to work with REST)
************************************************************************************/
register_rest_field( 'module', 'metadata', array(
    'get_callback' => function ( $data ) {
        return get_post_meta( $data['id'], '', '' );
    }, ));
