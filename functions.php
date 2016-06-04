<?php
load_theme_textdomain('text_domain');
/*
Theme Name: 	MPAT
Theme URI: 		http://mpat.fokus.fraunhofer.de/wordpress/
Description: 	Multi-platform Application Toolkit
Version: 		0.1
Author: 		Fraunhofer Fokus
Author URI: 	http://www.fokus.fraunhofer.de/go/fame
Tags: 			hbbtv
*/

/* Required external files */

require_once('external/mpat-utilities.php');
include_once 'mpat-button-control.php';
include_once 'mpat-shortcodes.php';
include_once 'mpat-metaboxes.php';


//======================================================================
// INITIALIZE
//======================================================================

/* Theme specific settings */
add_theme_support('post-thumbnails');

//-----------------------------------------------------
//  Actions and Filters
//-----------------------------------------------------

//Enqueue global scripts (frontend and backend)
//add_filter('wp_default_scripts', 'mpat_edit_default_scripts');
//Enqueue admin scripts (backend)
add_action('admin_enqueue_scripts', 'mpat_admin_scripts_init');

//Enqueue user scripts (frontend)
add_action('wp_enqueue_scripts', 'mpat_enqueue_frontend_scripts');

add_action('init', 'mpat_register_menus');
add_action('init', 'mpat_create_post_types');
add_action('init', 'mpat_register_shortcodes');

//disable emojis
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );


//Register scripts for customizer
add_action('customize_register', 'mpat_add_customizer_custom_controls');
add_action('customize_register', 'mpat_customizer_register');
add_action('customize_controls_enqueue_scripts', 'mpat_enqueue_customizer_admin_scripts');

add_action('admin_menu', 'mpat_button_control_menu');

add_action('wp_head', 'mpat_customizer_css');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'wp_msapplication_TileImage');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);

add_filter('body_class', array('Mpat_Utilities', 'add_slug_to_body_class'));

// Remove Canonical Link Added By Yoast WordPress SEO Plugin
add_filter('wpseo_canonical', function () {
    return false;
});

add_filter('default_page_template_title', function () {
    return __('Full Page');
});

add_filter('upload_mimes', function ($mimes=array()) {
  $mimes['vtt'] = 'text/vtt';
  return $mimes; 
});

//-----------------------------------------------------
//  Enqueue Scripts and Styles
//-----------------------------------------------------

//Global scripts
/*function mpat_edit_default_scripts(&$scripts){
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
}*/

//Admin scripts
function mpat_admin_scripts_init($hook)
{
    add_thickbox();

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core',false,array('jquery'));
    wp_enqueue_script('jquery-ui-sortable',false,array('jquery-ui-core'));

    wp_enqueue_style('jquery-ui', get_template_directory_uri() . '/backend/css/jquery-ui.min.css');
    wp_enqueue_style('jquery-ui-theme', get_template_directory_uri() . '/backend/css/jquery-ui.theme.css');

    wp_register_script('mpat-media-uploader', get_template_directory_uri() . '/backend/js/media-uploader.js', array('jquery', 'media-upload', 'thickbox'));
    wp_enqueue_script('mpat-media-uploader');

    wp_enqueue_style('thickbox');

    //Enqueue page specific scripts
    switch ($hook) {
        case 'toplevel_page_mpat_button_control':
            wp_enqueue_script('mpat-button-control-script', get_template_directory_uri() . '/backend/js/mpat-button-control.js', array('jquery'));
            wp_enqueue_style('mpat-button-control-style', get_template_directory_uri() . '/backend/css/mpat-button-control-style.css');
            break;
        case 'widgets.php':

    }
}

//Frontend scripts
function mpat_enqueue_frontend_scripts()
{
    global $post;
    global $page_content_key;;
    if ($post!=null){
        $postMeta = get_post_meta($post->ID,$page_content_key,true);
        if ($postMeta==='') $postMeta = array();
    }

    //Javascripts

    //Libraries
    wp_register_script('hbbtvlib', get_template_directory_uri() . '/frontend/js/general/hbbtvlib.js');

    wp_register_script('keycodes', get_template_directory_uri() . '/frontend/js/general/keycodes.js');

    wp_register_script('mpat-navigation', get_template_directory_uri() . '/frontend/js/mpat-navigation.js', array('hbbtvlib', 'keycodes', 'jquery'));

    //Final Scripts

    wp_enqueue_script('nav-menu', get_template_directory_uri() . '/frontend/js/nav-menu.js', array('mpat-navigation'));

    wp_enqueue_script('json2', get_template_directory_uri() . '/frontend/js/scribble/json2.js', array('jquery'));

    wp_enqueue_script('social', get_template_directory_uri() . '/frontend/js/modules/social.js', array('jquery'));

    wp_enqueue_script('jquery-timeago', get_template_directory_uri() . '/frontend/js/scribble/jquery.timeago.js', array('jquery'));

    //template specific styles and scripts
    if (is_page_template('page-templates/template-media-gallery.php')) {

        wp_enqueue_script('mpat-gallery', get_template_directory_uri() . '/frontend/js/gallery-template.js', array('mpat-navigation'));

        wp_enqueue_script('mpat-api', get_template_directory_uri() . '/frontend/js/api.js', array('jquery'));

        wp_enqueue_style('mpat-frontend-style', get_template_directory_uri() . '/style.css');

    } else if (is_page_template('page-templates/template-grid.php')){

        wp_enqueue_script('mpat-grid', get_template_directory_uri() . '/frontend/js/grid-template.js', array('mpat-navigation'));

        wp_enqueue_style('mpat-grid', get_template_directory_uri() . '/frontend/css/grid-template.css');

    } else if (is_page_template('page-templates/template-single-media.php')){

        wp_enqueue_script('mpat-single-media', get_template_directory_uri() . '/frontend/js/single-media-template.js', array('mpat-navigation'));

        wp_enqueue_style('mpat-single-media', get_template_directory_uri() . '/frontend/css/single-media-template.css');

        wp_enqueue_style('mpat-frontend-style', get_template_directory_uri() . '/style.css');

    } else if (is_page_template('page-templates/template-red-button.php')){

        wp_enqueue_script('mpat-red-button', get_template_directory_uri() . '/frontend/js/red-button-template.js', array('mpat-navigation'));
        wp_enqueue_style('mpat-red-button', get_template_directory_uri() . '/frontend/css/red-button-template.css');


    } else {

        wp_enqueue_script('mpat-column-navigation', get_template_directory_uri() . '/frontend/js/column-template.js', array('mpat-navigation'));

        wp_enqueue_style('mpat-frontend-style', get_template_directory_uri() . '/style.css');

    }

    $contains_content_type = function($postMeta,$contenttype){
        if (!isset($postMeta)) $postMeta = array();
        foreach ($postMeta as $box){
            if (isset($box['contenttype']) && $box['contenttype']===$contenttype) {
                return $box;
            }
        }
        return false;
    };

    //contentbox specific scripts

    $box = $contains_content_type($postMeta,'360');
    if ($box!==false){
        add_action('wp_head',function() use ($box){
            ?><script type="text/javascript">
                var SRV_BASE = "<?php echo $box['data']['server_url'];?>";
                var LAUNCH_SERVER_URL = SRV_BASE+"/streamer360/launch_video3.php";
                var META_DATA_URL = SRV_BASE+"/streamer360/meta.php";
                var META_PUT_URL = SRV_BASE+"/streamer360/put_meta.php";
            </script><?php
        });
        wp_enqueue_script('util360', get_template_directory_uri() . '/frontend/js/modules/360/util.js', array('jquery'));
        wp_enqueue_script('video360', get_template_directory_uri() . '/frontend/js/modules/360/video.js', array('jquery'));
        wp_enqueue_script('controller360', get_template_directory_uri() . '/frontend/js/modules/360/controller.js', array('jquery'));
    }

}

function mpat_enqueue_customizer_admin_scripts()
{
    wp_enqueue_script('customizer-admin', get_template_directory_uri() . '/backend/js/customizer-admin.js', array('jquery'),false,true);
    wp_enqueue_style('mpat-customizer-controls', get_template_directory_uri() . '/backend/css/customizer-controls.css');
}


//======================================================================
// CREATE THE MPAT STUFF
//======================================================================

// Register the primary menu
function mpat_register_menus()
{
    register_nav_menus(array(
        'primary-menu' => __('Primary Menu', 'mpat'),
        //'footer-menu' =>__( 'Footer Menu', 'mpat' ),
    ));
}

// Create 'Gallery Item','Popup' and 'Function'
function mpat_create_post_types()
{
    register_post_type(
        'mpat_gallery_item',
        array(
            'labels' => array(
                'name' => __('Gallery Items'),
                'singular_name' => __('Gallery Item'),
                'add_new_item' => __('Create a new Gallery Item')
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-format-gallery',
            'taxonomies' => array('post_tag')
        )
    );
    register_post_type(
        'mpat_popup',
        array(
            'labels' => array(
                'name' => __('Popups'),
                'singular_name' => __('Popup'),
                'add_new_item' => __('Create a new Popup')
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-category',
        )
    );
    register_post_type(
        'mpat_function',
        array(
            'labels' => array(
                'name' => __('Functions'),
                'singular_name' => __('Function'),
                'add_new_item' => __('Create a new Function')
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-media-code',
        )
    );
    remove_post_type_support('mpat_function', 'editor');
    remove_post_type_support('mpat_gallery_item', 'editor');
    remove_post_type_support('page', 'editor');
    register_taxonomy_for_object_type('post_tag', 'page');
}

// Register shortcodes specified in mpat-shortcodes.php
function mpat_register_shortcodes()
{
    add_shortcode('gallery', 'mpat_gallery_shortcode');
}
//-----------------------------------------------------
//  Setup the Customiser (Under Apperance->Customise)
//-----------------------------------------------------

function mpat_add_customizer_custom_controls( $wp_customize )
{

    class MPAT_Customize_Alpha_Color_Control extends WP_Customize_Control
    {

        public $type = 'alphacolor';
        public $palette = true;
        public $default = 'rgba(255,255,255,0.9)';

        protected function render()
        {
            $id = 'customize-control-' . str_replace('[', '-', str_replace(']', '', $this->id));
            $class = 'customize-control customize-control-' . $this->type; ?>
            <li id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
                <?php $this->render_content(); ?>
            </li>
        <?php }

        public function render_content()
        { ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <input type="text" data-palette="<?php echo $this->palette; ?>"
                       data-default-color="<?php echo $this->default; ?>"
                       value="<?php echo intval($this->value()); ?>"
                       class="mpat-color-control" <?php $this->link(); ?> />
            </label>
        <?php }
    }

}

function mpat_customizer_register( $wp_customize )
{

    class MPAT_Customize_Textarea_Control extends WP_Customize_Control
    {
        public $type = 'textarea';

        public function render_content()
        {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                    <textarea rows="5"
                              style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
            </label>
            <?php
        }
    }

    /* Settings Panel */
    $wp_customize->add_panel( 'settings', array(
      'title' => __( 'Theme Settings', 'mpat' ),
      'description' => __( 'Modify the theme settings' ),
      'priority' => 160,
    ) );


    /* Logo */
    $wp_customize->add_section( 'mpat_images', array(
        'title' => __('Logo', 'mpat'),
        'description' => __( 'Modify the theme logo' ),
        'panel' => 'settings',
    ) );
    $wp_customize->add_setting( 'logo_image', array(
        'type' => 'theme_mod',
        'default' => get_template_directory_uri() . '/backend/assets/logo_mpat.png',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_image', array(
        'label' => __( 'max. height: 85px, max. width: 250px', 'mpat' ),
        'section' => 'mpat_images',
        'settings' => 'logo_image',
        'mime_type' => 'image',
    ) ) );

    /* Background Image */
    $wp_customize->add_section( 'mpat_background_images', array(
        'title' => __( 'Background Image', 'mpat' ),
        'description' => __( 'Modify the theme Background Image' ),
        'panel' => 'settings',
    ) );
    $wp_customize->add_setting( 'bg_image', array(
        'type' => 'theme_mod',
        'default' => get_template_directory_uri() . '/shared/assets/bgr_mpat.png',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bg_image', array(
        'label' => __( 'height: 1280px, width: 720px', 'mpat' ),
        'section' => 'mpat_background_images',
        'settings' => 'bg_image',
        'mime_type' => 'image',
    ) ) );

    /* Font */
    $wp_customize->add_section( 'mpat_font', array(
        'title' => __( 'Font', 'mpat' ),
        'description' => __( 'Modify the size of the fonts' ),
        'panel' => 'settings',
    ) );
    $wp_customize->add_setting( 'title_size', array(
        'type' => 'theme_mod',
        'default' => '24',
    ) );
    $wp_customize->add_setting( 'title_color', array(
        'type' => 'theme_mod',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'font_size', array(
        'type' => 'theme_mod',
        'default' => '24',
    ) );
    $wp_customize->add_setting( 'font_color', array(
        'type' => 'theme_mod',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'link_color', array(
        'type' => 'theme_mod',
        'default' => '#00688B',
    ) );
    $wp_customize->add_control( 'title_size', array(
        'label' => __( 'Edit Title Size in px' ),
        'section' => 'mpat_font',
        'type' => 'text',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'title_color', array(
        'label' => __( 'Edit Title Color', 'mpat' ),
        'section' => 'mpat_font',
    ) ) );
    $wp_customize->add_control( 'font_size', array(
        'label' => __( 'Edit Font Size in px' ),
        'section' => 'mpat_font',
        'type' => 'text',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'font_color', array(
        'label' => __( 'Edit Font Color', 'mpat' ),
        'section' => 'mpat_font',
    ) ) );
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'link_color', array(
        'label' => __( 'Edit Links Color', 'mpat' ),
        'section' => 'mpat_font',
    ) ) );

    /* General Colors */
    $wp_customize->add_section( 'mpat_colors', array(
        'title' => __( 'General Colors', 'mpat'),
        'description' => __( 'Modify the theme colors' ),
        'panel' => 'settings',
    ) );
    $wp_customize->add_setting( 'background_color', array(
        'type' => 'theme_mod',
        'default' => '#fff',
    ) );
    $wp_customize->add_setting( 'title_background_color', array(
        'type' => 'theme_mod',
    ) );;

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
        'label' => __( 'Edit Background Color', 'mpat' ),
        'section' => 'mpat_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'title_background_color', array(
        'label' => __( 'Edit Title Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_colors',
    ) ) );


    /* Module Colors */
    $wp_customize->add_section( 'mpat_module_colors', array(
        'title' => __( 'Module Settings', 'mpat' ),
        'description' => __( 'Modify the module colors' ),
    ) );
    $wp_customize->add_setting( 'module_title_color', array(
        'type' => 'option',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'module_header_bg_color', array(
        'type' => 'option',
    ) );
    $wp_customize->add_setting( 'module_color', array(
        'type' => 'option',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'module_title_color', array(
        'label' => __( 'Edit Module Header Title Color', 'mpat' ),
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'module_header_bg_color', array(
        'label' => __( 'Edit Module Header Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control($wp_customize, 'module_color', array(
        'label' => __( 'Edit Module Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors',
    ) ) );


    /* Module Active Colors */
    $wp_customize->add_setting( 'module_title_color_active', array(
        'type' => 'option',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'module_header_bg_color_active', array(
        'type' => 'option',
    ) );
    $wp_customize->add_setting( 'module_color_active', array(
        'type' => 'option',
    ) );
    $wp_customize->add_setting( 'module_text_color_active', array(
        'type' => 'option',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'module_font_color_active_highlight', array(
        'type' => 'option',
    ) );
    $wp_customize->add_setting( 'module_color_active_highlight', array(
        'type' => 'option',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'module_title_color_active', array(
        'label' => __( 'Edit Selected Module Header Title Color', 'mpat' ),
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'module_header_bg_color_active', array(
        'label' => __( 'Edit Selected Module Header Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'module_color_active', array(
        'label' => __( 'Edit Selected Module Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors,'
    ) ) );
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'module_text_color_active', array(
        'label' => __( 'Edit Selected Module Text Color', 'mpat' ),
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control($wp_customize, 'module_font_color_active_highlight', array(
        'label' => __( 'Edit Selected Module Highlight Font Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'module_color_active_highlight', array(
        'label' => __( 'Edit Selected Module Highlight Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_module_colors',
    ) ) );

    /* Menu Colors */
    $wp_customize->add_section( 'mpat_menu_colors', array(
        'title' => __( 'Menu Settings', 'mpat' ),
        'description' => __( 'Modify the menu settings' ),
        'priority' => 202,
    ) );
    $wp_customize->add_setting( 'menu_text', array(
        'type' => 'option',
        'default' => 'Menu',
    ) );
    $wp_customize->add_setting( 'hide_text', array(
        'type' => 'option',
        'default' => 'Hide',
    ) );
    $wp_customize->add_setting( 'menu_background_color', array(
        'type' => 'option',
    ) );
    $wp_customize->add_setting( 'menu_font_color', array(
        'type' => 'option',
        'default' => '#666',
    ) );
    $wp_customize->add_setting( 'footer_background_color', array(
        'type' => 'option',
    ) );
    $wp_customize->add_control( 'menu_text', array(
        'label' => 'Change text for the Primary Menu',
        'section' => 'mpat_menu_colors',
        'type' => 'text',
    ) );
    $wp_customize->add_control( 'hide_text', array(
        'label' => 'Change text to hide the app',
        'section' => 'mpat_menu_colors',
        'type' => 'text',
    ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'menu_background_color', array(
        'label' => __( 'Edit Primary Menu Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_menu_colors',
    ) ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu_font_color', array(
        'label' => __( 'Edit Font Color', 'mpat' ),
        'section' => 'mpat_menu_colors',
        'settings' => 'menu_font_color',
    ) ) );
    $wp_customize->add_control( new MPAT_Customize_Alpha_Color_Control( $wp_customize, 'footer_background_color', array(
        'label' => __( 'Edit Menu Background Color', 'mpat' ),
        'palette' => true,
        'section' => 'mpat_menu_colors',
    ) ) );


    /* Frontpage */

    $wp_customize->remove_section( 'static_front_page' );
    update_option( 'show_on_front', 'page' );

    $wp_customize->add_section( 'mpat_front_page', array(
        'title' => __('Frontpage'),
        'priority' => 120,
        'description' => __('Select the page to be displayed on the front'),
    ) );

    $wp_customize->add_setting( 'page_on_front', array(
        'type' => 'option',
        'capability' => 'manage_options',
    ) );

    $wp_customize->add_control( 'page_on_front', array(
        'label' => __('Frontpage'),
        'section' => 'mpat_front_page',
        'type' => 'dropdown-pages',
    ) );
}

function mpat_customizer_css()
{
    ?><style type="text/css">
        body {
            color: <?php echo get_theme_mod( 'font_color' ); ?>;
        }

        a {
            color: <?php echo get_theme_mod( 'link_color' ); ?>;
        }

        p {
            font-size: <?php echo get_theme_mod( 'font_size' ); ?>px;
        }

        #hbbtv_app {
            background-color: # <?php echo get_theme_mod( 'background_color' ); ?>;
            background-image: url("<?php echo get_theme_mod('bg_image'); ?> ");
        }

        #title {
            color: <?php echo get_theme_mod( 'title_color' ); ?>;
            background-color: <?php echo get_theme_mod( 'title_background_color' ); ?>;
        }

        .menuText {
            color: <?php echo get_theme_mod( 'menu_font_color' ); ?>;
        }

        footer {
            background-color: <?php echo get_theme_mod( 'footer_background_color' ); ?>;
        }

        footer, footer a, #primary-menu-wrap a {
            color: <?php echo get_theme_mod( 'menu_font_color' ); ?>;
        }

        #primary-menu-wrap {
            background-color: <?php echo get_theme_mod( 'menu_background_color' ); ?>;
        }

        .contentHeader {
            color: <?php echo get_theme_mod( 'module_title_color' ); ?>;
            background-color: <?php echo get_theme_mod( 'module_header_bg_color' ); ?>;
            font-size: <?php echo get_theme_mod( 'title_size' ); ?>px;
        }

        .active .contentHeader {
            color: <?php echo get_theme_mod( 'module_title_color_active' ); ?>;
            background-color: <?php echo get_theme_mod( 'module_header_bg_color_active' ); ?> !important;
        }

        .textContent, .imageContent, .galleryContent, .socialContent, .socialPopup, #item-info {
            background-color: <?php echo get_theme_mod( 'module_color' ); ?>;
        }

        .active .textContent, .active .imageContent, .active .galleryContent, .active .socialContent {
            color: <?php echo get_theme_mod( 'module_text_color_active' ); ?>;
            background-color: <?php echo get_theme_mod( 'module_color_active' ); ?>;
        }

        .active .socialContent .activePost {
            color: <?php echo get_theme_mod( 'module_font_color_active_highlight' ); ?>;
            background-color: <?php echo get_theme_mod( 'module_color_active_highlight' ); ?>;
        }
    </style><?php
}

//-----------------------------------------------------
// Frontend Changes
//-----------------------------------------------------

/* Comments */
function mpat_comment($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    if ($comment->comment_approved == '1'):
        ?>
        <li>
            <article id="comment-<?php comment_ID() ?>">
                <?php echo get_avatar($comment); ?>
                <h4><?php comment_author_link() ?></h4>
                <time><a href="#comment-<?php comment_ID() ?>" pubdate><?php comment_date() ?>
                        at <?php comment_time() ?></a></time>
                <?php comment_text() ?>
            </article>
        </li>
    <?php endif;
}

function mpat_content_type($mime_type, $post_id)
{
    header('Content-type: application/vnd.hbbtv.xhtml+xml; charset=utf-8');
    // Process content here
    return $mime_type;
}

/* Customizer */


/* Menus */

//This class defines how the primary menu will look like
class mpat_walker_primary_menu extends Walker_Nav_Menu
{
    function start_el(&$output, $item, $depth = 0, $args = array(), $id=0)
    {
        global $wp_query;
        $indent = ($depth > 0 ? str_repeat("\t", $depth) : ''); // code indent

        $depth_classes = array(
            ($depth == 0 ? 'main-menu-item' : 'sub-menu-item'),
            ($depth >= 2 ? 'sub-sub-menu-item' : ''),
            ($depth % 2 ? 'menu-item-odd' : 'menu-item-even'),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr(implode(' ', $depth_classes));

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $class_names = esc_attr(implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item)));

        $output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ' class="menu-link ' . ($depth > 0 ? 'sub-menu-link' : 'main-menu-link') . '"';

        $item_output = $args->before;
        $item_output .= '<p onclick="$(this).navEnter()" ' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID);
        $item_output .= $description . $args->link_after;
        $item_output .= '</p>';

        $item_output .= '<script> jQuery(document).ready(function($){' . '$generalNav.nav("' . $button . '",function(){$("#menu-item-' . $item->ID . '").navEnter()});});</script>';
        if ($item->object === 'mpat_function') {
            $func = get_post($item->object_id);
            $item_output .= '<script> jQuery(document).ready(function($){if (!userFunctions) {userFunctions = new Object();} userFunctions.func' . $func->ID . ' = function(){' . get_post_meta($func->ID, '_mpat_functionContent', true) . '};});</script>';
        }

        $item_output .= $args->after;
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

//This class defines how the footer menu will look like
class mpat_walker_footer_menu extends Walker_Nav_Menu
{

    function start_el(&$output, $item, $depth = 0, $args = array(), $id=0)
    {
        $is_hide = get_post_meta($item->ID, '_menu_item_is_hide', true);
        $show = (get_post_meta($item->ID, '_menu_item_show_footer', true) === 'true') ? ' ' : 'style="display:none !important;" ';
        global $wp_query;
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array)$item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $output .= $indent . '<li ' . $show . 'id="menu-item-' . $item->ID . '" object-id="' . $item->object_id . '"' . $value . $class_names . '>';
        $button = get_post_meta($item->ID, '_menu_item_mpat_button', true);
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $description = !empty($item->description) ? '<span>' . esc_attr($item->description) . '</span>' : '';

        $item_output = $args->before;
        if ($button != '') {
            $item_output .= '<img src="' . get_template_directory_uri() . '/shared/assets/button' . $button . '.png"></img>';
        }
        $item_output .= '<p style="float:left" onclick="$(this).navEnter()" ' . $attributes . '>';

        if ($is_hide === 'true') {
            $hide_txt = get_theme_mod('hide_text');
            if (empty($hide_txt)) {
                $item_output .= 'Hide';
            } else {
                $item_output .= $hide_txt;
            }
        } else {
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID);
            $item_output .= $description . $args->link_after;
        }

        $item_output .= '</p>';
        if ($item->object === 'mpat_function') {
            $func = get_post($item->object_id);
            $item_output .= '<script> jQuery(document).ready(function($){console.log("userFunc registered");if (!userFunctions) {userFunctions = new Object();} userFunctions["func' . $func->ID . '"] = function(){' . get_post_meta($func->ID, '_mpat_functionContent', true) . '};});</script></li>';
        }

        $item_output .= '<script> jQuery(document).ready(function($){' .
            '$generalNav.nav("' . $button . '",function(){$("#menu-item-' . $item->ID . '").navEnter()});});</script>';

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

}

//======================================================================
// SETUP THE POST EDITORS
//======================================================================


//-----------------------------------------------------
//  Add meta boxes to page editor and custom post types
//-----------------------------------------------------

//Find out page template
$post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID'])?$_POST['post_ID']:"");
$template_file = get_post_meta($post_id, '_wp_page_template', TRUE);
switch ($template_file) {
    case 'default':
        add_action('add_meta_boxes', 'fullpage_add_meta_box');
        break;
    case 'page-templates/template-two-columns.php':
        add_action('add_meta_boxes', 'two_columns_add_meta_box');
        break;
    case 'page-templates/template-three-columns.php':
        add_action('add_meta_boxes', 'three_columns_add_meta_box');
        break;
    case 'page-templates/template-media-gallery.php':
        add_action('add_meta_boxes', 'mpat_gallery_add_meta_box');
        break;
    case 'page-templates/template-grid.php':
        add_action('add_meta_boxes', 'mpat_grid_add_meta_box');
        break;
    case 'page-templates/template-single-media.php':
        add_action('add_meta_boxes', 'mpat_single_media_add_meta_box');
        break;
    case 'page-templates/template-red-button.php':
        add_action('add_meta_boxes', 'mpat_red_button_add_meta_box');
        break;
}
add_action('add_meta_boxes', 'add_mpat_post_type_meta_boxes');

//Add the boxes depending on the template

//'Full Page'
function fullpage_add_meta_box()
{

    $var1 = get_template_directory_uri() . '/backend/assets/template-fullpage_mockup_Thumbnail.png';

    add_meta_box('mpat_layout_meta_box', 'Layout', 'meta_box_layout_callback', 'page', 'advanced', 'high');
    add_meta_box('mpat_content_meta_box', 'Content Box', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var1, 'box' => 'box1'));

}

//'Two Columns'
function two_columns_add_meta_box()
{

    $var1 = get_template_directory_uri() . '/backend/assets/template-two-columns_mockup_Thumbnail_big.png';
    $var2 = get_template_directory_uri() . '/backend/assets/template-two-columns_mockup_Thumbnail_small.png';

    add_meta_box('mpat_content_meta_box_left', 'Content Box Left', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var1, 'box' => 'box1')
    );

    add_meta_box('mpat_content_meta_box_right', 'Content Box Right', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var2, 'box' => 'box2')
    );
}

//'Three Columns'
function three_columns_add_meta_box()
{

    $var1 = get_template_directory_uri() . '/backend/assets/template-three-columns_mockup_Thumbnail_left.png';
    $var2 = get_template_directory_uri() . '/backend/assets/template-three-columns_mockup_Thumbnail_middle.png';
    $var3 = get_template_directory_uri() . '/backend/assets/template-three-columns_mockup_Thumbnail_right.png';

    add_meta_box('mpat_content_meta_box_left', 'Content Box Left', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var1, 'box' => 'box1')
    );

    add_meta_box('mpat_content_meta_box_middle', 'Content Box Middle', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var2, 'box' => 'box2')
    );

    add_meta_box('mpat_content_meta_box_right', 'Content Box Right', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var3, 'box' => 'box3')
    );
}

//'Gallery'
function mpat_gallery_add_meta_box()
{
    add_meta_box('mpat_gallery_meta_box', 'Gallery Layout', 'gallery_meta_box_callback', 'page', 'advanced', 'high');
}

//'Grid'
function mpat_grid_add_meta_box(){
    add_meta_box('mpat_grid_meta_box', 'Grid Content', 'grid_meta_box_callback', 'page', 'advanced', 'high');
}

//'Red Button'
function mpat_red_button_add_meta_box(){
    add_meta_box('mpat_red_button_meta_box', 'Red Button', 'red_button_meta_box_callback', 'page', 'advanced', 'high');
}

function mpat_single_media_add_meta_box(){

    $var1 = get_template_directory_uri() . '/backend/assets/template-two-columns_mockup_Thumbnail_big.png';
    $var2 = get_template_directory_uri() . '/backend/assets/template-two-columns_mockup_Thumbnail_small.png';

    add_meta_box('mpat_single_media_meta_box_primary', 'Primary Content Box', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var1, 'box' => 'primary')
    );

    add_meta_box('mpat_single_media_meta_box_secondary', 'Secondary Content Box', 'meta_box_contentselection_callback', 'page', 'advanced', 'high',
        array('iconurl' => $var2, 'box' => 'secondary')
    );


    add_meta_box('mpat_single_media_meta_box_advertisement', 'Advertisement', 'meta_box_advertisement_callback', 'page', 'advanced', 'high',
        array('iconurl' => "", 'box' => 'advertisement')
    );

}

//Add the meta boxes for the custom post types
function add_mpat_post_type_meta_boxes()
{
    //'Functions'
    add_meta_box('mpat_function_meta_box', 'Javascript Code', 'mpat_function_meta_box_callback', 'mpat_function', 'advanced', 'high');
    //'Popups'
    add_meta_box('mpat_popup_function_meta_box', 'Button Functions', 'mpat_popup_meta_box_callback', 'mpat_popup', 'advanced', 'high');
    //'Gallery Items'
    add_meta_box('mpat_gallery_item_meta_box', 'Gallery Item Content', 'mpat_gallery_item_meta_box_callback', 'mpat_gallery_item', 'advanced', 'high');
}
?>
