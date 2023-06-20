<?php

/**
 * The common functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_Common {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->cache = 10; // Default number of seconds to cache results
    }

    /**
     * Disable admin bar on the frontend of the website
     * for riders and volunteers (who cannot post news)
     */
    public function disable_admin_bar() {
        if (!current_user_can('edit_posts'))
            add_filter('show_admin_bar', '__return_false');
    }

    /**
     * Redirect based on user and do not allow access to
     * WP admin for riders and volunteers (who cannot post news)
     */
    public function redirect_user() {
        if (!current_user_can('edit_posts')) {
            if (defined('DOING_AJAX') | DOING_AJAX) {
                return;
            }
            if (current_user_can('access_tracking_area')) {
                wp_safe_redirect(site_url('volunteer-arrival-departure/'));
                exit;
            }
            if (current_user_can('access_registration_area')) {
                wp_safe_redirect(site_url('volunteer-registration/'));
                exit;
            }

            if (current_user_can('access_volunteer_area')) {
                wp_safe_redirect(site_url('volunteer-my-details/'));
                exit;
            }
            if (current_user_can('access_rider_area')) {
                wp_safe_redirect(site_url('rider-my-details/'));
                exit;
            }
            wp_safe_redirect(site_url());
            exit;
        }
    }

    public function register_scripts() {


        wp_register_script('flight-ajax', plugins_url('../js/flight-components/AjaxComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('flight-message', plugins_url('../js/flight-components/MessageComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('flight-info', plugins_url('../js/flight-components/InfoComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('flight-extra', plugins_url('../js/flight-components/ExtraComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('flight-hideshow', plugins_url('../js//flight-components/HideShowComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('rest-component', plugins_url('../js/flight-components/RestComponent.js', __FILE__), array('jquery', 'flight', 'local-forage'), '4.6', true);

        wp_register_script('rider-list-component', plugins_url('../js/flight-components/RiderListComponent.js', __FILE__), array('jquery', 'flight'), '2.9.1', true);
        wp_register_script('toggle-component', plugins_url('../js/flight-components/ToggleComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('button-component', plugins_url('../js/flight-components/ButtonComponent.js', __FILE__), array('jquery', 'flight'), '', true);
        wp_register_script('scan-component', plugins_url('../js/flight-components/AutoScanComponent.js', __FILE__), array('jquery', 'flight'), '1.6', true);

        wp_register_script('autotrack-component', plugins_url('../js/flight-components/AutoTrackComponent.js', __FILE__), array('jquery', 'flight'), '1.5', true);
        wp_register_script('redirect-component', plugins_url('../js/flight-components/RedirectComponent.js', __FILE__), array('jquery', 'flight'), '1.1', true);
        wp_register_script('online-offline-component', plugins_url('../js/flight-components/OnlineOfflineComponent.js', __FILE__), array('jquery', 'flight'), '1.1', true);
        wp_register_script('offline-count-component', plugins_url('../js/flight-components/OfflineCountComponent.js', __FILE__), array('jquery', 'flight'), '1.2', true);


        wp_register_script('hide-header-image', plugins_url('../js/hide-header-image.js', __FILE__), array('jquery'), '0.1', true);


        wp_register_script('lel-my-details', plugins_url('../js/my-details.js', __FILE__), array('jquery', 'flight-message', 'rest-component'), '', true);


        wp_register_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDyH1xigIDnq6Q1sqIhxHKVey98iRHiUOw', array(), '3', true);
        wp_register_script('acf-google-map', plugins_url('../js/google-map.js', __FILE__), array('google-map', 'jquery'), '', true);

        wp_register_script('datatable', 'https://cdn.datatables.net/v/bs/dt-1.10.12/datatables.min.js', array('bootstrap'), '3', true);

        wp_register_script('lel-scroll', plugins_url('../js/scroll.js', __FILE__), array('jquery', 'datatable'), '0.7', true);

        wp_register_script('public-tracking-list', plugins_url('../js/public-tracking-list.js', __FILE__), array('rider-list-component', 'flight-message', 'volunteer-templates'), '1.2', true);
        wp_register_script('auto-track', plugins_url('../js/auto-track.js', __FILE__), array('autotrack-component'), '1.1', true);
        wp_register_script('redirect', plugins_url('../js/redirect.js', __FILE__), array('redirect-component'), '0.3', true);
        wp_register_script('online-offline', plugins_url('../js/online-offline.js', __FILE__), array('online-offline-component'), '0.1', true);
        wp_register_script('offline-count', plugins_url('../js/offline-count.js', __FILE__), array('offline-count-component'), '0.1', true);

        wp_register_script('local-forage', plugins_url('../js/localforage.min.js', __FILE__), '', '0.1', true);
    }

    public function register_styles() {
        wp_register_style('acf-google-map', plugins_url('../css/acf-google-map.css', __FILE__));
        wp_register_style('datatables', 'https://cdn.datatables.net/v/bs/dt-1.10.12/datatables.css');
    }

// Rest route definitions here

    public function rest_api_init() {
// My details updates 
        register_rest_route('lel/v1', '/mydetails', [
            'methods' => 'POST',
            'callback' => [$this, 'updateMyDetails'],
            'permission_callback' => function () {
        return current_user_can('update_my_details');
    }
                ]
        );

// visa letter
        register_rest_route('lel/v1', '/visa', [
            'methods' => 'GET',
            'callback' => [$this, 'visa_pdf']
                ]
        );
    }

// Rest functions here

    public function updateMyDetails($request) {

// userid will be 0 is not signed in


        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return [
                'status' => 'error',
                'message' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ];
        }



        $params = $request->get_params();

// Deal with email and password updates


        $email1 = $params['Email1'];
        $email2 = $params['Email2'];
        $password1 = $params['Password1'];
        $password2 = $params['Password2'];

        $user = [
            'ID' => $user_id
        ];
        if (!empty($password1) or ! empty($password2)) {

            if ($password1 === $password2) {
                $user['user_pass'] = $password2;
            } else {
                return [
                    'status' => 'error',
                    'msg' => __('Both passwords must match', 'LEL2017Plugin')
                ];
            }
        }


        if (!empty($email2)) {

            if ($email1 === $email2) {
                $user['user_email'] = $email2;
            } else {
                return [
                    'status' => 'error',
                    'msg' => __('Both email addresses must match', 'LEL2017Plugin')
                ];
            }
        }
        if (wp_update_user($user) !== $user_id) {
            return [
                'status' => 'error',
                'msg' => __('Email in use by another user', 'LEL2017Plugin')
            ];
        }


// Update user data

        $userdata = [
            ID => $user_id,
            billing_address_1 => $params['Line1'],
            billing_address_2 => $params['Line2'],
            billing_city => $params['City'],
            billing_state => $params['County'],
            billing_country => $params['Country'],
            billing_postcode => $params['PostCode'],
            billing_phone => $params['Phone'],
            emergency_contact => $params['EmergencyContact'],
            emergency_phone => $params['EmergencyPhone'],
        ];

        if (isset($user['user_email'])) {
            $userdata['billing_email'] = $user['user_email'];
        }


        if (current_user_can('access_rider_area') | current_user_can('access_volunteer_area')) {
// Food
            $userdata['vegetarian'] = $params['Vegetarian'];
            $userdata['vegan'] = $params['Vegan'];
            $userdata['lactose'] = $params['Lactose'];
            $userdata['gluten'] = $params['Gluten'];
            $userdata['nut'] = $params['Nut'];
            if (current_user_can('access_rider_area')) {
// Privacy
                $userdata['show_checkin'] = $params['Show_checkin'] ?? 'no';
                $userdata['show_checkout'] = $params['Show_checkout'] ?? 'no';
                $userdata['show_bedbook'] = $params['Show_bedbook'] ?? 'no';
// Team
                if (get_option('lock_start_times') !== 'yes') {
                    $userdata['team_name'] = $params['TeamName'];
                }
            }
        }


        if (current_user_can('access_volunteer_area')) {
            $userdata['tshirt_size'] = $params['tShirtSize'];
        }

        foreach ($userdata as $key => $value) {
            $value = sanitize_text_field($value);

            update_user_meta($user_id, $key, $value);
        }



        return [
            'status' => 'success',
            'msg' => __('Your details have been updated', 'LEL2017Plugin')
        ];
    }

// Shortcodes here


    /*
     * Add login form to a page
     */
    public function login_form() {


        $redirect_to = '';
        $redirect = esc_url($_GET['redirect_to']);
        if ($redirect) {
            $redirect_to = '<input type="hidden" name="redirect_to" value="'
                    . $redirect
                    . '">';
        }




        if (isset($_GET['loggedout']) && !$error_message) {

            $error_msg = '<div class="alert alert-success">'
                    . __('You have successfully logged out', 'LEL2017Plugin')
                    . '</div>';
        }

        if (isset($_GET['resetpass']) && !$error_message) {
            $error_msg = '<div class="alert alert-success">'
                    . __('Password successfully reset', 'LEL2017Plugin')
                    . '</div>';
        }


// Either first time in or authentication failed, so show
// login form with any errors.

        $form = '<form name="login" id="login" action="' . wp_login_url() . '" method = "POST">';

        $form .= '<div class="form-group">'
                . '<label for="user_login">' . __('Username or Email Address', 'LEL2017Plugin') . '</label>'
                . '<input type = "text" name="user" id="user_login" class="form-control" >'
                . '</div>'
                . '<div class="form-group">'
                . '<label for="user_pass">' . __('Password', 'LEL2017Plugin') . '</label>'
                . '<input type="password" name="pwd" id = "user_pass" class="form-control">'
                . '</div>'
                . '<div class="checkbox">'
                . '<label><input name="rememberme" type ="checkbox" id="rememberme" value="yes">' . __('Remember Me', 'LEL2017Plugin') . '</label>'
                . '</div>'
                . '<input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="' . __('Login', 'LEL2017Plugin') . '">'
                . wp_nonce_field('login', '_verify', false, false)
                . $redirect_to
                . '</form>';
        if ($error_msg) {
            $form .= '<p>&nbsp;</p>'
                    . $error_msg;
        }

        $lostpassword_link = '<p>&nbsp;</p><p><a href="' . wp_lostpassword_url() . '">' . __('Lost Password', 'LEL2017Plugin') . '</a></p>';

        $output = $form . $lostpassword_link;
        return $output;
    }

    /*
     * Add login form to a page
     */

    public function lostpassword_form() {


// Check to see if form submitted
        if (isset($_POST['_verify']) && isset($_POST['user_login'])) {
            $wp_nonce = $_POST['_verify'];

// If nonce valid check user
            if (wp_verify_nonce($wp_nonce, 'lostpassword')) {
                $result = $this->retrieve_password();
                if (is_wp_error($result)) {
                    $error_msg = '<div class="alert alert-danger">'
                            . $result->get_error_message()
                            . '</div>';
                } else {
                    $error_msg = '<div class="alert alert-success">'
                            . __('Password reset email successfully sent', 'LEL2017Plugin')
                            . '</div>';
                }
            }
        }



        if (isset($_GET['invalidkey']) && !$error_msg) {
            $error_msg = '<div class="alert alert-danger">'
                    . 'Password reset key is invalid'
                    . '</div>';
        }

        if (isset($_GET['expiredkey']) && !$error_msg) {
            $error_msg = '<div class="alert alert-danger">'
                    . 'Password reset key has expired'
                    . '</div>';
        }


// Either first time in or authentication failed, so show
// login form with any errors.

        $form = '<form id="lost-password" action="' . wp_lostpassword_url() . '" method="POST">';
        $form .= '<div class="form-group">'
                . '<label for="user_login">' . __('Username or Email Address', 'LEL2017Plugin') . '</label>'
                . '<input type="text" name="user_login" class="form-control">'
                . '</div>'
                . '<input type="submit" class="btn btn-primary" value="' . __('Lost Password', 'LEL2017Plugin') . '">'
                . wp_nonce_field('lostpassword', '_verify', false, false)
                . '</form>';
        if ($error_msg) {
            $form .= '<p>&nbsp;</p>'
                    . $error_msg;
        }

        $login_link = '<p>&nbsp;</p><p><a href="' . wp_login_url() . '">' . __('Login', 'LEL2017Plugin') . '</a></p>';

        $output = $form . $login_link;
        return $output;
    }

    public function resetpassword_form() {
        $rp_cookie = 'wp-resetpass-' . COOKIEHASH;

        if (isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie], ':')) {

            list( $rp_login, $rp_key ) = explode(':', wp_unslash($_COOKIE[$rp_cookie]), 2);
            $user = check_password_reset_key($rp_key, $rp_login);


            if (isset($_POST['pass1']) && !hash_equals($rp_key, $_POST['rp_key'])) {
                $user = false;
            }
        } else {
            $user = false;
        }


        $errors = new WP_Error();

        if (isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'])
            $errors->add('password_reset_mismatch', __('The passwords do not match.', 'LEL2017Plugin'));

        do_action('validate_password_reset', $errors, $user);

        if ($errors->get_error_code()) {
            $msg = $errors->get_error_message();
            $error_msg = '<div class="alert alert-danger">'
                    . $msg
                    . '</div>';
        }

// Generate a new password

        $new_password = esc_attr(wp_generate_password(16));

// Reset password form
        $form = '<form name="resetpassform" id="resetpassform" action="/reset-password/" method="POST">'
        . '<input type="hidden" id="user_login" value="' . esc_attr($rp_login) . '">'
        . '<div class="alert alert-info">'
        . __('Please enter your new password below', 'LEL2017Plugin')
        . '</div>'
        . '<div class="form-group">'
        . '<label for="pass1">' . __('New Password', 'LEL2017Plugin') . '</label>'
        . '<input type="text" value="' . ( $_POST['pass1'] ?? $new_password ) . '" name="pass1" class="form-control">'
        . '</div>'
        . '<div class="form-group">'
        . '<label for="pass2">' . __('Confirm Password', 'LEL2017Plugin') . '</label>'
        . '<input type="text" value="' . ( $_POST['pass2'] ?? $new_password ) . '" name="pass2" class="form-control">'
                . '</div>'
                . '<input type="hidden" name="rp_key" value="' . esc_attr($rp_key) . '">'
                . '<input type="submit" name="submit" class="btn btn-primary" value="' . __('Reset Password', 'LEL2017Plugin') . '">'
                . '</form>';

        if ($error_msg) {
            $form .= '<p>&nbsp;</p>'
                    . $error_msg;
        }

        $lostpassword_link = '<p>&nbsp;</p><p><a href="' . wp_lostpassword_url() . '">' . __('Lost Password', 'LEL2017Plugin') . '</a></p>';

        $output = $form . $lostpassword_link;
        return $output;
    }

    public function auto_track($atts) {

        $args = shortcode_atts(array(
            'rest_method' => 'GET',
            'rest_path' => 'public/chart/trackingdistance/'
                )
                , $atts);


        $rest_method = $args['rest_method'];
        $rest_path = $args['rest_path'];

        $X_WP_Nonce = wp_create_nonce('wp_rest');
        wp_localize_script('auto-track', 'wp_var', array(
            'resturl' => get_rest_url() . 'lel/v1/',
            'method' => 'GET',
            'rest_method' => $rest_method,
            'rest_path' => $rest_path,
            'X_WP_Nonce' => $X_WP_Nonce,
            'security_token' => __('Security token expired. Please refresh the page.', 'LEL2017Plugin'),
            'tracking_rate' => __('Please slow down rate of tracking requests or you will be banned for a period', 'LEL2017Plugin')
        ));

        wp_enqueue_script('auto-track');

        return '<div id="auto-track"></div>';
    }

    public function online_offline() {

        $slug = the_slug();
// Gallery menu
        $trackingRegistrationPages = [

            'volunteer-arrival-departure',
            'volunteer-sleep',
            'volunteer-dnf',
            'volunteer-bag-drop',
            'volunteer-rider-dashboard',
            'volunteer-control-dashboard',
            'volunteer-event-dashboard',
            'volunteer-registration'
        ];
        if (in_array($slug, $trackingRegistrationPages)) {

            wp_enqueue_script('online-offline');

            return '<div class="col-md-11"><div id="online-offline" class="alert"></div></div>';
        }
    }

    public function offline_count() {

        $slug = the_slug();
// Gallery menu
        $trackingRegistrationPages = [

            'volunteer-arrival-departure',
            'volunteer-sleep',
            'volunteer-dnf',
            'volunteer-bag-drop',
            'volunteer-rider-dashboard',
            'volunteer-control-dashboard',
            'volunteer-event-dashboard',
            'volunteer-registration'
        ];
        if (in_array($slug, $trackingRegistrationPages)) {

            wp_enqueue_script('offline-count');

            return '<div class="col-md-11"><div id="offline-count"></div></div>';
        }
    }

    public function hide_header_image() {
        wp_enqueue_script('hide-header-image');
        return ' ';
    }

    public function my_orders() {

        switch_to_blog(1);
        $filters = array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => get_current_user_id(),
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses())
        );

        $loop = new WP_Query($filters);

        if ($loop->have_posts()) {
            $output = '<table class="table table-striped">';
            $output .= '<thead>';
            $output .= '<tr>';
            $output .= '<th>' . __('Item', 'LEL2017Plugin') . '</th>';
            $output .= '<th>' . __('Price', 'LEL2017Plugin') . '</th>';
            $output .= '<th>' . __('Order Number', 'LEL2017Plugin') . '</th>';
            $output .= '<th>' . __('Order Date', 'LEL2017Plugin') . '</th>';
            $output .= '<th>' . __('Order Status', 'LEL2017Plugin') . '</th>';
            $output .= '</thead>';
            $output .= '<tbody>';
        }

        while ($loop->have_posts()) {
            $loop->the_post();
            $order_id = $loop->post->ID;
            $order_date = $loop->post->post_date;
            switch ($loop->post->post_status) {
                case 'wc-on-hold':
                    $order_status = __('Awaiting Payment', 'LEL2017Plugin');
                    break;
                case 'wc-processing':
                    $order_status = __('Processing', 'LEL2017Plugin');
                    break;
                case 'wc-pending':
                    $order_status = __('Pending Payment', 'LEL2017Plugin');
                    break;
                case 'wc-completed':
                    $order_status = __('Paid', 'LEL2017Plugin');
                    break;
                case 'wc-cancelled':
                    $order_status = __('Cancelled', 'LEL2017Plugin');
                    break;
                case 'wc-refunded':
                    $order_status = __('Refunded', 'LEL2017Plugin');
                    break;

                default:
                    $order_status = __('Unknown', 'LEL2017Plugin');
            }


            $order = new WC_Order($order_id);

            foreach ($order->get_items() as $key => $lineItem) {

                if (strpos(strtolower($lineItem['name']), 'entry') === false) {

                    $mens_or_womens = $lineItem['mens-or-womens'] ?? '';
                    $size = $lineItem['size'] ?? '';
                    $output .= '<tr>';
                    $output .= '<td>' . $lineItem['name'] . ' ' . $mens_or_womens . ' ' . $size . ' ( x' . $lineItem['qty'] . ') </td>';
                    $output .= '<td> £' . $lineItem['line_total'] . '</td>';
                    $output .= '<td>' . $order_id . '</td>';
                    $output .= '<td>' . $order_date . '</td>';
                    $output .= '<td>' . $order_status . '</td>';
                    $output .= '</tr>';
                }
            }
        }

        if ($loop->have_posts()) {
            $output .= '</tbody>';
            $output .= '</table>';
        }

        wp_reset_postdata();
        restore_current_blog();
        return $output;
    }

// Output to links for rider and volunteer merchandise pages
    public function purchase_merchandise() {

        $output = '';
        switch_to_blog(1);
        if (current_user_can('access_rider_area')) {
            $output .= '<a href="' . home_url('shop/rider/') . '" target="_blank">' . __('Rider Merchandise', 'LEL2017Plugin') . ' </a><br>';
        }
        if (current_user_can('access_volunteer_area')) {
            $output .= '<a href="' . home_url('shop/volunteer/') . '" target="_blank">' . __('Volunteer Merchandise', 'LEL2017Plugin') . '</a><br>';
        }
        restore_current_blog();
        return $output;
    }

// The control list

    public function the_controls() {

// enqueue scripts to be loaded in browser
        wp_enqueue_script('acf-google-map');
        wp_enqueue_script('lel-scroll');
        wp_enqueue_style('acf-google-map');
        wp_enqueue_style('datatables');

        switch_to_blog(1);
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'post_type' => 'lel_control',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts = get_posts($args);
        $controls = [];
        $output = '';
        $table = '';

        if (count($posts) > 0) {

            foreach ($posts as $post) {
// Merge all the fields for a control into a single array
                $post = (array) $post;
                $post_id = $post['ID'];
                $custom_fields = get_fields($post_id);

                $control = array_merge($post, $custom_fields);

                $control['distance'] = ($control['distance_northbound'] !== false ? $control['distance_northbound'] : $control['distance_southbound']);
                $control['mandatory'] = ($control['mandatory'][0] === 'Yes' ? __('Yes', 'LEL2017Plugin') : '');
                $control['bag_drops'] = ($control['bag_drops'][0] === 'Yes' ? __('Yes', 'LEL2017Plugin') : '');



                $controls[] = $control;
            }
// sort controls by distance along route
            usort($controls, array($this, 'sort_by_sequence'));

// Setup summary table headers
            $table .= '<div class="col-md-9 col-sm-10 table-responsive" id="summary">'
                    . '<table class="table table-striped myDataTable" >'
                    . '<thead>'
                    . '<tr>'
                    . '<th>' . __('Control', 'LEL2017Plugin') . '</th>'
                    . '<th>' . __('Northbound', 'LEL2017Plugin') . ' (km)</th>'
                    . '<th>' . __('Southbound', 'LEL2017Plugin') . '  (km)</th>'
                    . '<th>' . __('Mandatory', 'LEL2017Plugin') . '</th>'
                    . '<th>' . __('Bag Drops', 'LEL2017Plugin') . '</th>'
                    . '<th>' . __('Bed Capacity', 'LEL2017Plugin') . '</th>'
                    . '</tr>'
                    . '</thead>'
                    . '<tbody>';




            foreach ($controls as $control) {

                $output .= '<div id="' . $control['ID'] . '" class="clearfix">';
                $output .= '<h3>' . $control['post_title'] . '</h3>';
                $north_bound = (isset($control['distance_northbound']) ? $control['distance_northbound'] : '') . ' km';
                if ($north_bound !== ' km') {
                    $output .= '<strong>' . __('Northbound', 'LEL2017Plugin') . ': </strong>' . $north_bound . '<br>';
                }

                $south_bound = (isset($control['distance_southbound']) ? $control['distance_southbound'] : '') . ' km';
                if ($south_bound !== ' km') {
                    $output .= '<strong>' . __('Southbound', 'LEL2017Plugin') . ': </strong>' . $south_bound . '<br>';
                }
                $output .= '<strong>' . __('Mandatory', 'LEL2017Plugin') . ': </strong>' . ($control['mandatory'] === '' ? __('No', 'LEL2017Plugin') : __('Yes', 'LEL2017Plugin')) . '<br>';
                $output .= '<strong>' . __('Bag Drops', 'LEL2017Plugin') . ': </strong>' . ($control['bag_drops'] === '' ? __('No', 'LEL2017Plugin') : __('Yes', 'LEL2017Plugin')) . '<br>';
                $output .= '<strong>' . __('Bed Capacity', 'LEL2017Plugin') . ': </strong>' . $control['bed_capacity'] . '<br>';

                if (!empty($control['post_content'])) {
                    $output .= '<br>';
                    $output .= apply_filters('the_content', $control['post_content']);
                }


                $output .= '<h4>Address</h4>'
                        . $control['address']
                        . '<br>';

                $location = $control['location'];

                if (!empty($location)) {

                    $output .= '<span class="clearfix"></span><div class="col-md-7 col-sm-10"><div class="acf-map">'
                            . '<div class="marker" data-lat="' . $location['lat'] . '" data-lng="' . $location['lng'] . '"></div>'
                            . '</div></div></div>';
                }

                $output .= '<span scroll="summary">Return to summary</span>';

// now add summary table row
                $table .= '<tr>'
                        . '<td scroll="' . $control['ID'] . '">' . $control['post_title'] . '</td>'
                        . '<td>' . $control['distance_northbound'] . '</td>'
                        . '<td>' . $control['distance_southbound'] . '</td>'
                        . '<td>' . $control['mandatory'] . '</td>'
                        . '<td>' . $control['bag_drops'] . '</td>'
                        . '<td>' . $control['bed_capacity'] . '</td>'
                        . '</tr>';
            }
        }
        wp_reset_postdata();
        restore_current_blog();

        $table .= '</tbody></table></div><span class="clearfix"></span>';

        return $table . $output;
    }

    /*
     * Add Redirect
     */

    public function redirect($atts) {

        $args = shortcode_atts(array(
            'url' => '/volunteer-arrival-departure/',
                )
                , $atts);


        $url = $args['url'];

        wp_localize_script('redirect', 'wp_var', array(
            'url' => $url
        ));

        wp_enqueue_script('redirect');

        return '<div id="redirect"></div>';
    }

    /*
     * Output a rider list
     */

    public function rider_list($atts) {
        $args = shortcode_atts(array(
            'script' => '',
            'clear' => 'yes',
            'limit' => 0,
            'rest' => 'no',
            'method' => 'POST',
            'id' => 'rider-list'
                )
                , $atts);

        $script = $args['script'];
        $rest = $args['rest'];
        $id = $args['id'];


        if ($rest === 'yes') {
            $args['resturl'] = get_rest_url() . 'lel/v1/';
            $X_WP_Nonce = wp_create_nonce('wp_rest');
            $args['X_WP_Nonce'] = $X_WP_Nonce;
        }


        if ($script) {
            $args['Timestamp'] = __('Timestamp', 'LEL2017Plugin');
            $args['Event'] = __('Event', 'LEL2017Plugin');
            $args['Control'] = __('Control', 'LEL2017Plugin');
            $args['Distance'] = __('Distance (km)', 'LEL2017Plugin');
            $args['TimeinHand'] = __('Time in Hand', 'LEL2017Plugin');
            wp_localize_script($script, 'wp_var', $args);
            wp_enqueue_script($script);
        }

        $out = '<div id="' . $id . '"></div>';
        return $out;
    }

    public function bagDropLocations($array = false, $id = 0) {
        $result = [];
// userid will be 0 if not logged in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            $result[] = [
                'ID' => 0,
                'post_title' => 'Error'
            ];

            if ($array) {
                return $result;
            } else {
                wp_send_json($result);
            }
        }

        $result = wp_cache_get('bagDropLocations', 'Common');
        if (false !== $result) {
            if ($array) {
                return $result;
            } else {
                wp_send_json($result);
            }
        }

        switch_to_blog(1); // always get data from English site
// Get list of the controls in northbound sequence

        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'post_type' => 'lel_control',
            'meta_key' => 'distance_northbound',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts = get_posts($args);
        $result[] = [
            'ID' => 0,
            'post_title' => __('None', 'LEL2017Plugin')
        ];
        if (count($posts) > 0) {
            foreach ($posts as $post) {
                if (get_field('bag_drops', $post->ID)[0] == 'Yes') {
                    $result[] = [
                        'ID' => $post->ID,
                        'post_title' => $post->post_title
                    ];
                }
            }
        }

        restore_current_blog();

// Cache the result
        wp_cache_set('bagDropLocations', $result, 'Common', $this->cache);


        if ($array) {
            return $result;
        } else {
            wp_send_json($result);
        }
    }

// Other functions here

    public function sort_by_distance($a, $b) {

        return $a['distance'] - $b['distance'];
    }

    public function sort_by_sequence($a, $b) {

        return $a['sequence'] - $b['sequence'];
    }

    public function get_country($iso) {

        global $wpdb;

        $table_name = "countries";

        $sql = "SELECT * FROM " . $table_name;
        $sql .= ' WHERE iso = %s';
        $sql = $wpdb->prepare($sql, $iso);
        $row = $wpdb->get_row($sql);

        $country = $row->name;

        return $country;
    }

    public function get_countryList() {
        return array(
            'AF' => __('Afghanistan', 'woocommerce'),
            'AX' => __('&#197;land Islands', 'woocommerce'),
            'AL' => __('Albania', 'woocommerce'),
            'DZ' => __('Algeria', 'woocommerce'),
            'AD' => __('Andorra', 'woocommerce'),
            'AO' => __('Angola', 'woocommerce'),
            'AI' => __('Anguilla', 'woocommerce'),
            'AQ' => __('Antarctica', 'woocommerce'),
            'AG' => __('Antigua and Barbuda', 'woocommerce'),
            'AR' => __('Argentina', 'woocommerce'),
            'AM' => __('Armenia', 'woocommerce'),
            'AW' => __('Aruba', 'woocommerce'),
            'AU' => __('Austra lia', 'wo ocommerce'),
            'AT' => __('Austria', 'woocommerce'),
            'AZ' => __('Azerbaijan', 'woocommerce'),
            'BS' => __('Bahamas', 'woocommerce'),
            'BH' => __('Bahrain', 'woocommerce'),
            'BD' => __('Bangladesh', 'woocommerce'),
            'BB' => __('Barbados', 'woocommerce'),
            'BY' => __('Belarus', 'woocommerce'),
            'BE' => __('Belgium', 'woocommerce'),
            'PW' => __('Belau', 'woocommerce'),
            'BZ' => __('Belize', 'woocommerce'),
            'BJ' => __('Benin', 'woocommerce'),
            'BM' => __('Bermuda', 'woocommerce'),
            'BT' => __('Bhutan', 'woocommerce'),
            'BO' => __('Bolivia', 'woocommerce'),
            'BQ' => __('Bonaire, Saint Eustatius and Saba', 'woocommerce'),
            'BA' => __('Bosnia and Herzegovina', 'woocommerce'),
            'BW' => __('Botswana', 'woocommerce'),
            'BV' => __('Bouvet Island', 'woocommerce'),
            'BR' => __('Brazil', 'woocommerce'),
            'IO' => __('British Indian Ocean Territory', 'woocommerce'),
            'VG' => __('British Virgin Islands', 'woocommerce'),
            'BN' => __('Brunei', 'woocommerce'),
            'BG' => __('Bulgaria', 'woocommerce'),
            'BF' => __('Burkina Faso', 'woocommerce'),
            'BI' => __('Burundi', 'woocommerce'),
            'KH' => __('Cambodia', 'woocommerce'),
            'CM' => __('Cameroon', 'woocommerce'),
            'CA' => __('Canada', 'woocommerce'),
            'CV' => __('Cape Verde', 'woocommerce'),
            'KY' => __('Cayman Islands', 'woocommerce'),
            'CF' => __('Central African Republic', 'woocommerce'),
            'TD' => __('Chad', 'woocommerce'),
            'CL' => __('Chile', 'woocommerce'),
            'CN' => __('China', 'woocommerce'),
            'CX' => __('Christmas Island', 'woocommerce'),
            'CC' => __('Cocos (Keeling) Islands', 'woocommerce'),
            'CO' => __('Colombia', 'woocommerce'),
            'KM' => __('Comoros', 'woocommerce'),
            'CG' => __('Congo (Brazzaville)', 'woocommerce'),
            'CD' => __('Congo (Kinshasa)', 'woocommerce'),
            'CK' => __('Cook Islands', 'woocommerce'),
            'CR' => __('Costa Rica', 'woocommerce'),
            'HR' => __('Croatia', 'woocommerce'),
            'CU' => __('Cuba', 'woocommerce'),
            'CW' => __('Cura&Ccedil;ao', 'woocommerce'),
            'CY' => __('Cyprus', 'woocommerce'),
            'CZ' => __('Czech Republic', 'woocommerce'),
            'DK' => __('Denmark', 'woocommerce'),
            'DJ' => __('Djibouti', 'woocommerce'),
            'DM' => __('Dominica', 'woocommerce'),
            'DO' => __('Dominican Republic', 'woocommerce'),
            'EC' => __('Ecuador', 'woocommerce'),
            'EG' => __('Egypt', 'woocommerce'),
            'SV' => __('El Salvador', 'woocommerce'),
            'GQ' => __('Equatorial Guinea', 'woocommerce'),
            'ER' => __('Eritrea', 'woocommerce'),
            'EE' => __('Estonia', 'woocommerce'),
            'ET' => __('Ethiopia', 'woocommerce'),
            'FK' => __('Falkland Islands', 'woocommerce'),
            'FO' => __('Faroe Islands', 'woocommerce'),
            'FJ' => __('Fiji', 'woocommerce'),
            'FI' => __('Finland', 'woocommerce'),
            'FR' => __('France', 'woocommerce'),
            'GF' => __('French Guiana', 'woocommerce'),
            'PF' => __('French Polynesia', 'woocommerce'),
            'TF' => __('French Southern Territories', 'woocommerce'),
            'GA' => __('Gabon', 'woocommerce'),
            'GM' => __('Gambia', 'woocommerce'),
            'GE' => __('Georgia', 'woocommerce'),
            'DE' => __('Germany', 'woocommerce'),
            'GH' => __('Ghana', 'woocommerce'),
            'GI' => __('Gibraltar', 'woocommerce'),
            'GR' => __('Greece', 'woocommerce'),
            'GL' => __('Greenland', 'woocommerce'),
            'GD' => __('Grenada', 'woocommerce'),
            'GP' => __('Guadeloupe', 'woocommerce'),
            'GT' => __('Guatemala', 'woocommerce'),
            'GG' => __('Guernsey', 'woocommerce'),
            'GN' => __('Guinea', 'woocommerce'),
            'GW' => __('Guinea-Bissau', 'woocommerce'),
            'GY' => __('Guyana', 'woocommerce'),
            'HT' => __('Haiti', 'woocommerce'),
            'HM' => __('Heard Island and McDonald Islands', 'woocommerce'),
            'HN' => __('Honduras', 'woocommerce'),
            'HK' => __('Hong Kong', 'woocommerce'),
            'HU' => __('Hungary', 'woocommerce'),
            'IS' => __('Iceland', 'woocommerce'),
            'IN' => __('India', 'woocommerce'),
            'ID' => __('Indonesia', 'woocommerce'),
            'IR' => __('Iran', 'woocommerce'),
            'IQ' => __('Iraq', 'woocommerce'),
            'IE' => __('Republic of Ireland', 'woocommerce'),
            'IM' => __('Isle of Man', 'woocommerce'),
            'IL' => __('Israel', 'woocommerce'),
            'IT' => __('Italy', 'woocommerce'),
            'CI' => __('Ivory Coast', 'woocommerce'),
            'JM' => __('Jamaica', 'woocommerce'),
            'JP' => __('Japan', 'woocommerce'),
            'JE' => __('Jersey', 'woocommerce'),
            'JO' => __('Jordan', 'woocommerce'),
            'KZ' => __('Kazakhstan', 'woocommerce'),
            'KE' => __('Kenya', 'woocommerce'),
            'KI' => __('Kiribati', 'woocommerce'),
            'KW' => __('Kuwait', 'woocommerce'),
            'KG' => __('Kyrgyzstan', 'woocommerce'),
            'LA' => __('Laos', 'woocommerce'),
            'LV' => __('Latvia', 'woocommerce'),
            'LB' => __('Lebanon', 'woocommerce'),
            'LS' => __('Lesotho', 'woocommerce'),
            'LR' => __('Liberia', 'woocommerce'),
            'LY' => __('Libya', 'woocommerce'),
            'LI' => __('Liechtenstein', 'woocommerce'),
            'LT' => __('Lithuania', 'woocommerce'),
            'LU' => __('Luxembourg', 'woocommerce'),
            'MO' => __('Macao S.A.R., China', 'woocommerce'),
            'MK' => __('Macedonia', 'woocommerce'),
            'MG' => __('Madagascar', 'woocommerce'),
            'MW' => __('Malawi', 'woocommerce'),
            'MY' => __('Malaysia', 'woocommerce'),
            'MV' => __('Maldives', 'woocommerce'),
            'ML' => __('Mali', 'woocommerce'),
            'MT' => __('Malta', 'woocommerce'),
            'MH' => __('Marshall Islands', 'woocommerce'),
            'MQ' => __('Martinique', 'woocommerce'),
            'MR' => __('Mauritania', 'woocommerce'),
            'MU' => __('Mauritius', 'woocommerce'),
            'YT' => __('Mayotte', 'woocommerce'),
            'MX' => __('Mexico', 'woocommerce'),
            'FM' => __('Micronesia', 'woocommerce'),
            'MD' => __('Moldova', 'woocommerce'),
            'MC' => __('Monaco', 'woocommerce'),
            'MN' => __('Mongolia', 'woocommerce'),
            'ME' => __('Montenegro', 'woocommerce'),
            'MS' => __('Montserrat', 'woocommerce'),
            'MA' => __('Morocco', 'woocommerce'),
            'MZ' => __('Mozambique', 'woocommerce'),
            'MM' => __('Myanmar', 'woocommerce'),
            'NA' => __('Namibia', 'woocommerce'),
            'NR' => __('Nauru', 'woocommerce'),
            'NP' => __('Nepal', 'woocommerce'),
            'NL' => __('Netherlands', 'woocommerce'),
            'AN' => __('Netherlands Antilles', 'woocommerce'),
            'NC' => __('New Caledonia', 'woocommerce'),
            'NZ' => __('New Zealand', 'woocommerce'),
            'NI' => __('Nicaragua', 'woocommerce'),
            'NE' => __('Niger', 'woocommerce'),
            'NG' => __('Nigeria', 'woocommerce'),
            'NU' => __('Niue', 'woocommerce'),
            'NF' => __('Norfolk Island', 'woocommerce'),
            'KP' => __('North Korea', 'woocommerce'),
            'NO' => __('Norway', 'woocommerce'),
            'OM' => __('Oman', 'woocommerce'),
            'PK' => __('Pakistan', 'woocommerce'),
            'PS' => __('Palestinian Territory', 'woocommerce'),
            'PA' => __('Panama', 'woocommerce'),
            'PG' => __('Papua New Guinea', 'woocommerce'),
            'PY' => __('Paraguay', 'woocommerce'),
            'PE' => __('Peru', 'woocommerce'),
            'PH' => __('Philippines', 'woocommerce'),
            'PN' => __('Pitcairn', 'woocommerce'),
            'PL' => __('Poland', 'woocommerce'),
            'PT' => __('Portugal', 'woocommerce'),
            'QA' => __('Qatar', 'woocommerce'),
            'RE' => __('Reunion', 'woocommerce'),
            'RO' => __('Romania', 'woocommerce'),
            'RU' => __('Russia', 'woocommerce'),
            'RW' => __('Rwanda', 'woocommerce'),
            'BL' => __('Saint Barth&eacute;lemy', 'woocommerce'),
            'SH' => __('Saint Helena', 'woocommerce'),
            'KN' => __('Saint Kitts and Nevis', 'woocommerce'),
            'LC' => __('Saint Lucia', 'woocommerce'),
            'MF' => __('Saint Martin (French part)', 'woocommerce'),
            'SX' => __('Saint Martin (Dutch part)', 'woocommerce'),
            'PM' => __('Saint Pierre and Miquelon', 'woocommerce'),
            'VC' => __('Saint Vincent and the Grenadines', 'woocommerce'),
            'SM' => __('San Marino', 'woocommerce'),
            'ST' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'woocommerce'),
            'SA' => __('Saudi Arabia', 'woocommerce'),
            'SN' => __('Senegal', 'woocommerce'),
            'RS' => __('Serbia', 'woocommerce'),
            'SC' => __('Seychelles', 'woocommerce'),
            'SL' => __('Sierra Leone', 'woocommerce'),
            'SG' => __('Singapore', 'woocommerce'),
            'SK' => __('Slovakia', 'woocommerce'),
            'SI' => __('Slovenia', 'woocommerce'),
            'SB' => __('Solomon Islands', 'woocommerce'),
            'SO' => __('Somalia', 'woocommerce'),
            'ZA' => __('South Africa', 'woocommerce'),
            'GS' => __('South Georgia/Sandwich Islands', 'woocommerce'),
            'KR' => __('South Korea', 'woocommerce'),
            'SS' => __('South Sudan', 'woocommerce'),
            'ES' => __('Spain', 'woocommerce'),
            'LK' => __('Sri Lanka', 'woocommerce'),
            'SD' => __('Sudan', 'woocommerce'),
            'SR' => __('Suriname', 'woocommerce'),
            'SJ' => __('Svalbard and Jan Mayen', 'woocommerce'),
            'SZ' => __('Swaziland', 'woocommerce'),
            'SE' => __('Sweden', 'woocommerce'),
            'CH' => __('Switzerland', 'woocommerce'),
            'SY' => __('Syria', 'woocommerce'),
            'TW' => __('Taiwan', 'woocommerce'),
            'TJ' => __('Tajikistan', 'woocommerce'),
            'TZ' => __('Tanzania', 'woocommerce'),
            'TH' => __('Thailand', 'woocommerce'),
            'TL' => __('Timor-Leste', 'woocommerce'),
            'TG' => __('Togo', 'woocommerce'),
            'TK' => __('Tokelau', 'woocommerce'),
            'TO' => __('Tonga', 'woocommerce'),
            'TT' => __('Trinidad and Tobago', 'woocommerce'),
            'TN' => __('Tunisia', 'woocommerce'),
            'TR' => __('Turkey', 'woocommerce'),
            'TM' => __('Turkmenistan', 'woocommerce'),
            'TC' => __('Turks and Caicos Islands', 'woocommerce'),
            'TV' => __('Tuvalu', 'woocommerce'),
            'UG' => __('Uganda', 'woocommerce'),
            'UA' => __('Ukraine', 'woocommerce'),
            'AE' => __('United Arab Emirates', 'woocommerce'),
            'GB' => __('United Kingdom (UK)', 'woocommerce'),
            'US' => __('United States (US)', 'woocommerce'),
            'UY' => __('Uruguay', 'woocommerce'),
            'UZ' => __('Uzbekistan', 'woocommerce'),
            'VU' => __('Vanuatu', 'woocommerce'),
            'VA' => __('Vatican', 'woocommerce'),
            'VE' => __('Venezuela', 'woocommerce'),
            'VN' => __('Vietnam', 'woocommerce'),
            'WF' => __('Wallis and Futuna', 'woocommerce'),
            'EH' => __('Western Sahara', 'woocommerce'),
            'WS' => __('Western Samoa', 'woocommerce'),
            'YE' => __('Yemen', 'woocommerce'),
            'ZM' => __('Zambia', 'woocommerce'),
            'ZW' => __('Zimbabwe', 'woocommerce')
        );
    }

    public function my_details() {
        $user_id = get_current_user_id();
        if ($user_id != 0) {

            $X_WP_Nonce = wp_create_nonce('wp_rest');
            wp_localize_script('lel-my-details', 'wp_var', array(
                'resturl' => get_rest_url() . 'lel/v1/',
                'method' => 'POST',
                'X_WP_Nonce' => $X_WP_Nonce,
            ));


            add_action('wp_enqueue_scripts', wp_enqueue_script('lel-my-details'));




            $user_info = get_userdata($user_id);
// Name
            $first_name = $user_info->first_name ?? $user_info->billing_first_name;
            $last_name = $user_info->last_name ?? $user_info->billing_last_name;
            ;
// email
            $email = $user_info->user_email ?? $user_info->billing_email;
// Address
            $address1 = $user_info->billing_address_1;
            $address2 = $user_info->billing_address_2;
            $city = $user_info->billing_city;
            $county = $user_info->billing_state;

// Countries is a dropdown
            $country = $user_info->billing_country;
// Get a list of countries

            $country_list = $this->get_countryList();

            $postcode = $user_info->billing_postcode;

            $phone = $user_info->billing_phone;

            $emergencyContact = $user_info->emergency_contact;
            $emergencyPhone = $user_info->emergency_phone;

            $teamname = $user_info->team_name;

            $tshirt_size = $user_info->tshirt_size;
// Food / dietary requirements
            $vegetarian = $user_info->vegetarian ?? 'no';
            $vegan = $user_info->vegan ?? 'no';
            $lactose = $user_info->lactose ?? 'no';
            $gluten = $user_info->gluten ?? 'no';
            $nut = $user_info->nut ?? 'no';

// Online tracking privacy_data

            $show_checkin = $user_info->show_checkin ?? 'yes';
            $show_checkout = $user_info->show_checkout ?? 'yes';
            $show_bedbook = $user_info->show_bedbook ?? 'yes';

            // Rider number

            $rider_id = $user_info->rider_id;



            $output = '<form id="lel-my-details">'
                    . '<h4>' . $first_name . ' ' . $last_name . '</h4>';
            if (!empty($rider_id)) {
                $output .= '<div id="lel-extra" class="alert alert-info">'
                        . '<p>' . __('Your rider number is ', 'LEL2017Plugin') . ' ' . $rider_id . '.</p>';
                $output .= '</div>';
            }

            $output .= '<fieldset>'
                    . '<legend>' . __('Password', 'LEL2017Plugin') . ':</legend> '
                    . '<label for="Password1">' . __('New password', 'LEL2017Plugin') . ':</label> '
                    . '<input type="password" class="form-control" name="Password1">'
                    . '<label for="Password2">' . __('Confirm new password', 'LEL2017Plugin') . ':</label> '
                    . '<input type="password" class="form-control" name="Password2"><br>'
                    . '</fieldset>'
                    . '<fieldset>'
                    . '<legend>' . __('Email', 'LEL2017Plugin') . ':</legend> '
                    . '<label for="Email1">' . __('New email', 'LEL2017Plugin') . ':</label> '
                    . '<input type="email" class="form-control" name="Email1" value="' . $email . '">'
                    . '<label for="Email2">' . __('Confirm new email', 'LEL2017Plugin') . ':</label> '
                    . '<input type="email" class="form-control" name="Email2" placeholder="' . __('Please confirm new email address', 'LEL2017Plugin') . '"><br>'
                    . '</fieldset>'
                    . '<fieldset>'
                    . '<legend>' . __('Contact', 'LEL2017Plugin') . ':</legend> '
                    . '<label for="Line1">' . __('Line 1', 'LEL2017Plugin') . ':</label> '
                    . '<input type="text" class="form-control" name="Line1" value="' . $address1 . '">'
                    . '<label for="Line2">' . __('Line 2', 'LEL2017Plugin') . ':</label> '
                    . '<input type="text" class="form-control" name="Line2" value="' . $address2 . '">'
                    . '<label for="City">' . __('City', 'LEL2017Plugin') . ':</label> '
                    . '<input type="text" class="form-control" name="City" value="' . $city . '">'
                    . '<label for="County">' . __('County', 'LEL2017Plugin') . ':</label> '
                    . '<input type="text" class="form-control" name="County" value="' . $county . '">'

// Create a dropdown
                    . '<label for="Country">' . __('Country', 'LEL2017Plugin') . ':</label> '
                    . '<select class="form-control" name="Country">';
            foreach ($country_list as $key => $value) {
                $output .= '<option value="' . $key . '"' . ($key === $country ? ' selected ' : '') . '>' . $value . '</option>';
            }
            $output .= '</select>';

            $output .= '<label for="PostCode">' . __('Post Code', 'LEL2017Plugin') . ':</label> ';
            $output .= '<input type="text" class="form-control" name="PostCode" value="' . $postcode . '">';
            $output .= '<br>';

            $output .= '<label for="Phone">' . __('Phone', 'LEL2017Plugin') . ':</label> ';
            $output .= '<input type="text" class="form-control" name="Phone" value="' . $phone . '">';
            $output .= '</fieldset>';
            $output .= '<br>';
            $output .= '<fieldset>';
            $output .= '<legend>' . __('Emergency', 'LEL2017Plugin') . ':</legend> ';
            $output .= '<label for="EmergencyContact">' . __('Name', 'LEL2017Plugin') . ':</label> ';
            $output .= '<input type="text" class="form-control" name="EmergencyContact" value="' . $emergencyContact . '">';
            $output .= '<label for="EmergencyPhone">' . __('Phone', 'LEL2017Plugin') . ':</label> ';
            $output .= '<input type="text" class="form-control" name="EmergencyPhone" value="' . $emergencyPhone . '">';
            $output .= '</fieldset>';
            $output .= '<br>';
            $output .= '<fieldset>';


            $output .= '<fieldset>'
                    . '<legend>' . __('Food', 'LEL2017Plugin') . ':</legend> '
                    . '<p>' . __('To improve the food choices at the event please indicate your dietary needs. We cannot guarantee gluten, nut or lactose-free food at controls.', 'LEL2017Plugin')
                    . '<div class="checkbox">'
                    . '<label><input type="checkbox" name="Vegetarian" value="yes" '
                    . ($vegetarian === 'yes' ? 'checked' : '')
                    . '> '
                    . __('Vegetarian', 'LEL2017Plugin')
                    . '</label><br>'
                    . '<label><input type="checkbox" name="Vegan" value="yes" '
                    . ($vegan === 'yes' ? 'checked' : '')
                    . '>'
                    . __('Vegan', 'LEL2017Plugin')
                    . '</label><br>'
                    . '<label><input type="checkbox" name="Gluten" value="yes" '
                    . ($gluten === 'yes' ? 'checked' : '')
                    . '>'
                    . __('Gluten Intolerant', 'LEL2017Plugin')
                    . '</label><br>'
                    . '<label><input type="checkbox" name="Nut" value="yes" '
                    . ($nut === 'yes' ? 'checked' : '')
                    . '>'
                    . __('Nut Allergy', 'LEL2017Plugin')
                    . '</label><br>'
                    . '<label><input type="checkbox" name="Lactose" value="yes" '
                    . ($lactose === 'yes' ? 'checked' : '')
                    . '>'
                    . __('Lactose Intolerant', 'LEL2017Plugin')
                    . '</label><br>'
                    . '</div>';
            if (current_user_can('access_rider_area')) {
// Tracking Privacy

                $output .= '<fieldset>'
                        . '<legend>' . __('Privacy', 'LEL2017Plugin') . ':</legend> '
                        . '<p>' . __('By default, all of your online event tracking data will be made public on this website. You have the option to change this below. ', 'LEL2017Plugin') . '</p>'
                        . '<div class="checkbox">'
                        . '<label><input type="checkbox" name="Show_checkin" value="yes" '
                        . ($show_checkin === 'yes' ? 'checked' : '')
                        . '> '
                        . __('Show when I check in to a control location', 'LEL2017Plugin')
                        . '</label><br>'
                        . '<label><input type="checkbox" name="Show_checkout" value="yes" '
                        . ($show_checkout === 'yes' ? 'checked' : '')
                        . '> '
                        . __('Show when I check out of a control location', 'LEL2017Plugin')
                        . '</label><br>'
                        . '<label><input type="checkbox" name="Show_bedbook" value="yes" '
                        . ($show_bedbook === 'yes' ? 'checked' : '')
                        . '> '
                        . __('Show when I book a bed at a control location', 'LEL2017Plugin')
                        . '</label><br>'
                        . '</div>';
            }

            $output .= '<legend>' . __('Other', 'LEL2017Plugin') . ':</legend> ';
            if (current_user_can('access_rider_area')) {
                if (get_option('lock_start_times') !== 'yes') {
                    $output .= '<label for="TeamName">' . __('Team Name', 'LEL2017Plugin') . ':</label> ';
                    $output .= '<input type="text" class="form-control" name="TeamName" value="' . $teamname . '">';
                    $output .= '</fieldset>';
                } else {
                    $output .= '<p><strong>' . __('Team Name', 'LEL2017Plugin') . '</strong>: ' . $teamname . '</p>';
                }

                $output .= '<br>';
            }



            if (current_user_can('access_volunteer_area')) {

                $tshirt_sizes = [
                    'None' => 'None',
                    'Small' => 'Small - 36&quot; - 91cm',
                    'Medium' => 'Medium - 40&quot; - 102cm',
                    'Large' => 'Large - 44&quot; - 112cm',
                    'XL' => 'XL - 48&quot; - 122cm',
                    'XXL' => 'XXL - 52&quot; - 132cm'
                ];

                $output .= '<label for="tShirtSize">t-shirt:</label>'
                        . '<select class="form-control" name="tShirtSize">';

                foreach ($tshirt_sizes as $key => $value) {
                    $output .= '<option value="' . $key . '"';
                    if ($tshirt_size === $key) {
                        $output .= ' selected';
                    }
                    $output .= '>' . $value . '</option>';
                }

                $output .= '</select>'
                        . '<br>';
            }


            $output .= '<input type="submit" class="btn btn-primary" value="' . __('Update', 'LEL2017Plugin') . '">';

            $output .= '</form><br><br>';
            $output .= '<div id="lel-update-message" ></div>';


            return $output;
        }
    }

    public function default_value($var, $default) {
        return is_null($var) ? $default : $var;
    }

    public function get_start_waves() {
// Try cache first
        $result = wp_cache_get('riderStartTimes', 'Common');
        if (false !== $result) {
            return $result;
        }
// Always work against English blog for db access
        switch_to_blog(1);
        global $wpdb;
// Get the start wave time information
        $table_name = $wpdb->prefix . 'startwaves';
        $sql = "SELECT * FROM " . $table_name . " ORDER BY id";
        $result = $wpdb->get_results($sql);
        restore_current_blog();
// Cache the result
        wp_cache_set('riderStartTimes', $result, 'Common', $this->cache);

        return $result;
    }

    /*
     * Get arrival and departure tracking data for riders
     */

    public function get_tracking_riders() {


        $results = wp_cache_get('trackingRiders', 'Common');

        if ($results !== false) {
            return $results;
        }


        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT rider_id, ROUND(time_in_hand / 3600 , 1) as time_in_hand, elapsed, distance FROM ' . $table_name
                . ' WHERE '
                . ' action = "Arrival"'
                . ' OR action = "Departure"'
                . ' ORDER BY rider_id, distance, timestamp';

        $results = $wpdb->get_results($sql);
        wp_cache_set('trackingRiders', $results, 'Common', 18000);
        return $results;
    }

    public function get_tracking($rider_user_id) {


        $results = wp_cache_get('tracking' . $rider_user_id, 'Common');

        if ($results !== false) {
            return $results;
        }


        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT * FROM ' . $table_name . ' WHERE rider_id = %d';
        $sql = $wpdb->prepare($sql, $rider_user_id);
        $results = $wpdb->get_results($sql);
        wp_cache_set('tracking' . $rider_user_id, $results, 'Common', 18000);
        return $results;
    }

    public function get_tracking_forecast() {
        $results = wp_cache_get('tracking_forecast', 'Common');
        if ($results !== false) {
            return $results;
        }
        global $wpdb;


        $table_name = $wpdb->prefix . "tracking";

        $sql = 'select * from'
                . '(select UNIX_TIMESTAMP(timestamp) - 3600 AS timestamp, rider_id, control_id, direction, distance, action from ' // - 3600 as timestamps hled in BST
                . $table_name
                . ' where id in ('
                . 'SELECT  max(id) FROM '
                . $table_name
                . ' group by rider_id'
                . ')) as a,'
                . '(SELECT id, rider_id, average_speed_leg from '
                . $table_name
                . ' where id in ('
                . 'SELECT max(id) FROM '
                . $table_name
                . ' where average_speed_leg is not null group by rider_id'
                . ') ) as b'
                . ' where a.rider_id = b.rider_id';

        $results = $wpdb->get_results($sql, 'ARRAY_A');
        wp_cache_set('tracking_forecast', $results, 'Common', 60 ?? $this->cache);
        return $results;
    }

    public function get_control_tracking() {


        $results = wp_cache_get('tracking_controls', 'Common');

        if ($results !== false) {
            return $results;
        }


        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT control_id, direction, COUNT(*) as total FROM ' . $table_name;
        $sql .= ' WHERE id in (SELECT MAX(id) as id FROM ' . $table_name . ' GROUP BY rider_id) ';
        $sql .= ' AND action <> "Registration"';
        $sql .= ' AND action <> "Entered Start Pen"';
        $sql .= ' AND action <> "Bag Drop Here"';
        $sql .= ' GROUP BY control_id, direction';

        $results = $wpdb->get_results($sql, 'ARRAY_A');
        wp_cache_set('tracking_controls', $results, 'Common', $this->cache);

        return $results;
    }

    public function get_control_tracking_scans() {


        $results = wp_cache_get('tracking_controls_scan', 'Common');

        if ($results !== false) {
            return $results;
        }


        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT control_id, action, COUNT(*) as total FROM ' . $table_name;
        $sql .= ' WHERE id in (SELECT MAX(id) as id FROM ' . $table_name . ' GROUP BY rider_id) AND action NOT IN ("Registration","Bag Drop Arrival", "Bag Drop Departure")';
        $sql .= ' GROUP BY control_id, action ';

        $results = $wpdb->get_results($sql, 'ARRAY_A');
        wp_cache_set('tracking_controls_scan', $results, 'Common', $this->cache);

        return $results;
    }

    /*
     * Get active riders on the road,
     * plus numbers through each control
     */

    public function get_tracking_active() {


        $results = wp_cache_get('tracking_active', 'Common');

        if ($results !== false) {
            return $results;
        }

        // Active riders
        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT count(*) FROM '
                . $table_name
                . ' WHERE action = "Registration" AND '
                . 'rider_id NOT IN '
                . '(SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "DNF")';

        $active_riders = $wpdb->get_var($sql);

        $sql = 'SELECT a.control_id, a.direction, count(*) AS total FROM '
                . '(SELECT DISTINCT control_id, rider_id, direction FROM '
                . $table_name
                . ' WHERE action = "Arrival"'
                . ' UNION '
                . 'SELECT DISTINCT control_id, rider_id, direction FROM '
                . $table_name
                . ' WHERE action = "Departure" and control_id = 4785'
                . ') AS a '
                . ' GROUP BY control_id, direction';

        $results = $wpdb->get_results($sql, 'ARRAY_A');



        $output = [
            active => $active_riders,
            controls => $results
        ];

        wp_cache_set('tracking_active', $output, 'Common', $this->cache);
        return $output;
    }

    /*
     * Get tracking activity in last 4 hours grouped in 15 min
     * intervals
     */

    public function get_tracking_recent() {


        $results = wp_cache_get('tracking_recent', 'Common');

        if ($results !== false) {
            return $results;
        }

        // Active riders
        global $wpdb;

        $table_name = $wpdb->prefix . "tracking";

        $sql = 'SELECT control_id, action, FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(timestamp)/(15*60)) * 15 * 60) AS timeint, count(*) as total'
                . ' FROM '
                . $table_name
                . ' WHERE DATE_SUB(NOW(), INTERVAL 4 HOUR) < timestamp '
                . ' GROUP BY control_id, timeint, action ORDER BY control_id, timestamp, action';



        $result = $wpdb->get_results($sql, 'ARRAY_A');

        wp_cache_set('tracking_recent', $result, 'Common', $this->cache);
        return $result;
    }

    /*
     * Override the WordPress default expiry times for auth cookies
     */

    public function cookie_expiration($seconds, $user_id, $remember) {
// Ignore those who can access backend

        if (!user_can($user_id, 'access_tracking_area') && !user_can($user_id, 'access_register_area')) {

//if "remember me" is checked;

            if ($remember) {

// 12 hours

                $seconds = 12 * 60 * 60;
            } else {

// 1 hour;

                $seconds = 1 * 60 * 60;
            }
        }
        return $seconds;
    }

    /*
     * Return the tracking data for a rider
     */

    public function get_rider_tracking($rider_id) {

// Try cache first
        $result = wp_cache_get('tracking' . $rider_id, 'Common');
        if (false !== $result) {
            return $result;
        }
// Always work against English blog for db access
        switch_to_blog(1);
        global $wpdb;
// Get the start tracking information
        $table_name = $wpdb->prefix . 'tracking';
        $sql = "SELECT * FROM " . $table_name;
        if ($rider_id !== '') {
            $sql .= ' WHERE rider_id = %d';
        }
        $sql .= ' ORDER BY timestamp DESC';
        $sql = $wpdb->prepare($sql, $rider_id);

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        restore_current_blog();
// Cache the result
        wp_cache_set('tracking' . $rider_id, $result, 'Common', 1200);

        return $result;
    }

    public function get_event_distance() {
        $result = wp_cache_get('eventDistance', 'Common');
        if (false !== $result) {
            return $result;
        }
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'post_type' => 'lel_control',
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts = get_posts($args);
        $result = 0;
        foreach ($posts as $post) {
            $result = max($result, $post->distance_northbound);
            $result = max($result, $post->distance_southbound);
        }
        wp_cache_set('eventDistance' . $rider_id, $result, 'Common', 3600); //Cache 1 hour
        return $result;
    }

    /*
     * Generate a visa letter
     */

    public function visa_pdf() {

        $user_id = get_current_user_id();
        if ($user_id != 0) {

            $user_info = get_userdata($user_id);
// Name
            $first_name = $user_info->first_name ?? $user_info->billing_first_name;
            $last_name = $user_info->last_name ?? $user_info->billing_last_name;
            $output = '<style> p {
            font: 16px;
            }</style>'
                    . '<img src="https://s3-eu-west-1.amazonaws.com/aws-lel2017-static-assets/wp-content/uploads/2015/10/04161144/LEL2017_LogoWordmark_Date-EN_GB.png" alt="LEL2017_Header" width="471" height="132" />'
                    . '<h1>30th July to 4th August 2017</h1>'
                    . '<p>The event organisers are pleased to invite ' . $first_name . ' ' . $last_name . ' to London to take part in the London-Edinburgh-London 1400km Randonee cycle ride.</p>'
                    . '<p>You will need to arrive in London, United Kingdom in time to attend the pre-event registration on 29th July 2017.</p>'
                    . '<p>On behalf of LEL 2017 Limited<br>'
                    . 'Roger Cortis<br>'
                    . 'Director<br>'
                    . 'Telephone 0044 1508 470478<br>'
                    . '<a href="http://londonedinburghlondon.com/">https://londonedinburghlondon.com</a>'
                    . '</p >';
            /*
             * Collect visa output as pdf
             */
            require_once(dirname(__FILE__) . '/html2pdf/html2pdf.class.php');

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetAuthor('Roger Cortis');
            $html2pdf->pdf->SetTitle('LEL2017 Visa Letter');
            $html2pdf->pdf->SetSubject('LEL2017 Visa application letter');
            $html2pdf->pdf->SetKeywords('LEL2017, London, Visa');
            $html2pdf->WriteHTML($output);
            $html2pdf->Output('Visa Letter.pdf');
        }
        die();
    }

    function join_site($user_login, $user) {

        $user_id = $user->ID;
        $blog_id = get_current_blog_id();

        if (0 === $user_id)
            return false;

        if (1 !== $blog_id) {
            return false;
        }


        if (!$user->exists())
            return false;
// Get role 
        $role = $user->roles[0];

// Add to other blogs with role rider if not already setup
        if ($role === 'rider') {

            if (!is_user_member_of_blog($user_id, 3)) {
                add_user_to_blog(3, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 4)) {
                add_user_to_blog(4, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 12)) {
                add_user_to_blog(12, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 13)) {
                add_user_to_blog(13, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 14)) {
                add_user_to_blog(14, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 15)) {
                add_user_to_blog(15, $user_id, $role);
            }
            if (!is_user_member_of_blog($user_id, 20)) {
                add_user_to_blog(20, $user_id, $role);
            }
        }
    }

    /*
     * Override the password reset email
     */

    public function retrieve_password_message($message, $key, $user_login, $user_data) {

        $user_email = $user_data->user_email;

        $message = __('Someone has requested a password reset for the following account:', 'LEL2017Plugin') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Email: %s'), $user_email) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . home_url("/reset-password/?key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        return $message;
    }

    /*
     * Override the password change email
     */

    public function password_change_email($pass_change_email, $user, $userdata) {
        $pass_change_email['message'] = str_replace('###USERNAME###', $user['first_name'], $pass_change_email['message']);
        return $pass_change_email;
    }

    /*
     * Override email change email
     */

    public function email_change_email($email_change_email, $user, $userdata) {
        $email_change_email['message'] = str_replace('###USERNAME###', $user['first_name'], $email_change_email['message']);
        return $email_change_email;
    }

    /*
     * Make the lifetime of a nonce to 1-2 hours
     */

    public function nonce_life() {

        if (!is_user_logged_in()) {
            return 20 * 60; // 20 minutes
        }
        return 7 * DAY_IN_SECONDS;
    }

    /*
     * Change the lost password url
     */

    function lostpassword_url($lostpassword_url, $redirect) {
        return '/lostpassword/';
    }

    /*
     * Change the lost password url
     */

    public function login_url($login_url, $redirect) {
        return '/login/';
    }

    public function logout_url($logout_url, $redirect) {

        return '/login/?logout=1';
    }

    public function lostpassword_redirect() {
        return home_url();
    }

    /**
     * Handles sending password retrieval email to user.
     *
     * @return bool|WP_Error True: when finish. WP_Error on error
     */
    function retrieve_password() {
        $errors = new WP_Error();

        if (empty($_POST['user_login'])) {
            $errors->add('empty_username', __('Enter a username or email address.'));
        } elseif (strpos($_POST['user_login'], '@')) {
            $user_data = get_user_by('email', trim(wp_unslash($_POST['user_login'])));
            if (empty($user_data))
                $errors->add('invalid_email', __('There is no user registered with that email address.'));
        } else {
            $login = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
        }

        /**
         * Fires before errors are returned from a password reset request.
         *
         * @since 2.1.0
         * @since 4.4.0 Added the `$errors` parameter.
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action('lostpassword_post', $errors);

        if ($errors->get_error_code())
            return $errors;

        if (!$user_data) {
            $errors->add('invalidcombo', __('Invalid username or email.'));
            return $errors;
        }

// Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key = get_password_reset_key($user_data);

        if (is_wp_error($key)) {
            return $key;
        }

        $message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        if (is_multisite()) {
            $blogname = get_network()->site_name;
        } else {
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        /* translators: Password reset email subject. 1: Site name */
        $title = sprintf(__('[%s] Password Reset'), $blogname);

        /**
         * Filters the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters('retrieve_password_title', $title, $user_login, $user_data);

        /**
         * Filters the message body of the password reset mail.
         *
         * If the filtered message is empty, the password reset email will not be sent.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);

        if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message))
            wp_die(__('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.'));

        return true;
    }

    /*
     * Change how long password reset links are valid
     */

    public function password_reset_expiration() {
        return 1800; // 20 minutes
    }

    /*
     * Change the rest url prefix
     */

    function rest_url_prefix($slug) {

        return 'api';
    }

    public function template_redirect() {
        if (is_page('login')) {

// Check to see if form submitted
            if (isset($_POST['_verify']) && isset($_POST['user']) && isset($_POST['pwd'])) {
                $wp_nonce = $_POST['_verify'];
                $username = $_POST['user'];
                $password = $_POST['pwd'];
                $rememberme = $_POST['rememberme'] ? true : false;
                $redirect_to = $_POST['redirect_to'];

                if (wp_verify_nonce($wp_nonce, 'login')) {
                    $user = wp_authenticate($username, $password);
                    if (is_wp_error($user)) {
                        $error_msg = '<div class="alert alert-danger">'
                                . __('Username, email address, or password are incorrect', 'LEL2017Plugin')
                                . '</div>';
                    } else {
                        $user_id = $user->ID;
                        wp_set_auth_cookie($user_id, $rememberme);
                        wp_set_current_user($user_id);
                        if ($redirect_to) {
                            wp_safe_redirect($redirect_to);
                            exit;
                        } else {
                            $this->redirect_user();
//Fall back to home page
                            wp_safe_redirect(site_url());
                            exit;
                        }
                    }
                } else {
                    $error_msg = '<div class="alert alert-danger">'
                            . __('Please refresh page', 'LEL2017Plugin')
                            . '</div>';
                }
            }

            if (isset($_GET['logout']) && !$error_message) {
                wp_logout();
                wp_safe_redirect(wp_login_url() . '?loggedout=1');
                exit;
            }
        }

        if (is_page('reset-password')) {
            list( $rp_path ) = explode('?', wp_unslash($_SERVER['REQUEST_URI']));
            $rp_cookie = 'wp-resetpass-' . COOKIEHASH;

            if (isset($_GET['key'])) {

                $value = sprintf('%s:%s', wp_unslash($_GET['login']), wp_unslash($_GET['key']));
                setcookie($rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
                wp_safe_redirect(remove_query_arg(array('key', 'login')));
                exit;
            }

            if (isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie], ':')) {

                list( $rp_login, $rp_key ) = explode(':', wp_unslash($_COOKIE[$rp_cookie]), 2);
                $user = check_password_reset_key($rp_key, $rp_login);


                if (isset($_POST['pass1']) && !hash_equals($rp_key, $_POST['rp_key'])) {
                    $user = false;
                }
            } else {
                $user = false;
            }

            if (!$user || is_wp_error($user)) {
//setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(wp_lostpassword_url() . '?expiredkey=1');
                    exit;
                } else {
                    wp_redirect(wp_lostpassword_url() . '?invalidkey=1');
                    exit;
                }
            }


            $errors = new WP_Error();

            if (isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'])
                $errors->add('password_reset_mismatch', __('The passwords do not match.', 'LEL2017Plugin'));

            do_action('validate_password_reset', $errors, $user);


            if ((!$errors->get_error_code() ) && isset($_POST['pass1']) && !empty($_POST['pass1'])) {
                reset_password($user, $_POST['pass1']);
                setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
                wp_safe_redirect(wp_login_url() . '?resetpass=1');
                exit;
            }
        }
    }

    /*
     * Admin Menu
     */

    public function admin_menu() {

        add_options_page('LEL2017 Settings', 'LEL2017', 'lel2017_update_settings', 'LEL2017', [$this, 'lel2017_options']);
    }

    /*
     * register settings
     */

    function register_settings() { // whitelist options
        register_setting('LEL2017', 'lock_start_times');
        register_setting('LEL2017', 'lock_bag_drops');
        register_setting('LEL2017', 'lock_tracking');
        register_setting('LEL2017', 'lock_public_tracking');
    }

    /*
     * Draw the options page
     */

    public function lel2017_options() {
        $X_WP_Nonce = wp_create_nonce('wp_rest');
        ?>
        <div class="wrap">
            <h1>LEL2017 Settings</h1>
            <p>Update the settings as required.</p>

            <form method="post" action="options.php">
                <?php
                settings_fields('LEL2017');
                do_settings_sections('LEL2017');

                // rider allocated starts
                if ($_FILES['allocated_start']) {
                    $this->process_allocated_starts();
                    settings_errors();
                }
                // rider bag drops
                if ($_FILES['bag_drops']) {
                    $this->process_bag_drops();
                    settings_errors();
                }
                // Volunteer details
                if ($_FILES['volunteer_details']) {
                    $this->process_volunteer_details();
                    settings_errors();
                }
                ?>
                <h2>Rider</h2>
                <div class="checkbox">
                    <label>
                        <input name="lock_start_times" type="checkbox" value="yes" <?php checked('yes', esc_attr(get_option('lock_start_times'))) ?>>Lock Start Times
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input name="lock_bag_drops" type="checkbox" value="yes" <?php checked('yes', esc_attr(get_option('lock_bag_drops'))) ?>>Lock Bag Drops
                    </label>
                </div>
                <h2>Volunteer</h2>
                <div class="checkbox">
                    <label>
                        <input name="lock_tracking" type="checkbox" value="yes" <?php checked('yes', esc_attr(get_option('lock_tracking'))) ?>>Lock Tracking
                    </label>
                </div>
                <h2>Public</h2>
                <div class="checkbox">
                    <label>
                        <input name="lock_public_tracking" type="checkbox" value="yes" <?php checked('yes', esc_attr(get_option('lock_public_tracking'))) ?>>Lock Tracking
                    </label>
                </div>

                <?php submit_button(); ?>
            </form>

            <h1>LEL2017 Exports</h1>
            <p>Available export links are below</p>
            <a href="/api/lel/v1/csv/riders/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Start Times export</a><br>
            <a href="/api/lel/v1/csv/ridersbagdrops/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Bag Drops export</a><br>
            <a href="/api/lel/v1/csv/ridersframecards/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Frame Cards export</a><br>
            <a href="/api/lel/v1/csv/riderspersonaldetails/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Personal Details export</a><br>
            <a href="/api/lel/v1/csv/ridersstartlists/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Start Lists export</a><br>
            <a href="/api/lel/v1/csv/ridersfinishlists/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Finish Lists export</a><br>
            <a href="/api/lel/v1/csv/orders/?_wpnonce=<?php echo $X_WP_Nonce ?>">Orders export</a><br>

            <a href="/api/lel/v1/csv/ridersinsurance/?_wpnonce=<?php echo $X_WP_Nonce ?>">Rider Insurance export</a><br>


            <a href="/api/lel/v1/csv/volunteerspersonaldetails/?_wpnonce=<?php echo $X_WP_Nonce ?>">Volunteer Personal Details export</a><br>


            <h1>LEL2017 Imports</h1>

            <p>Available imports  are below</p>

            <form method="post" enctype="multipart/form-data" >

                <h2>Rider</h2>
                <p>Import allocated start times and rider numbers</p>

                <input name="allocated_start" type="file" >

                <?php submit_button(); ?>

            </form>

            <form method="post" enctype="multipart/form-data" >

                <p>Import bag drop allocations</p>

                <input name="bag_drops" type="file" >

                <?php submit_button(); ?>

            </form>


            <form method="post" enctype="multipart/form-data" >

                <h2>Volunteer</h2>
                <p>Import volunteer details</p>

                <input name="volunteer_details" type="file" >

                <?php submit_button(); ?>

            </form>



        </div>



        <?php
    }

    /*
     * Process file of allocated starts
     */

    public function process_allocated_starts() {
        if (!isset($_FILES['allocated_start'])) {
            add_settings_error('start_alloc', 'st00', 'No start time file provided', 'error');
            return;
        }
        if ($_FILES['allocated_start']['size'] > 200000) {
            add_settings_error('start_alloc', 'st01', 'Keep start time file under 200kb', 'error');
            return;
        }

        if ($_FILES['allocated_start']['error'] === UPLOAD_ERR_NO_FILE) {

            add_settings_error('start_alloc', 'st02', 'No start time file provided', 'error');
            return;
        }


        if ($_FILES['allocated_start']['size'] === 0) {
            add_settings_error('start_alloc', 'st03', 'Start time file cannot be empty', 'error');
            return;
        }
        $filename = $_FILES['allocated_start']['tmp_name'];
        // Check MIME Type



        if (mime_content_type($filename) !== 'text/plain') {
            add_settings_error('start_alloc', 'st04', 'Allocated start time file must be csv format', 'error');
            return;
        }
        // Now process file

        $fd = fopen($filename, "r");
        // get csv headers
        $head = fgetcsv($fd, 1000);
        if ($head === FALSE) {
            add_settings_error('start_alloc', 'st05', 'Allocated start time file must be csv format', 'error');
            return;
        }
        if ($head[8] !== "Designated Start") {
            add_settings_error('start_alloc', 'st06', 'Allocated start time file must be same format as exported file', 'error');
            return;
        }
        // now loop through the data
        while (($data = fgetcsv($fd, 1000)) !== FALSE) {
            $designated_start = null;
            $rider_id = null;

            $user = get_user_by('login', $data[0]);

            $user_id = $user ? $user->ID : null;


            if ($user_id) {

                if ($data[8]) {
                    $tmp = $data[8];
                    $dd = substr($tmp, 0, 2);
                    $mm = substr($tmp, 3, 2);
                    $yyyy = substr($tmp, 6, 4);
                    $hhmm = substr($tmp, 11, 5);
                    $designated_start = $yyyy . '-' . $mm . '-' . $dd . ' ' . $hhmm;
                    update_user_meta($user_id, 'designated_start', $designated_start);
                }
                $time_limit = $data[9] ? $data[9] : '116:40';
                update_user_meta($user_id, 'designated_limit', $time_limit);


                if ($data[10]) {
                    $rider_id = $data[10];
                    update_user_meta($user_id, 'rider_id', $rider_id);
                }
            }
        }
        fclose($fd);

        // Success
        add_settings_error('start_alloc', 'st07', 'Allocated start times successfully updated', 'updated');
    }

    /*
     * Process Rider Bag Drops
     */

    public function process_bag_drops() {
        if (!isset($_FILES['bag_drops'])) {
            add_settings_error('bag_drops', 'bd00', 'No rider bag drops file provided', 'error');
            return;
        }
        if ($_FILES['bag_drops']['size'] > 200000) {
            add_settings_error('bag_drops', 'bd01', 'Keep bag drops file under 200kb', 'error');
            return;
        }

        if ($_FILES['bag_drops']['error'] === UPLOAD_ERR_NO_FILE) {

            add_settings_error('bag_drops', 'bd02', 'No bag drop details provided', 'error');
            return;
        }


        if ($_FILES['bag_drops']['size'] === 0) {
            add_settings_error('bag_drops', 'bd03', 'Bag drop details file cannot be empty', 'error');
            return;
        }
        $filename = $_FILES['bag_drops']['tmp_name'];
        // Check MIME Type



        if (mime_content_type($filename) !== 'text/plain') {
            add_settings_error('bag_drops', 'bd04', 'Bag drop details file must be csv format', 'error');
            return;
        }
        // Get list of controls
        switch_to_blog(1);
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'post_type' => 'lel_control',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts = get_posts($args);
        $controls = [];
        foreach ($posts as $post) {
            $controls[$post->post_title] = $post->ID;
        };


        // Now process file

        $fd = fopen($filename, "r");
        // get csv headers
        $head = fgetcsv($fd, 1000);
        if ($head === FALSE) {
            add_settings_error('bag_drops', 'bd05', 'Bag drop details file must be csv format', 'error');
            return;
        }
        if ($head[3] !== "Bag Drop 1") {
            add_settings_error('bag_drops', 'vd06', 'Bag drop details file must be same format as exported file', 'error');
            return;
        }
        // now loop through the data
        while (($data = fgetcsv($fd)) !== FALSE) {

            $userdata = [
            email => $data[0],
            bag_drop_1 => $controls[$data[3]] ?? null,
            bag_drop_2 => $controls[$data[4]] ?? null,
            ];

            $user = get_user_by('email', $userdata['email']);
            $user_id = $user ? $user->ID : null;

            if ($user_id) {

                foreach ($userdata as $key => $value) {
                    $value = sanitize_text_field($value);
                    update_user_meta($user_id, $key, $value);
                }
            }
        }
        fclose($fd);

        // Success
        add_settings_error('bag_drops', 'bd07', 'Bag Drop details successfully updated', 'updated');
    }

    /*
     * Process Volunteer details
     */

    public function process_volunteer_details() {
        if (!isset($_FILES['volunteer_details'])) {
            add_settings_error('volunteer_details', 'vd00', 'No volunteer details file provided', 'error');
            return;
        }
        if ($_FILES['volunteer_details']['size'] > 200000) {
            add_settings_error('volunteer_details', 'vd01', 'Keep volunteer details file under 200kb', 'error');
            return;
        }

        if ($_FILES['volunteer_details']['error'] === UPLOAD_ERR_NO_FILE) {

            add_settings_error('volunteer_details', 'vd02', 'No volunteer details provided', 'error');
            return;
        }


        if ($_FILES['volunteer_details']['size'] === 0) {
            add_settings_error('volunteer_details', 'vd03', 'Volunteer details file cannot be empty', 'error');
            return;
        }
        $filename = $_FILES['volunteer_details']['tmp_name'];
        // Check MIME Type



        if (mime_content_type($filename) !== 'text/plain') {
            add_settings_error('volunteer_details', 'vd04', 'Volunteer details file must be csv format', 'error');
            return;
        }
        // Get list of controls
        switch_to_blog(1);
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'post_type' => 'lel_control',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts = get_posts($args);
        $controls = [];
        foreach ($posts as $post) {
            $controls[$post->post_title] = $post->ID;
        };


        // Now process file

        $fd = fopen($filename, "r");
        // get csv headers
        $head = fgetcsv($fd, 1000);
        if ($head === FALSE) {
            add_settings_error('volunteer_details', 'vd05', 'Volunteer details file must be csv format', 'error');
            return;
        }
        if ($head[3] !== "Control 1") {
            add_settings_error('volunteer_details', 'vd06', 'Volunteer details file must be same format as exported file', 'error');
            return;
        }
        // now loop through the data
        while (($data = fgetcsv($fd)) !== FALSE) {

            $userdata = [
            email => $data[0],
            first_name => $data[1],
            last_name => $data[2],
            assigned_control_1 => $controls[$data[3]] ?? null,
            assigned_control_2 => $controls[$data[4]] ?? null,
            selected_control => $controls[$data[3]] ?? null,
            tshirt_size => $data[5],
            billing_phone => $data[6],
            gender => $data[7],
            billing_first_name => $data[1],
            billing_last_name => $data[2],
            billing_address_1 => $data[8],
            billing_address_2 => $data[9],
            billing_city => $data[10],
            billing_postcode => $data[11],
            billing_country => $data[12],
            emergency_contact => $data[13],
            emergency_phone => $data[14]
            ];

            $user = get_user_by('email', $userdata['email']);
            $user_id = $user ? $user->ID : null;

            if (!$user_id) {
                $user_id = wp_create_user($userdata['email'], wp_generate_password(12, true), $userdata['email']);
                $user = new WP_User($user_id);
                $user->set_role('volunteer');
            }


            if ($user_id) {

                foreach ($userdata as $key => $value) {
                    $value = sanitize_text_field($value);
                    update_user_meta($user_id, $key, $value);
                }
            }
        }
        fclose($fd);

        // Success
        add_settings_error('volunteer_details', 'vd07', 'Volunteer details successfully updated', 'updated');
    }

}
