<?php
/**
* Plugin Name: WP Content Modules
* Plugin URI: https://github.com/russellramey/wp-content-modules
* Description: Build pages modularly. Create simple, repeateable, blocks of content that can be used anywhere with the generated [module] shortcode. You can find more information about WP Content Moudles on github.
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

/************************************************************************************
*** Module Metaboxes
	- Global options for the 'module' post type
	- REQUIRES CMB2 Metabox library
	  @link https://github.com/CMB2/CMB2
************************************************************************************/
// Module Setup
add_action("add_meta_boxes", "wp_content_module_setup");
function wp_content_module_setup() {
    // Add setup box action
    add_meta_box("wp_content_module_setup", "Module Setup", "wp_content_module_setup_markup", "module", "normal", "high", null);

    // Markup
    function wp_content_module_setup_markup() {
        // WP Nonce Hook (required)
        wp_nonce_field(basename(__FILE__), "meta-box-nonce");

        // Get all available or previsouly set meta data
        $meta = get_post_meta(get_the_ID());
        // For each entry get the value if available
        foreach ( $meta as $key => $value ) {
            ${$key} = $value[0];
        }

    // Start input markup
    ?>
    <div class="wp-module--setup clearfix">
        <!-- Module Width -->
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module Width</p>
            </div>
            <div class="wp-module--meta-field-input">
                <?php
                    // Set dropdown options
                    $width_options = array(
                        "wp-module--auto" => "Auto",
                        "wp-module--full" => "Full Width",
                    );
                    // Render dropdown options
                    wp_content_module_select_input('_module_width', $width_options, isset($_module_width) ? $_module_width : null);
                ?>
                <p class="wp-module--meta-field-desc">Select the width of the entire module<br />- Auto (Module will flow inline with max width of parent container)<br />- Full Width (Module will fill width of viewport, background and all)</p>
            </div>
        </div>

        <!-- Module Content Width -->
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module Content Width</p>
            </div>
            <div class="wp-module--meta-field-input">
                <?php
                    // Set dropdown options
                    $content_width_options = array(
                        "auto" => "Auto",
                        "small" => "Small (768px)",
                        "medium" => "Medium (960px)",
                        "large" => "Large (1170px)",
                        "xlarge" => "X Large (1440px)",
                    );
                    // Render dropdown options
                    wp_content_module_select_input('_module_content_width', $content_width_options, isset($_module_content_width) ? $_module_content_width : null);
                ?>
                <p class="wp-module--meta-field-desc">Option to set the width of the inner content within the module<br />- Auto (Module content will fill same width as "Module Outer Width" above)<br />- Small (Module content max width of 768px)<br />- Medium (Module content max width of 960px)<br />- Large (Module content max width of 1280px)<br />- X Large (Module content max width of 1440px)</p>
            </div>
        </div>

        <!-- Module Padding -->
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module Padding</p>
            </div>
            <div class="wp-module--meta-field-input">
                <?php
                    // Set dropdown options
                    $padding_options = array(
                        "auto" => "Auto",
                        "small" => "Small (40px)",
                        "medium" => "Medium (80px)",
                        "large" => "Large (120px)",
                        "xlarge" => "Larger (160px)",
                        "xxlarge" => "Largest (200px)",
                    );
                    // Render dropdown options
                    wp_content_module_select_input('_module_padding', $padding_options, isset($_module_padding) ? $_module_padding : null);
                ?>
                <p class="wp-module--meta-field-desc">Set the padding for the top and bottom of the module inner content (will affect total height of module)</p>
            </div>
        </div>

        <!-- Module Margin -->
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module Margin</p>
            </div>
            <div class="wp-module--meta-field-input">
                <?php
                    // Set dropdown options
                    $margin_options = array(
                        "none" => "None",
                        "small" => "Small (40px)",
                        "medium" => "Medium (80px)",
                        "large" => "Large (120px)",
                    );
                    // Render dropdown options
                    wp_content_module_select_input('_module_margin', $margin_options, isset($_module_margin) ? $_module_margin : null);
                ?>
                <p class="wp-module--meta-field-desc">Set the margin for the top and bottom of the module<br />(this will affect the spacing between the module and other content on the page)</p>
            </div>
        </div>

        <!-- Module Text Color -->
        <div class="wp-module--meta-field">
            <div class="wp-module--meta-field-label">
                <p>Module Text Color</p>
            </div>
            <div class="wp-module--meta-field-input">
                <?php
                    // Set dropdown options
                    $color_options = array(
                        "black" => "Dark",
                        "white" => "Light",
                    );
                    // Render dropdown options
                    wp_content_module_select_input('_module_text_color', $color_options, isset($_module_text_color) ? $_module_text_color : null);
                ?>
                <p class="wp-module--meta-field-desc">Select the default text color for the module.<br />(You can overirde text colors using the editor styles above - this option is used to set the base color/theme.)</p>
            </div>
        </div>

    </div>
<?php }
}
// Module Background
add_action("add_meta_boxes", "wp_content_module_overlay");
function wp_content_module_overlay() {
    // Add setup box action
    add_meta_box("wp_content_module_overlay", "Module Overlay Options", "wp_content_module_overlay_markup", "module", "normal", "high", null);

    // Markup
    function wp_content_module_overlay_markup() {
        // WP Nonce Hook (required)
        wp_nonce_field(basename(__FILE__), "meta-box-nonce");

        // Get all available or previsouly set meta data
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
        <div class="wp-module--overlay clearfix">
            <!-- Module Overlay Color 1 -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Overlay Color 1</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php wp_content_module_text_input('_module_overlay_color_1', 'color-picker', isset($_module_overlay_color_1) ? $_module_overlay_color_1 : null); ?>
                    <p class="wp-module--meta-field-desc">First color option for color overlay</p>
                </div>
            </div>
            <!-- Module Overlay Color 1 -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Overlay Color 2</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php wp_content_module_text_input('_module_overlay_color_2', 'color-picker', isset($_module_overlay_color_2) ? $_module_overlay_color_2 : null); ?>
                    <p class="wp-module--meta-field-desc">Select a second overlay color to create a gradient<br />(leave blank to use <b>Overlay Color 1</b> for a solid color overlay)</p>
                </div>
            </div>
            <!-- Module Overlay Opacity -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Overlay Opacity</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php
                        // Set dropdown options
                        $opacity_options = array(
                            null => "None",
                            "90" => "90%",
                            "80" => "80%",
                            "70" => "70%",
                            "60" => "60%",
                            "50" => "50%",
                            "40" => "40%",
                            "30" => "30%",
                            "20" => "20%",
                            "10" => "10%",
                        );
                        // Render dropdown options
                        wp_content_module_select_input('_module_overlay_opacity', $opacity_options, isset($_module_overlay_opacity) ? $_module_overlay_opacity : null);
                    ?>
                    <p class="wp-module--meta-field-desc">Select the opacity of the overlay color/gradient (default is 100%, no opacity)</p>
                </div>
            </div>
            <!-- Module Overlay Direction -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Overlay Direction</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php
                        // Set dropdown options
                        $direction_options = array(
                            "left" => "Left",
                            "right" => "Right",
                            "top" => "Top",
                            "bottom" => "Bottom",
                        );
                        // Render dropdown options
                        wp_content_module_select_input('_module_overlay_direction', $direction_options, isset($_module_overlay_direction) ? $_module_overlay_direction : null);
                    ?>
                    <p class="wp-module--meta-field-desc">Select the overlay gradient direction. Gradient flows from <b>Overlyay Color 1</b> to <b>Overlay Color 2</b></p>
                </div>
            </div>
        </div>
    <?php }
}
// Module Background
add_action("add_meta_boxes", "wp_content_module_background");
function wp_content_module_background() {
    // Add setup box action
    add_meta_box("wp_content_module_background", "Module Background Options", "wp_content_module_background_markup", "module", "normal", "high", null);

    // Markup
    function wp_content_module_background_markup() {
        // WP Nonce Hook (required)
        wp_nonce_field(basename(__FILE__), "meta-box-nonce");

        // Get all available or previsouly set meta data
        $meta = get_post_meta(get_the_ID());
        // For each entry get the value if available
        foreach ( $meta as $key => $value ) {
            ${$key} = $value[0];
        }

        // Start html output
        ?>
        <div class="wp-module--background clearfix">
            <!-- Module Background Color -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Background Color</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php wp_content_module_text_input('_module_background_color', 'color-picker', isset($_module_background_color) ? $_module_background_color : null); ?>
                    <p class="wp-module--meta-field-desc">Choose the background color of the module<br />(This color will be used as the fallback for background images and background videos - default is <b>White</b>)</p>
                </div>
            </div>

            <!-- Module Text Color -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Background Image Format</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php
                        // Set dropdown options
                        $image_format_options = array(
                            "cover" => "Cover",
                            "repeat" => "Repeat",
                            "fixed" => "Fixed",
                        );
                        // Render dropdown options
                        wp_content_module_select_input('_module_background_image_format', $image_format_options, isset($_module_background_image_format) ? $_module_background_image_format : null);
                    ?>
                    <p class="wp-module--meta-field-desc">Select the format for the module background image (if background image exists).<br />- <b>Cover</b> Background image will fill width and height of the moudle (default).<br>- <b>Repeat</b> Background image will repeat on both X and Y axis, best option for a texture background.<br> - <b>Fixed</b> Background image will fill width and hight of module, but will be fixed to the viewport (simple parrallax effect).</p>
                </div>
            </div>

            <!-- Module Video ID -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Video ID</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php wp_content_module_text_input('_module_video_id', '', isset($_module_video_id) ? $_module_video_id : null); ?>
                    <p class="wp-module--meta-field-desc">Use the youtube or viemo video ID here.<br />You can use the <b>Background Image</b> field to set a fallback image for devices that do not support background videos (like tablets and mobile)</p>
                </div>
            </div>


            <!-- Module Video Source -->
            <div class="wp-module--meta-field">
                <div class="wp-module--meta-field-label">
                    <p>Module Video Source</p>
                </div>
                <div class="wp-module--meta-field-input">
                    <?php
                        // Set dropdown options
                        $video_options = array(
                            null => 'None',
                            "youtube" => "YouTube",
                            "vimeo" => "Vimeo",
                        );
                        // Render dropdown options
                        wp_content_module_select_input('_module_background_video_source', $video_options, isset($_module_background_video_source) ? $_module_background_video_source : null);
                    ?>
                    <p class="wp-module--meta-field-desc">If you want to use a background video, select the source of the video ID.<br />This is required to source the correct video API for the ID above, if no source is choosen the video will not be shown - even with a supplied ID</p>
                </div>
            </div>
        </div>

<?php }

}
// Metabox input generation
// Generate select dropdown
function wp_content_module_select_input($name, $options, $value) {
    echo '<select name="'. $name . '">';
    foreach($options as $metaKey => $metaValue) {
        if($metaKey == $value) {
            echo '<option selected value="' . $metaKey . '">' . $metaValue . '</option>';
        } else {
            echo '<option value="' . $metaKey . '">' . $metaValue . '</option>';
        }
    }
    echo '</select>';
}
// Generate text input fields
function wp_content_module_text_input($name, $class, $value) {
    echo '<input class="' . $class . '" type="text" name="' . $name . '"  value="' . $value . '" />';
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
	    $module_classes = array('wp-module', $_module_width);
	    // OUTPUT HTML BELOW
	    ?>

        <div id="module-<?php the_ID(); ?>" class="module-margin--<?php echo isset($_module_margin) ? $_module_margin : ''; ?> module-text--<?php echo isset($_module_text_color) ? $_module_text_color : ''; ?>">
            <div <?php post_class($module_classes); //WP Post Classes ?>>
                <div class="module-wallpaper module-wallpaper--<?php echo isset($_module_background_image_format) ? $_module_background_image_format : 'cover'; ?>" style="background:<?php echo isset($_module_background_color) ? $_module_background_color : '#ffffff;'; ?>; <?php if(has_post_thumbnail()) { echo 'background: url('; the_post_thumbnail_url(); echo ')'; } ?>"></div>

                <?php
                // If Video
                if(isset($_module_background_video_source) ? $_module_background_video_source != '' : null )  { ?>
                    <div class="module-video">
                        <div class="video <?php echo $_module_background_video_source; ?>" data-id="<?php echo $_module_video_id; ?>"></div>
                    </div>
                <?php } ?>

                <div class="module-overlay" style="background:<?php echo isset($_module_overlay_color_1) ? $_module_overlay_color_1 : ''; ?>; background:linear-gradient(to <?php echo isset($_module_overlay_direction) ? $_module_overlay_direction : ''; ?>, <?php echo isset($_module_overlay_color_1) ? $_module_overlay_color_1 : ''; ?>, <?php echo isset($_module_overlay_color_2) ? $_module_overlay_color_2 : ''; ?>); opacity:.<?php echo isset($_module_overlay_opacity) ? $_module_overlay_opacity : ''; ?>;"></div>

                <div class="module-content <?php echo 'module-content--height-' . $_module_padding . ' module-content--width-' . $_module_content_width; ?> ">
                    <?php the_content(); ?>
                </div>

            </div>
        </div>

		<?php
		// Reset Query
		wp_reset_query();
        // Reset Post Data
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

    // Render script tags
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
    }
));
