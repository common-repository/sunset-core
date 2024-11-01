<?php
/**
 * Funtions Initializer
 *
 * @since   1.0.0
 * @package Sunset Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sunset_core_block_assets' );
/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function sunset_core_block_assets() { // phpcs:ignore

	// include color picker
	if ( ! wp_script_is( 'wp-color-picker', 'enqueued' ) ) {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	// Register block styles for both frontend + backend.
	wp_enqueue_style(
		'sunset_core-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-block-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'sunset_core-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor script for frontend.
    if (!is_admin()) {
		wp_enqueue_script(
			'sunset_core-front-js', // Handle.
			plugins_url( '/dist/front.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
			array( 'jquery' ), // Dependencies, defined above.
			null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
			true // Enqueue the script in the footer.
		);
	}

	// Register block editor styles for backend.
	wp_register_style(
		'sunset_core-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `sunsetCoreGlobal` object.
	wp_localize_script(
		'sunset_core-block-js',
		'sunsetCoreGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `sunsetCoreGlobal` object.
		]
	);
	
	wp_set_script_translations( 'sunset_core-block-js', 'sunset-core', plugin_dir_path( __DIR__ ) .'languages' );

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.0.0
	 */
	register_block_type(
		'sunset/block-sunset-core', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'sunset_core-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'sunset_core-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'sunset_core-block-editor-css',
		)
	);
}

add_action( 'after_setup_theme', 'sunset_core_add_editor_styles' );
/**
 * Add styles for Gutenberg
 */
function sunset_core_add_editor_styles() {
	add_editor_style( 'dist/blocks.style.build.css' );
}

/**
 * Register custom block category
 * @see https://gutenberghub.com/how-to-create-custom-block-category/
 */
if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
	add_filter( 'block_categories_all', 'sunset_core_register_block_category' );
} else {
	add_filter( 'block_categories', 'sunset_core_register_block_category' );
}
function sunset_core_register_block_category( $categories ) {

    // Adding a new category.
	$categories[] = array(
		'slug'  => 'sunset-core',
		'title' => 'Sunset',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="512px" height="512px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"> <g><path style="opacity:0.962" fill="#000000" d="M 252.5,81.5 C 256.684,80.7036 259.85,82.037 262,85.5C 262.667,104.833 262.667,124.167 262,143.5C 257.667,147.5 253.333,147.5 249,143.5C 248.333,124.167 248.333,104.833 249,85.5C 249.69,83.6498 250.856,82.3164 252.5,81.5 Z"/></g> <g><path style="opacity:0.899" fill="#000000" d="M 136.5,112.5 C 139.199,112.466 141.699,113.133 144,114.5C 153.828,130.658 162.994,147.158 171.5,164C 171.011,168.489 168.511,170.989 164,171.5C 161.113,171.097 158.779,169.764 157,167.5C 148.667,152.833 140.333,138.167 132,123.5C 130.393,118.438 131.893,114.772 136.5,112.5 Z"/></g> <g><path style="opacity:0.899" fill="#000000" d="M 370.5,112.5 C 377.685,112.848 380.519,116.515 379,123.5C 370.667,138.167 362.333,152.833 354,167.5C 348.851,172.996 344.184,172.663 340,166.5C 339.333,164.833 339.333,163.167 340,161.5C 348.333,146.833 356.667,132.167 365,117.5C 366.107,114.887 367.94,113.22 370.5,112.5 Z"/></g> <g><path style="opacity:0.958" fill="#000000" d="M 511.5,318.5 C 511.5,321.167 511.5,323.833 511.5,326.5C 510.373,327.122 509.373,327.955 508.5,329C 339.833,329.667 171.167,329.667 2.5,329C 1.62656,327.955 0.626561,327.122 -0.5,326.5C -0.5,323.833 -0.5,321.167 -0.5,318.5C 0.626561,317.878 1.62656,317.045 2.5,316C 41.8319,315.5 81.1653,315.333 120.5,315.5C 127.689,260.958 156.689,223.125 207.5,202C 268.944,182.815 320.777,197.315 363,245.5C 379.091,266.03 388.258,289.364 390.5,315.5C 429.835,315.333 469.168,315.5 508.5,316C 509.373,317.045 510.373,317.878 511.5,318.5 Z M 243.5,208.5 C 295.538,206.462 335.038,227.462 362,271.5C 369.598,285.226 374.098,299.893 375.5,315.5C 295.5,315.5 215.5,315.5 135.5,315.5C 141.844,271.958 164.51,240.124 203.5,220C 216.425,214.273 229.759,210.44 243.5,208.5 Z"/></g> <g><path style="opacity:0.899" fill="#000000" d="M 49.5,198.5 C 51.8568,198.337 54.1902,198.503 56.5,199C 71.1667,207.333 85.8333,215.667 100.5,224C 105.996,229.149 105.663,233.816 99.5,238C 97.8333,238.667 96.1667,238.667 94.5,238C 78.6702,229.253 63.0035,220.253 47.5,211C 44.5567,206.27 45.2233,202.103 49.5,198.5 Z"/></g> <g><path style="opacity:0.9" fill="#000000" d="M 454.5,198.5 C 463.977,198.133 466.977,202.299 463.5,211C 447.996,220.253 432.33,229.253 416.5,238C 410.768,239.132 407.435,236.799 406.5,231C 406.934,228.684 407.934,226.684 409.5,225C 424.563,216.14 439.563,207.307 454.5,198.5 Z"/></g> <g><path style="opacity:0.913" fill="#000000" d="M 85.5,364.5 C 120.835,364.333 156.168,364.5 191.5,365C 195.821,365.321 198.155,367.655 198.5,372C 198.205,374.923 196.871,377.257 194.5,379C 157.5,379.667 120.5,379.667 83.5,379C 79.7885,376.487 78.6218,372.987 80,368.5C 81.4998,366.531 83.3331,365.198 85.5,364.5 Z"/></g> <g><path style="opacity:0.916" fill="#000000" d="M 244.5,364.5 C 304.834,364.333 365.168,364.5 425.5,365C 431.616,367.321 433.116,371.488 430,377.5C 429.25,378.126 428.416,378.626 427.5,379C 365.5,379.667 303.5,379.667 241.5,379C 236.136,372.914 237.136,368.08 244.5,364.5 Z"/></g> <g><path style="opacity:0.911" fill="#000000" d="M 144.5,414.5 C 172.169,414.333 199.835,414.5 227.5,415C 233.653,419.252 233.986,423.919 228.5,429C 200.167,429.667 171.833,429.667 143.5,429C 140.634,427.607 139.301,425.274 139.5,422C 139.426,418.256 141.092,415.756 144.5,414.5 Z"/></g> <g><path style="opacity:0.911" fill="#000000" d="M 273.5,414.5 C 304.502,414.333 335.502,414.5 366.5,415C 371.687,417.867 372.854,422.033 370,427.5C 369.25,428.126 368.416,428.626 367.5,429C 335.833,429.667 304.167,429.667 272.5,429C 268.788,426.487 267.622,422.987 269,418.5C 270.397,416.93 271.897,415.596 273.5,414.5 Z"/></g> </svg>'
	);

	return $categories;
}

add_action('admin_notices', 'sunset_core_no_theme_admin_notice');
/**
 * Add notice to wp-admin if no sunset-theme disabled
 */
function sunset_core_no_theme_admin_notice() {
	$theme = wp_get_theme(); // gets the current theme
	$theme_url = 'https://wordpress.org/themes/my-sunset/';
	if ( 'My Sunset' !== $theme->name && 'My Sunset' !== $theme->parent_theme ) {
		echo wp_kses('<div data-dismissible="disable-done-notice-forever" class="notice notice-warning is-dismissible">
			<p>'.sprintf(__('Since you enabled sunset-core plugin, then you need to enable <a href="%1$s" target="_blank">My Sunset theme</a> too','sunset-core'), $theme_url).'&nbsp;😁</p>
		</div>', 'post');
	}
}

/**
 * Add Meta Box for Gutenberg with extra page/post options
 */
abstract class Sunset_Core_Meta_Box {
 
    /**
     * Set up and add the meta box.
     */
    public static function add() {
		add_meta_box(
			'sunset_page_custom_options',          // Unique ID
			__('Page options', 'sunset-core'), // Box title
			[ self::class, 'html' ],   // Content callback, must be of type callable
			'page',                  // Post type
			'side',
			'low',
			null
		);
		add_meta_box(
			'sunset_subtitle_options',          // Unique ID
			__('Subtitle', 'sunset-core'), // Box title
			[ self::class, 'html_subtitle' ],   // Content callback, must be of type callable
			array('page', 'post'),                  // Post type
			'side',
			'low',
			null
		);
    }
 
    /**
     * Save the meta box selections.
     *
     * @param int $post_id  The post ID.
     */
    public static function save( int $post_id ) {
	
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

        if ( array_key_exists( 'page_header_type', $_POST ) && $_POST['page_header_type'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_page_header_type',
                sanitize_text_field($_POST['page_header_type'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_page_header_type' );
		}

		if ( array_key_exists( 'page_header_animation_type', $_POST ) && $_POST['page_header_animation_type'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_page_header_animation_type',
                sanitize_text_field($_POST['page_header_animation_type'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_page_header_animation_type' );
		}

		if ( array_key_exists( 'page_show_breadcrumbs', $_POST ) && $_POST['page_show_breadcrumbs'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_page_show_breadcrumbs',
                sanitize_text_field($_POST['page_show_breadcrumbs'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_page_show_breadcrumbs' );
		}

		if ( array_key_exists( 'page_hide_title', $_POST ) && $_POST['page_hide_title'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_page_hide_title',
                sanitize_text_field($_POST['page_hide_title'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_page_hide_title' );
		}

		if ( array_key_exists( 'page_hide_paddings', $_POST ) && $_POST['page_hide_paddings'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_page_hide_paddings',
                sanitize_text_field($_POST['page_hide_paddings'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_page_hide_paddings' );
		}

		if ( array_key_exists( 'post_subtitle', $_POST ) && $_POST['post_subtitle'] !== '' ) {
            update_post_meta(
                $post_id,
                '_sunset_post_subtitle',
                wp_kses_post($_POST['post_subtitle'])
            );
        } else {
			delete_post_meta( $post_id, '_sunset_post_subtitle' );
		}

		return $post_id;
    }
 
    /**
     * Display the meta box HTML to the user.
     *
     * @param \WP_Post $post   Post object.
     */
    public static function html( $post ) {
		$header_choices = array (
			''   => __('Inherit from theme\'s options', 'sunset-core'),
			'default'   => __('Default', 'sunset-core'),
			'cover'   => __('Cover', 'sunset-core'),
			'split'   => __('Split', 'sunset-core'),
			'big-text'   => __('Big text', 'sunset-core'),
		);
		$animation_choices = array (
			''   => __('Inherit from theme\'s options', 'sunset-core'),
			'none'   => __('None', 'sunset-core'),
			'slide-up'   => __('Slide Up', 'sunset-core'),
			'slide-down'   => __('Slide Down', 'sunset-core'),
			'typing'   => __('Typing', 'sunset-core'),
			'zoom-in'   => __('Zoom In', 'sunset-core'),
			'fade-in'   => __('Fade In', 'sunset-core'),
		);
		$breadcrumbs_choices = array (
			''   => __('Inherit from theme\'s options', 'sunset-core'),
			'1'   => __('Show', 'sunset-core'),
			'0'   => __('Hide', 'sunset-core'),
		);
		$hide_title_choices = array (
			'0'   => __('Show', 'sunset-core'),
			'1'   => __('Hide', 'sunset-core'),
		);
		$hide_padding_choices = array (
			''   => __('Inherit from theme\'s options', 'sunset-core'),
			'1'   => __('Show', 'sunset-core'),
			'0'   => __('Hide', 'sunset-core'),
		);
        $page_header_type = get_post_meta( $post->ID, '_sunset_page_header_type', true );
        $page_header_animation_type = get_post_meta( $post->ID, '_sunset_page_header_animation_type', true );
        $page_show_breadcrumbs = get_post_meta( $post->ID, '_sunset_page_show_breadcrumbs', true );
        $page_hide_title = get_post_meta( $post->ID, '_sunset_page_hide_title', true );
        $page_hide_paddings = get_post_meta( $post->ID, '_sunset_page_hide_paddings', true );
        ?>
        <p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label class="post-attributes-label" for="page_header_type"><?php _e('Page header type', 'sunset-core'); ?></label>
		</p>
        <select name="page_header_type" id="page_header_type" class="postbox">
			<?php foreach ($header_choices as $key => $value) : ?>
            	<option value="<?php echo esc_attr($key); ?>" <?php selected( $page_header_type, $key ); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select>
		<p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label class="post-attributes-label" for="page_header_animation_type"><?php _e('Page header animation type', 'sunset-core'); ?></label>
		</p>
		<select name="page_header_animation_type" id="page_header_animation_type" class="postbox">
			<?php foreach ($animation_choices as $key => $value) : ?>
            	<option value="<?php echo esc_attr($key); ?>" <?php selected( $page_header_animation_type, $key ); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select>
		<p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label class="post-attributes-label" for="page_show_breadcrumbs"><?php _e('Show breadcrumbs', 'sunset-core'); ?></label>
		</p>
		<select name="page_show_breadcrumbs" id="page_show_breadcrumbs" class="postbox">
			<?php foreach ($breadcrumbs_choices as $key => $value) : ?>
            	<option value="<?php echo esc_attr($key); ?>" <?php selected( $page_show_breadcrumbs, $key ); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select>
		<p class="post-attributes-label-wrapper page-template-label-wrapper" style="<?php echo esc_attr($page_header_type === 'default' ? '' : 'display: none'); ?>">
			<label class="post-attributes-label" for="page_hide_title"><?php _e('Hide title?', 'sunset-core'); ?></label>
		</p>
		<select name="page_hide_title" id="page_hide_title" class="postbox">
			<?php foreach ($hide_padding_choices as $key => $value) : ?>
            	<option value="<?php echo esc_attr($key); ?>" <?php selected( $page_hide_title, $key ); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select>
		<select name="page_hide_paddings" id="page_hide_paddings" class="postbox" style="<?php echo esc_attr($page_header_type === 'default' ? '' : 'display: none'); ?>">
			<?php foreach ($hide_title_choices as $key => $value) : ?>
            	<option value="<?php echo esc_attr($key); ?>" <?php selected( $page_hide_paddings, $key ); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select>
		<script>
			jQuery(document).ready(function($){
				$("#page_header_type").on("change", function(){
					let val = $(this).val();
					if (val === 'default') {
						$("#page_hide_title").show();
						$("#page_hide_title").prev().show();
					} else {
						$("#page_hide_title").hide();
						$("#page_hide_title").prev().hide();
					}
				})
			})
		</script>
        <?php
    }

	/**
     * Display the meta box HTML to the user.
     *
     * @param \WP_Post $post   Post object.
     */
    public static function html_subtitle( $post ) {
		
        $post_subtitle = get_post_meta( $post->ID, '_sunset_post_subtitle', true );
        ?>
        <p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label class="post-attributes-label" for="post_subtitle"><?php _e('Subtitle', 'sunset-core'); ?></label>
		</p>
		<textarea name="post_subtitle" id="post_subtitle" class="postbox"><?php echo esc_html($post_subtitle); ?></textarea>        
        <?php
    }
}

// add legacy meta boxes if Gutenberg disabled
add_action( 'wp', function() {
	if (is_admin() && !sunset_core_is_gutenberg_editor()) {
		add_action( 'add_meta_boxes', [ 'Sunset_Core_Meta_Box', 'add' ], 999 );
		add_action( 'save_post', [ 'Sunset_Core_Meta_Box', 'save' ] );
	}
}, 999 );


add_action( 'init', 'sunset_core_custom_post_meta');
/**
 * Register custom page/post options
 */
function sunset_core_custom_post_meta () {
	register_post_meta(
		'page',
		'_sunset_page_header_type',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'page',
		'_sunset_page_header_animation_type',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'page',
		'_sunset_page_show_breadcrumbs',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'page',
		'_sunset_page_hide_title',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'page',
		'_sunset_page_hide_paddings',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'post',
		'_sunset_post_subtitle',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'wp_kses_post',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);

	register_post_meta(
		'page',
		'_sunset_post_subtitle',
		array (
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			}
		)
	);
}

add_action( 'category_add_form_fields', 'sunset_core_add_term_fields' );
/**
 * Add custom term fields to the Add New Term Screen for post categories
 * 
 * @param string $taxonomy - name of the taxonomy
 */
function sunset_core_add_term_fields( $taxonomy ) {
	echo wp_kses('<div class="form-field">
	<label for="sunset-category-background">'.__("Category background", "sunset-core").'</label>
	<input type="text" name="sunset_category_background" id="sunset-category-background" />
	<p>'.__("Select background color for the category or leave blank for default.", "sunset-core").'</p>
	</div>', 'post');
	?>
	<script>
		jQuery(document).ready(function($){
			$("#sunset-category-background").wpColorPicker();			
		})
	</script>
	<?php
}

add_action( 'category_edit_form_fields', 'sunset_core_edit_term_fields', 10, 2 );
/**
 * Add custom term fields to the Edit Term Screen for post categories
 * 
 * @param object $term - term's object
 * @param string $taxonomy - name of the taxonomy
 */
function sunset_core_edit_term_fields( $term, $taxonomy ) {
	$value = get_term_meta( $term->term_id, '_sunset_category_background', true );
	echo wp_kses('
		<tr class="form-field term-category-background-wrap">
			<th scope="row"><label for="sunset-category-background">'.__("Category background", "sunset-core").'</label></th>
			<td><input name="sunset_category_background" id="sunset-category-background" type="text" value="'.esc_attr( $value ).'" size="40">
				<p class="description">'.__("Select background color for the category or leave blank for default.", "sunset-core").'</p>
			</td>
		</tr>
	', 'post');
	?>
	<script>
		jQuery(document).ready(function($){
			$("#sunset-category-background").wpColorPicker();			
		})
	</script>
	<?php
}

add_action( 'created_category', 'sunset_core_save_term_fields' );
add_action( 'edited_category', 'sunset_core_save_term_fields' );
/**
 * Save custom term fields
 * 
 * @param integer $term_id - ID of the term
 */
function sunset_core_save_term_fields( $term_id ) {
	
	if ( array_key_exists( 'sunset_category_background', $_POST ) && $_POST['sunset_category_background'] !== '' ) {
		update_term_meta(
			$term_id,
			'_sunset_category_background',
			sanitize_hex_color( $_POST[ 'sunset_category_background' ] )
		);
	} else {
		delete_term_meta( $term_id, '_sunset_category_background' );
	}
}

/**
 * Check if Gutenberg enabled on current page
 * you have to call this after the admin_init hook
 * 
 * @return boolean
 */
function sunset_core_is_gutenberg_editor() {	
    if( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) { 
        return true;
    }
    $current_screen = get_current_screen();
    if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
        return true;
    }
    return false;
}

//add_filter('use_block_editor_for_post', '__return_false', 5);


add_filter( 'render_block', 'sunset_core_gutenberg_block_html', 10, 2 );
/**
 * Custom html for gutenberg elements
 */
function sunset_core_gutenberg_block_html( $block_content, $block ) {

	if ( 'core/image' === $block['blockName'] ) {
		if ( isset($block['attrs']['imageMask']) && $block['attrs']['imageMask'] === 'custom' && $block['attrs']['imageMaskURL'] !== '' && $block['attrs']['imageMaskID'] !== '' ) {
			$custom_css = '
				<style>
				.image-mask-id-'.$block['attrs']['imageMaskID'].' > img {
					-webkit-mask-image: url('.$block['attrs']['imageMaskURL'].');
				}
				</style>
			';
			$block_content = $custom_css.$block_content; // css MUST be before $block_content
		}
	}

	return $block_content;
}

add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'widget_custom_html_content', 'do_shortcode' );
add_filter( 'widget_text', 'shortcode_unautop');

add_shortcode('sunset_year', 'sunset_core_year_shortcode');
/**
 * Shortcode for current year
 */
function sunset_core_year_shortcode(){
	return date('Y');
}

add_shortcode('sunset_socials', 'sunset_core_socials_shortcode');
/**
 * Shortcode for social links
 */
function sunset_core_socials_shortcode(){
	if ( function_exists( 'sunset_social_links' ) ) {
		ob_start();
		sunset_social_links(false, true);
		return ob_get_clean();
	}
	return 'no';
}

add_action( 'wp_footer', 'sunset_core_svg_filters' );
/**
 * Add SVG filters for hover effect
 */
function sunset_core_svg_filters() {
?>
<svg style="display: none">
    <filter id="sunset_core_svg_r"><feColorMatrix
        type="matrix"
        values="1 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0 "/>
    </filter>
    <filter id="sunset_core_svg_g"><feColorMatrix
        type="matrix"
        values="0 0 0 0 0  0 1 0 0 0  0 0 0 0 0  0 0 0 1 0 "/>
    </filter>
    <filter id="sunset_core_svg_b"><feColorMatrix
        type="matrix"
        values="0 0 0 0 0  0 0 0 0 0  0 0 1 0 0  0 0 0 1 0 "/>
    </filter>
</svg>
<?php
}