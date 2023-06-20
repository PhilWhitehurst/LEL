<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class LEL_2017 {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $rider    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;


        $this->load_dependencies();
        $this->check_version();
        $this->set_locale();
        $this->define_common_hooks();
        $this->define_chart_hooks();
        // $this->define_ninja_hooks(); # switched off at not needed at present time
        $this->define_nginx_hooks();
        $this->define_rider_hooks();
        $this->define_volunteer_hooks();
        $this->define_woocommerce_hooks();
    }

    /**
     * Checks to see if the version to see if upgrade functions required.
     *
     * If version of plugin has increased then call the update functions
     *
     * @since    1.0.0
     * @access   private
     */
    private function check_version() {
        $prev_version = get_option('LEL2017_plugin_version', 0.1);

        if ($prev_version < $this->version) {
            /*
             * Return reference to update class
             */
            $plugin_update = new LEL_Update($this->get_plugin_name(), $this->get_version(), $prev_version);
        }
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - class-loader. Orchestrates the hooks of the plugin.
     * - class-lang. Defines internationalization functionality.
     * - class-admin. Defines all hooks for the admin area.
     * - class-public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /*
         *  The class responsible for orchestrating the actions and filters of the*
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-loader.php';
        /*
         *  The class responsible for applying any updates on change of plugin version
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-update.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lang.php';
        /**
         * The class responsible for defining all actions that are common.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-common.php';

        /**
         * The class responsible for defining all chart functions.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-charts.php';

        /*
         * The class responsible for defining all actions that occur in the rider area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nginx.php';


        /**
         * The class responsible for defining all actions that occur in Ninja forms.
         */
        //require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ninja.php';
        /**
         * The class responsible for defining all actions that occur in the rider area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-rider.php';

        /**
         * The class responsible for defining all actions that occur in the volunteer area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-volunteer.php';

        /**
         * The class responsible for defining all actions that occur in Woo Commerce.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce.php';



        $this->loader = new LEL2017_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the lel_control_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_lang = new LEL2017_Lang();
        $plugin_lang->set_domain($this->get_plugin_name());

        add_action('plugins_loaded', array($plugin_lang, 'load_plugin_textdomain'));
    }

    /**
     * Register all of the hooks related to the common functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_common_hooks() {
        /*
         * Return reference to common class
         */
        $plugin_common = new LEL_Common($this->get_plugin_name(), $this->get_version());
        /*
         * Shortcodes
         */
        $this->loader->add_shortcode('lel2017_my_details', $plugin_common, 'my_details');

        $this->loader->add_shortcode('lel2017_my_orders', $plugin_common, 'my_orders');
        $this->loader->add_shortcode('lel2017_purchase_merchandise', $plugin_common, 'purchase_merchandise');

        $this->loader->add_shortcode('lel2017_the_controls', $plugin_common, 'the_controls');

        $this->loader->add_shortcode('lel2017_rider_list', $plugin_common, 'rider_list');

        $this->loader->add_shortcode('lel2017_auto_track', $plugin_common, 'auto_track');

        $this->loader->add_shortcode('lel2017_login_form', $plugin_common, 'login_form');

        $this->loader->add_shortcode('lel2017_lostpassword_form', $plugin_common, 'lostpassword_form');

        $this->loader->add_shortcode('lel2017_resetpassword_form', $plugin_common, 'resetpassword_form');

        $this->loader->add_shortcode('lel2017_redirect', $plugin_common, 'redirect');

        $this->loader->add_shortcode('lel2017_online_offline', $plugin_common, 'online_offline');
        $this->loader->add_shortcode('lel2017_offline_count', $plugin_common, 'offline_count');


        $this->loader->add_shortcode('lel2017_hide_header_image', $plugin_common, 'hide_header_image');

        /*
         * Actions
         */

        $this->loader->add_action('after_setup_theme', $plugin_common, 'disable_admin_bar');
        $this->loader->add_action('admin_init', $plugin_common, 'redirect_user');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_common, 'register_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_common, 'register_styles');

        $this->loader->add_action('rest_api_init', $plugin_common, 'rest_api_init');
        $this->loader->add_action('wp_ajax_visa_pdf', $plugin_common, 'visa_pdf');

        $this->loader->add_action('wp_login', $plugin_common, 'join_site', 99, 2);

        $this->loader->add_action('template_redirect', $plugin_common, 'template_redirect');

        $this->loader->add_action('admin_menu', $plugin_common, 'admin_menu');

        $this->loader->add_action('admin_init', $plugin_common, 'register_settings');

        /*
         * Filters
         */
        $this->loader->add_filter('auth_cookie_expiration', $plugin_common, 'cookie_expiration', 99, 3);

        $this->loader->add_filter('retrieve_password_message', $plugin_common, 'retrieve_password_message', 99, 4);

        $this->loader->add_filter('password_change_email', $plugin_common, 'password_change_email', 99, 3);

        $this->loader->add_filter('email_change_email', $plugin_common, 'email_change_email', 99, 3);

        $this->loader->add_filter('nonce_life', $plugin_common, 'nonce_life');

        $this->loader->add_filter('login_url', $plugin_common, 'login_url', 99, 2);

        $this->loader->add_filter('logout_url', $plugin_common, 'logout_url', 99, 2);

        $this->loader->add_filter('lostpassword_url', $plugin_common, 'lostpassword_url', 99, 2);

        $this->loader->add_filter('lostpassword_redirect', $plugin_common, 'lostpassword_redirect', 99, 2);

        $this->loader->add_filter('password_reset_expiration', $plugin_common, 'password_reset_expiration');

        $this->loader->add_filter('rest_url_prefix', $plugin_common, 'rest_url_prefix');
    }

    private function define_ninja_hooks() {
        /*
         * Return reference to Ninja class
         */
        $plugin_ninja = new LEL_Ninja($this->get_plugin_name(), $this->get_version());

        //$this->loader->add_filter('nf_notification_types', $plugin_ninja, 'custom_nf_actions', 10);
    }

    private function define_chart_hooks() {
        /*
         * Return reference to charts class
         */
        $plugin_charts = new LEL_Charts($this->get_plugin_name(), $this->get_version());

        /*
         * Chart short codes
         */

        $this->loader->add_shortcode('lel2017-bubble-chart', $plugin_charts, 'bubble_chart');
        $this->loader->add_shortcode('lel2017-world-map', $plugin_charts, 'world_map');
        $this->loader->add_shortcode('lel2017-bar-chart', $plugin_charts, 'bar_chart');
        $this->loader->add_shortcode('lel2017-js-chart', $plugin_charts, 'js_chart');

        $this->loader->add_shortcode('lel2017-rider', $plugin_charts, 'ridersByCountry');
        /*
         * define actions
         */
        $this->loader->add_action('wp_ajax_ridersByCountry', $plugin_charts, 'ridersByCountry');
        $this->loader->add_action('wp_ajax_nopriv_ridersByCountry', $plugin_charts, 'ridersByCountry');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_charts, 'register_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_charts, 'register_styles');

// Chart rest routes
        $this->loader->add_action('rest_api_init', $plugin_charts, 'rest_api_init');
    }

    private function define_nginx_hooks() {
        /*
         * Return reference to Nginx class
         */
        $plugin_nginx = new LEL_Nginx($this->get_plugin_name(), $this->get_version());
        /*
         * Define actions
         */
        $this->loader->add_action('save_post', $plugin_nginx, 'purge_cache');
        $this->loader->add_action('trash_post', $plugin_nginx, 'purge_cache');
    }

    private function define_rider_hooks() {
        /*
         * Return reference to rider class
         */
        $plugin_rider = new LEL_Rider($this->get_plugin_name(), $this->get_version());


        $this->loader->add_shortcode('lel2017_bag_drops', $plugin_rider, 'bag_drops');
        $this->loader->add_shortcode('lel2017_start_waves', $plugin_rider, 'start_waves');
        $this->loader->add_shortcode('lel2017_admin_start_waves', $plugin_rider, 'admin_start_waves');
        $this->loader->add_shortcode('lel2017_admin_designated_starts', $plugin_rider, 'admin_designated_starts');

        $this->loader->add_shortcode('lel2017_rider_finish_list', $plugin_rider, 'rider_finish_list');



        /*
         * Actions
         */

        $this->loader->add_action('wp_enqueue_scripts', $plugin_rider, 'register_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_rider, 'register_styles');

        $this->loader->add_action('wp_ajax_updateBagDrops', $plugin_rider, 'updateBagDrops');
        $this->loader->add_action('wp_ajax_updateStartTime', $plugin_rider, 'updateStartTime');
        $this->loader->add_action('wp_ajax_addRiderWave', $plugin_rider, 'addRiderWave');
        $this->loader->add_action('wp_ajax_getRiderWaves', $plugin_rider, 'getRiderWaves');
        $this->loader->add_action('wp_ajax_chooseRiderWave', $plugin_rider, 'chooseRiderWave');


        $this->loader->add_action('wp_ajax_updateRiderWave', $plugin_rider, 'updateRiderWave');
        $this->loader->add_action('wp_ajax_deleteRiderWave', $plugin_rider, 'deleteRiderWave');
        $this->loader->add_action('wp_ajax_getDesignatedStart', $plugin_rider, 'getDesignatedStart');
        $this->loader->add_action('wp_ajax_bagDropLocations', $plugin_rider, 'bagDropLocations');
        $this->loader->add_action('wp_ajax_searchSurnameRiders', $plugin_rider, 'searchSurnameRiders');
        $this->loader->add_action('wp_ajax_searchChosenWaveRiders', $plugin_rider, 'searchChosenWaveRiders');

        $this->loader->add_action('wp_ajax_searchDesignatedStartRiders', $plugin_rider, 'searchDesignatedStartRiders');
        $this->loader->add_action('wp_ajax_searchTeamRiders', $plugin_rider, 'searchTeamRiders');

        $this->loader->add_action('rest_api_init', $plugin_rider, 'rest_api_init');


        $this->loader->add_action('template_redirect', $plugin_rider, 'auth_redirect');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_volunteer_hooks() {
        /*
         * Return reference to public class
         */

        $plugin_volunteer = new LEL_Volunteer($this->get_plugin_name(), $this->get_version());

        /*
         * Rider short codes
         */

        $this->loader->add_shortcode('lel2017_set_my_control', $plugin_volunteer, 'set_my_control');
        $this->loader->add_shortcode('lel2017_get_my_control', $plugin_volunteer, 'get_my_control');
        $this->loader->add_shortcode('lel2017_search_rider', $plugin_volunteer, 'search_rider');
        $this->loader->add_shortcode('lel2017_bar_code', $plugin_volunteer, 'bar_code');
        $this->loader->add_shortcode('lel2017_checkin_checkout', $plugin_volunteer, 'checkin_checkout');

        /*
         * Actions
         */
        $this->loader->add_action('rest_api_init', $plugin_volunteer, 'rest_api_init');

        $this->loader->add_action('init', $plugin_volunteer, 'register_custom_posttypes');



        $this->loader->add_action('wp_enqueue_scripts', $plugin_volunteer, 'register_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_volunteer, 'register_styles');
        $this->loader->add_action('template_redirect', $plugin_volunteer, 'auth_redirect');
    }

    private function define_woocommerce_hooks() {
        /*
         * Return reference to woo commerce class
         */

        $plugin_woocommerce = new LEL_WooCommerce($this->get_plugin_name(), $this->get_version());

        /*
         * Purchasable false if already bought (prevent multiple purchases of rider places.
         */

        $this->loader->add_filter('woocommerce_is_purchasable', $plugin_woocommerce, 'is_purchasable', 10, 2);
        /*
         *  Single item in cart, check for products with same sku
         */

        $this->loader->add_filter('woocommerce_add_to_cart_sold_individually_quantity', $plugin_woocommerce, 'is_one_item_in_cart', 10, 5);
        /*
         *  Remove out of stock items from basket
         */
        $this->loader->add_action('woocommerce_check_cart_items', $plugin_woocommerce, 'remove_from_basket');

        /*
         * Gender field
         */
        $this->loader->add_action('woocommerce_after_order_notes', $plugin_woocommerce, 'checkout_fields');
        $this->loader->add_action('woocommerce_checkout_process', $plugin_woocommerce, 'checkout_fields_validate');
        /*
         * Recaptcha field in woocommerce checkout page
         */
        if (get_option('wc_settings_recaptcha_checkout') === 'yes') {
            $this->loader->add_action('woocommerce_after_order_notes', $plugin_woocommerce, 'recaptcha_checkout_field');
            $this->loader->add_action('woocommerce_after_checkout_validation', $plugin_woocommerce, 'recaptcha_checkout_field_validate');
        }
        /*
         * Privacy policy and aged 18 or over checkboxes
         */
        $this->loader->add_action('woocommerce_review_order_before_submit', $plugin_woocommerce, 'checkout_accept_fields');
        $this->loader->add_action('woocommerce_checkout_process', $plugin_woocommerce, 'checkout_accept_fields_validate');
        $this->loader->add_action('woocommerce_checkout_update_user_meta', $plugin_woocommerce, 'checkout_field_update_user_meta', 10, 2);

        /*
         * Remove Company field from checkout
         */
        $this->loader->add_filter('woocommerce_checkout_fields', $plugin_woocommerce, 'checkout_remove_fields');


        /*
         * Rider and Volunteer products should only be visible to
         * riders and volunteers
         */

        $this->loader->add_filter('woocommerce_product_is_visible', $plugin_woocommerce, 'product_is_visible', 10, 2);
        /*
         *  Remove PayPal as option if not permitted for role
         */

        $this->loader->add_filter('woocommerce_available_payment_gateways', $plugin_woocommerce, 'paypal_disable', 10, 2);



        /*
         * Prevent direct access to rider and volunteer products
         */
        $this->loader->add_action('template_redirect', $plugin_woocommerce, 'merchandise_access');



        /*
         * Load front end assets to handle woo commerce custom front end
         */

        $this->loader->add_action('wp_footer', $plugin_woocommerce, 'enqueueAssets');


        /*
         * Display the additional fields in woo product inventory admin tab
         */
        $this->loader->add_action('woocommerce_product_options_inventory_product_data', $plugin_woocommerce, 'woo_add_custom_inventory_fields');

        /*
         * Save the additional fields as meta on woo product save
         */
        $this->loader->add_action('woocommerce_process_product_meta', $plugin_woocommerce, 'woo_add_custom_inventory_fields_save');
        /*
         * Display the additional fields in woo product general admin tab
         */
        $this->loader->add_action('woocommerce_product_options_general_product_data', $plugin_woocommerce, 'woo_add_custom_general_fields');
        /*
         * Save the additional fields as meta on woo product save
         */
        $this->loader->add_action('woocommerce_process_product_meta', $plugin_woocommerce, 'woo_add_custom_general_fields_save');

        /*
         * Add RECAPTCHA tab under woocommerce settings page
         */

        $this->loader->add_filter('woocommerce_settings_tabs_array', $plugin_woocommerce, 'woo_add_recaptcha_section', 50);
        /*
         * Add settings to new recaptcha tab
         */

        $this->loader->add_action('woocommerce_settings_tabs_recaptcha', $plugin_woocommerce, 'woo_recaptcha_add_settings_to_tab');
        /*
         * Save settings from new recaptcha tab
         */

        $this->loader->add_action('woocommerce_update_options_recaptcha', $plugin_woocommerce, 'update_recaptcha_settings');
        /*
         * Woo commerce simply order export add extra fields
         */
        $this->loader->add_filter('wc_settings_tab_order_export', $plugin_woocommerce, 'add_export_tab_fields');
        /*
         * Woo Commerce simply order export add additional columns to export.
         */
        $this->loader->add_filter('wpg_order_columns', $plugin_woocommerce, 'add_export_columns');

        /*
         * Woo commerce simply order export, add extra fields to CSV
         */

        $this->loader->add_action('wpg_add_values_to_csv', $plugin_woocommerce, 'add_values_to_csv', 10, 6);
        /*
         * Add handling fee for Paypal
         */

        $this->loader->add_action('admin_head', $plugin_woocommerce, 'extra_fee_form_fields');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_woocommerce, 'recalc_totals_script');

        $this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_woocommerce, 'Payment_handling_fees');

        /*
         * Set Paypal orders to paid if appropriate
         */
        $this->loader->add_filter('woocommerce_payment_complete_order_status', $plugin_woocommerce, 'autocomplete_paid_virtual_orders', 10, 2);
        /*
         * Woocommerce password strength
         */

        $this->loader->add_action('wp_print_scripts', $plugin_woocommerce, 'remove_password_strength', 100);

        /*
         * Login for some Woo Commerce pages
         */
        $this->loader->add_action('template_redirect', $plugin_woocommerce, 'auth_redirect');
        /*
         * Date of birth field for insurance products
         */
        $this->loader->add_action('woocommerce_before_add_to_cart_button', $plugin_woocommerce, 'add_date_of_birth_field');
        $this->loader->add_filter('woocommerce_add_to_cart_validation', $plugin_woocommerce, 'date_of_birth_validation', 10, 3);
        $this->loader->add_action('woocommerce_add_cart_item_data', $plugin_woocommerce, 'save_date_of_birth_field', 10, 2);
        $this->loader->add_filter('woocommerce_get_item_data', $plugin_woocommerce, 'render_meta_on_cart_and_checkout', 10, 2);
        $this->loader->add_action('woocommerce_add_order_item_meta', $plugin_woocommerce, 'date_of_birth_order_meta_handler', 10, 3);


        /*
         *  Custom Fees
         */
        // Extra Tab

        $this->loader->add_action('woocommerce_product_data_tabs', $plugin_woocommerce, 'add_fees_product_tab', 99, 1);

        // Display Fields
        $this->loader->add_action('woocommerce_product_data_panels', $plugin_woocommerce, 'add_custom_fees');
        // Save Fields
        $this->loader->add_action('woocommerce_process_product_meta', $plugin_woocommerce, 'add_custom_fees_save');
        // add into cart and order meta
        $this->loader->add_action('woocommerce_add_cart_item_data', $plugin_woocommerce, 'save_date_of_birth_field', 10, 2);

// Add fees
        $this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_woocommerce, 'add_custom_cart_fee');
        /*
         * lifetime group
         */
        $this->loader->add_action('woocommerce_add_cart_item_data', $plugin_woocommerce, 'save_lifetime_group', 10, 2);
        $this->loader->add_action('woocommerce_add_order_item_meta', $plugin_woocommerce, 'lifetime_group_order_meta_handler', 10, 3);

// Rest API definitions
        $this->loader->add_action('rest_api_init', $plugin_woocommerce, 'rest_api_init');
    }

    /*

      /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */

    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    LEL2017_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
