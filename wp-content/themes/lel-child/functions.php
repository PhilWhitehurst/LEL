<?php

/*
 * Author: Phil Whitehurst
 * Description:  Required additional functions for LEL2017 child theme
 * 
 */

/**
 * Load child theme stylesheet
 * Set the $deps to 'nudie' to load after the main stylesheet
 */
function child_theme_styles() {

    wp_enqueue_style('child-theme-style', get_stylesheet_uri(), array('nudie'), '3.30', 'all');
    wp_enqueue_style('child-theme-print', get_stylesheet_directory_uri() . '/print.css', array('child-theme-style'), '3.22', 'print');
}

add_action('wp_enqueue_scripts', 'child_theme_styles');

/*
 * Loads the child theme's translated strings.
 */
add_action('after_setup_theme', 'lel2017_child_theme_setup');

function lel2017_child_theme_setup() {
    load_child_theme_textdomain('LEL2017', get_stylesheet_directory() . '/languages');
}

/*
 * Override site title in head section of web page
 */

add_filter('wp_title', 'lel2017_custom_title');

function lel2017_custom_title() {
    // Return my custom title

    $siteTitle = __('London Edinburgh London 2017', 'LEL2017');
    $pageTitle = get_the_title();



    return $siteTitle . ' - ' . $pageTitle;
}

/**
 * Register sidebar for language switcher
 *
 */
function lel2017_widgets_init() {


// Multilingual Press language switcher

    if (function_exists('register_sidebar')) {
        register_sidebar(array(
            'name' => 'Language Switcher',
            'id' => 'language-switcher',
            'before_widget' => '<div id="lang-switcher" >',
            'after_widget' => '</div>'
        ));
    }
}

;
add_action('widgets_init', 'lel2017_widgets_init');
/*
 * Decide on whether to show language switcher based on page
 */

function lel2017_show_language_switcher() {

    if (true) {
        if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Language Switcher')) {
            
        }
    }
}

/*
 * Change [...] to a ...more
 */

add_filter('excerpt_more', 'lel2017_excerpt_more');

function lel2017_excerpt_more($more) {
    $return = '<a href="' . get_permalink() . '">' . __('...more', 'LEL2017') . '</a>';
    return $return;
}

/*
 * Set except length to 20 words
 */
add_filter('excerpt_length', 'lel2017_excerpt_length', 999);

function lel2017_excerpt_length($length) {
    return 30;
}

// stop WordPress removing div tags
function lel2017_tinymce_fix($init) {
    // html elements being stripped
    $init['extended_valid_elements'] = 'div[*]';

    // pass back to wordpress
    return $init;
}

add_filter('tiny_mce_before_init', 'lel2017_tinymce_fix');

add_action('wp_enqueue_scripts', 'lel2017_register_scripts');

function lel2017_register_scripts() {

    wp_register_script('blueimp-gallery-script', get_stylesheet_directory_uri() . '/js/blueimp-gallery.js', array(), '1.0', true);
    wp_register_script('bootstrap-image-gallery-script', get_stylesheet_directory_uri() . '/js/bootstrap-image-gallery.js', array('blueimp-gallery-script'), '1.0', true);
    wp_register_script('flight', get_stylesheet_directory_uri() . '/js/flight.min.js', array('jquery'), true);
    wp_register_script('require', get_stylesheet_directory_uri() . '/js/require.js', null, true);
}

add_action('wp_enqueue_scripts', 'lel2017_register_styles');

function lel2017_register_styles() {
    wp_register_style('blueimp-gallery-css', "//blueimp.github.io/Gallery/css/blueimp-gallery.min.css", '1.0');
    wp_register_style('bootstrap-image-css', get_stylesheet_directory_uri() . '/css/bootstrap-image-gallery.min.css', array('bootstrap', 'blueimp-gallery-css'), '1.0');
}

/*
 * Output the page slug (the last bit of the URL after the /)
 */

function the_slug($id = null, $echo = false) {
    $slug = basename(get_permalink($id));
    do_action('before_slug', $slug);
    $slug = apply_filters('slug_filter', $slug);
    if ($echo)
        echo $slug;
    do_action('after_slug', $slug);
    return $slug;
}

/*
 * Return header image based on slug of page, use a default if image not
 * found
 */

function lel2017_header_image($echo = true) {
    $base_url = get_stylesheet_directory_uri() . '/img/header/';
    $base_dir = get_stylesheet_directory() . '/img/header/';
    $img_prefix = 'LEL-2017_';
    $img_default = 'default';
    $img_suffix = '-head.jpg';
    /*
     * Generate default header image url
     */
    $header_default_img_url = $base_url . $img_prefix . $img_default . $img_suffix;
    /*
     * Generate page specific header image url as well as file path
     */
    $slug = the_slug();
    if (is_single()) {
        $slug = 'news';
    }
    $header_page_img_url = $base_url . $img_prefix . $slug . $img_suffix;
    $header_page_img_path = $base_dir . $img_prefix . $slug . $img_suffix;

    $return_header_img = ( file_exists($header_page_img_path) ? $header_page_img_url : $header_default_img_url );
    /*
     * output image uri
     */
    if ($echo) {
        echo $return_header_img;
    }
    return $return_header_img;
}

/*
 * Process shortcodes in the text widget
 */

add_filter('widget_text', 'do_shortcode');

/*
 * Hide some admin menu's items from none admins
 */

function lel2017_remove_menus() {

    if (!current_user_can('activate_plugins')) {
        remove_menu_page('edit-comments.php');          //Comments
        remove_menu_page('tools.php');                 //Tools
        remove_menu_page('WP-GPX-Maps');
        remove_submenu_page('options-general.php', 'WP-GPX-Maps');  //WP-GPX
    }
}

add_action('admin_menu', 'lel2017_remove_menus');

/*
 * Hide admin notices
 */

function lel2017_hide_update_notices() {
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }
    //Remove WooCommerce's annoying update message
    remove_action('admin_notices', 'woothemes_updater_notice');
}

add_action('admin_head', 'lel2017_hide_update_notices', 1);

function lel2017_remove_dashboard_meta() {
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');

    remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');

    remove_meta_box('multilingualpress-dashboard-widget', 'dashboard', 'normal');
}

add_action('admin_init', 'lel2017_remove_dashboard_meta');

/*
 * Add a short url as meta data to a post whenever it is saved
 */
add_action('new_post', 'lel2017_update_short_url');

function lel2017_update_short_url($post_id) {
    if (wp_is_post_revision($post_id)) {
        return;
    }
    $apiKey = 'AIzaSyAVWsK7NBS4D_SrmiFgfsK2JJzPYQ6QfVE';
    $Url = get_permalink($post_id);
    $googl = new Googl($apiKey);
    // Shorten URL
    $sUrl = $googl->shorten($Url);

    update_post_meta($post_id, 'lel2017_short_url', $sUrl);
}

function lel2017_get_short_url($post_id) {
    $output = get_post_meta($post_id, 'lel2017_short_url', true);
    return $output;
}

// convert all links from http:// or https:// to //
// to avoid mixed content warnings on a https connection
add_filter('post_link', 'lel2017_resolve_mixed_content', 99);
add_filter('wp_calculate_image_srcset', 'lel2017_resolve_srcset', 99);
add_filter('script_loader_src', 'lel2017_resolve_mixed_content', 99);
add_filter('style_loader_src', 'lel2017_resolve_mixed_content', 99);
add_filter('admin_url', 'lel2017_resolve_mixed_content', 99);

function lel2017_resolve_mixed_content($src) {
    $result = preg_replace('/^https??:(.+)/', '\1', $src);
    return $result;
}

function lel2017_resolve_srcset($sources) {
    $newSource = [];
    foreach ($sources as $source) {
        $source['url'] = str_replace('http:', '', $source['url']);
        array_push($newSource, $source);
    }
    return $newSource;
}

add_filter('mime_types', 'lel2017_upload_mimes');

function lel2017_upload_mimes($existing_mimes) {
    $existing_mimes['gpx'] = 'application/xml';

    return $existing_mimes;
}

/**
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 * */
add_action('wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99);

function child_manage_woocommerce_styles() {
    //remove generator meta tag
    remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));

    //first check that woo exists to prevent fatal errors
    if (function_exists('is_woocommerce')) {
        //dequeue scripts and styles
        if (!is_woocommerce() && !is_cart() && !is_checkout()) {
            wp_dequeue_style('woocommerce_frontend_styles');
            wp_dequeue_style('woocommerce_fancybox_styles');
            wp_dequeue_style('woocommerce_chosen_styles');
            wp_dequeue_style('woocommerce_prettyPhoto_css');
            wp_dequeue_script('wc_price_slider');
            wp_dequeue_script('wc-single-product');
            wp_dequeue_script('wc-add-to-cart');
            wp_dequeue_script('wc-cart-fragments');
            wp_dequeue_script('wc-checkout');
            wp_dequeue_script('wc-add-to-cart-variation');
            wp_dequeue_script('wc-single-product');
            wp_dequeue_script('wc-cart');
            wp_dequeue_script('wc-chosen');
            wp_dequeue_script('woocommerce');
            wp_dequeue_script('prettyPhoto');
            wp_dequeue_script('prettyPhoto-init');
            wp_dequeue_script('jquery-blockui');
            wp_dequeue_script('jquery-placeholder');
            wp_dequeue_script('fancybox');
        }
    }
}

include( get_stylesheet_directory() . '/includes/goo.gl.php');
include( get_stylesheet_directory() . '/includes/shortcodes.php');
include( get_stylesheet_directory() . '/includes/ninja-forms.php');
include( get_stylesheet_directory() . '/includes/woocommerce.php');
