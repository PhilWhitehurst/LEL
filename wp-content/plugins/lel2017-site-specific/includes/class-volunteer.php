<?php

/**
 * The volunteer functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 *

 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class LEL_Volunteer {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     *
     *
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->common = new LEL_Common($this->plugin_name, $this->version);
    }

    public function register_scripts() {


        wp_register_script('volunteer-templates', plugins_url('../js/underscore-templates/Volunteer.js', __FILE__), array('underscore'), '4.6', true);


        wp_register_script('set-my-control', plugins_url('../js/set-my-control.js', __FILE__), array('rest-component', 'flight-message', 'flight-info'), '', true);
        wp_register_script('volunteer-search', plugins_url('../js/search.js', __FILE__), array('rest-component', 'flight-hideshow', 'rider-list-component', 'flight-message', 'volunteer-templates'), '1.5', true);
        wp_register_script('bar-code', plugins_url('../js/bar-code.js', __FILE__), array('toggle-component', 'scan-component', 'rest-component'), '1.1', true);
        wp_register_script('volunteer-tracking-list', plugins_url('../js/volunteer-tracking-list.js', __FILE__), array('rest-component', 'rider-list-component', 'flight-message', 'volunteer-templates'), '', true);
        wp_register_script('volunteer-tracking-notes', plugins_url('../js/volunteer-tracking-notes.js', __FILE__), array('rest-component', 'rider-list-component', 'flight-message', 'volunteer-templates'), '1.5', true);
    }

    public function register_styles() {
        
    }

// Rest route definitions here

    public function rest_api_init() {
// My control updates by volunteer
        register_rest_route('lel/v1', '/mycontrol', [
            'methods' => 'POST',
            'callback' => [$this, 'UpdateMyControl'],
            'permission_callback' => function () {
        return current_user_can('access_volunteer_area');
    }
                ]
        );
// Search for riders
        register_rest_route('lel/v1', '/search/rider/(?P<rider_id>[\w]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'RiderSearch'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }]
        );
        register_rest_route('lel/v1', '/search/lastname/(?P<last_name>[\S]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'RiderSearch'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }]
        );
        register_rest_route('lel/v1', '/public/search/rider/(?P<rider_id>[\w]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'RiderSearch'],
            'permission_callback' => [$this, 'valid_nonce']
                ]
        );
        register_rest_route('lel/v1', '/public/search/lastname/(?P<last_name>[\S]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'RiderSearch'],
            'permission_callback' => [$this, 'valid_nonce']
                ]
        );



// Tracking
        register_rest_route('lel/v1', '/tracking/(?P<id>[\d]+)', [
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'TrackingDelete'],
                'permission_callback' => function () {

            if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
                return true;
            }
        }]
                ]
        );



        register_rest_route('lel/v1', '/tracking', [
            [
                'methods' => 'POST',
                'callback' => [$this, 'TrackingUpdate'],
                'permission_callback' => function () {

            if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
                return true;
            }
        }]
                ]
        );

        register_rest_route('lel/v1', '/tracking/notes', [
            [
                'methods' => 'PUT',
                'callback' => [$this, 'TrackingUpdateNotes'],
                'permission_callback' => function () {

            return current_user_can('access_tracking_area');
        }]
                ]
        );


        register_rest_route('lel/v1', '/tracking/rider/(?P<rider>[\w]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'TrackingRead'],
                'permission_callback' => function () {

            return current_user_can('access_tracking_area');
        }]
                ]
        );
        // Public route so we can rate limit
        register_rest_route('lel/v1', '/public/tracking/rider/(?P<rider>[\w]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'TrackingRead'],
                'permission_callback' => [$this, 'valid_nonce']
            ]
                ]
        );

        register_rest_route('lel/v1', '/tracking/notes/rider/(?P<rider>[\w]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'TrackingNotes'],
                'permission_callback' => function () {

            return current_user_can('access_tracking_area');
        }
            ]
                ]
        );

        register_rest_route('lel/v1', '/csv/volunteerspersonaldetails', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_volunteers_personal_details'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );
    }

    public function valid_nonce($request) {
        // Check Nonce
        $headers = $request->get_headers();
        $nonce = $headers['x_wp_nonce'][0];
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }
        return true;
    }

// Rest route callbacks here


    public function UpdateMyControl($request) {
        $params = $request->get_params();

        $selected_control = $params['selected-control'];
        $user_id = get_current_user_id();

        if (empty($selected_control) or ( $user_id === 0)) {
            $result = [
                'status' => 'error',
                'msg' => __('There was an error, please refresh your web page', 'LEL2017')
            ];
            return $result;
        }

        if (!update_user_meta($user_id, 'selected_control', $selected_control)) {
            $result = [
                'status' => 'error',
                'msg' => 'The control was not changed',
            ];
            return $result;
        };

        $control = $this->get_control($selected_control);

        $result = [
            'status' => 'success',
            'info' => $control,
            'msg' => __('You have successfully updated your selected control', 'LEL2017')
        ];

        return $result;
    }

    public function RiderSearch($request) {
        if (!current_user_can('manage_options')) {
            if (get_option('lock_public_tracking') === 'yes') {
                $result = [
                    status => 'error',
                    msg => 'Online tracking is now locked.'
                ];
                return $result;
            }
        }


        $params = $request->get_params();
        $searchkey = 'rider_id';
        if ($params['last_name']) {
            $searchkey = "last_name";
        }

        if (empty($params[$searchkey])) {
            $result = [
                status => 'error',
                msg => __('Please enter search criteria', 'LEL2017Plugin')
            ];
            return $result;
        }

        // $meta_value = str_replace('+', ' ', $params[$searchkey]);

        $meta_value = urldecode($params[$searchkey]);


        switch_to_blog(1);


        $args = [
            'role' => 'rider',
            'meta_key' => $searchkey,
            'meta_compare' => ' = ',
            'meta_value' => $meta_value,
            'offset' => 0,
            'number' => -1,
            'fields' => 'all'
        ];
        $users = get_users($args);

        restore_current_blog();
        /*
         * How process and return result
         */
        $riders = [];
        foreach ($users as $user) {
            if ($user->rider_id) {
                $fields = [
                    'rider_id' => $user->rider_id,
                    'first_name' => ucfirst(strtolower($user->first_name)),
                    'last_name' => ucfirst(strtolower($user->last_name)),
                    'country' => $this->common->get_country($user->billing_country),
                    'action' => $params['action'],
                    'confirm' => __('Confirm', 'LEL2017Plugin')
                ];
                $riders[] = $fields;
            }
        }


        $result = [
            status => 'success',
            msg => (count($riders) === 0 ? __('No matching riders found', 'LEL2017Plugin') : __('Matching riders found', 'LEL2017Plugin')),
            result => $riders
        ];

        return $result;
    }

    /*
     * Read tracking data for a rider
     */

    public function TrackingRead($request) {


        if (!current_user_can('manage_options')) {
            if (get_option('lock_public_tracking') === 'yes') {
                $result = [
                    status => 'error',
                    msg => 'Online tracking is now locked.'
                ];
                return $result;
            }
        }

// Get submitted params
        $params = $request->get_params();
        $rider_id = $params['rider'];

        if (empty($rider_id)) {
// Should never happen if submitted from genuine page
            $result = [
                status => 'error',
                msg => "There was an error, please refresh the page and try again."
            ];
            return $result;
        }
// Get internal user id from rider id
        switch_to_blog(1);
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        $users = get_users($args);
        restore_current_blog();

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => __("Rider number not recognised.", "LEL2017Plugin")
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];
// Get privacy settings for user
        $hide_tracking = [];
        if (!current_user_can('access_tracking_area')) {
            $show_checkin = get_user_meta($rider_user_id, 'show_checkin', true);
            $show_checkout = get_user_meta($rider_user_id, 'show_checkout', true);
            $show_bedbook = get_user_meta($rider_user_id, 'show_bedbook', true);
            if ($show_checkin === 'no') {
                $hide_tracking[] = 'Arrival';
            }
            if ($show_checkout === 'no') {
                $hide_tracking[] = 'Departure';
            }
            if ($show_bedbook === 'no') {
                $hide_tracking[] = 'Sleep Start';
                $hide_tracking[] = 'Sleep End';
            }
        }
// Get control names
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
            $controls[$post->ID] = $post->post_title;
        };

// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);
        usort($results, array($this, 'sort_by_timestamp'));
        // Now assemble tracking data to return;
        $tracking = [];



        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {

                $time_in_hand = intval($result['time_in_hand']);
                $sign = $time_in_hand < 0 ? -1 : +1;

                $tih_hh = abs(intval($time_in_hand / 3600)) * $sign;


                $tih_mm = intval((abs($time_in_hand) - abs($tih_hh) * 3600) / 60);
                $sign_fmt = $sign === -1 && $tih_hh === 0 ? '-' : '';
                $tih_fmt = $sign_fmt . $tih_hh . ' ' . __('hours', 'LEL2017Plugin') . ' ' . $tih_mm . ' ' . __('mins', 'LEL2017Plugin');



                $tmp = [
                    'timestamp' => $result['timestamp'],
                    'action' => __($result['action'], 'LEL2017Plugin'),
                    'control' => $controls[$result['control_id']],
                    'distance' => $result['distance'],
                    'time_in_hand' => $tih_fmt
                ];
                $tracking[] = $tmp;
            }
        };


        if (count($tracking) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin"),
                result => $tracking
            ];
            return $result;
        }


        $result = [
            status => 'success',
            msg => __("Tracking data found", "LEL2017Plugin"),
            result => $tracking
        ];


        return $result;
    }

    public function TrackingUpdate($request) {
        if (!current_user_can('manage_options')) {
            if (get_option('lock_tracking') === 'yes') {
                $result = [
                    status => 'error',
                    msg => 'Online tracking is now locked. No updates are allowed.'
                ];
                return $result;
            }
        }

// Check logged in and has a selected control
        $user_id = get_current_user_id();
        if ($user_id === 0) {

            $result = [
                status => 'error',
                msg => "There was an error please refresh the page and try again"
            ];
            return $result;
        }

// Get the selected control of the user that sent update request

        $selected_control = get_user_meta($user_id, 'selected_control', true);

        if (empty($selected_control)) {
            $result = [
                status => 'error',
                msg => "Please visit the My Control page to set the control you are working at."
            ];
            return $result;
        }

        $control_distances = $this->get_control_distances($selected_control);


// Get submitted params
        $params = $request->get_params();
        $rider_id = $params['rider'];

        $action = $params['action'];


        if (empty($rider_id) or empty($action)) {
// Should never happen if submitted from genuine page
            $result = [
                status => 'error',
                msg => "There was an error, please refresh the page and try again."
            ];
            return $result;
        }

        $rider_id = trim(strtoupper($rider_id));

// Get internal user id from rider id
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        $users = get_users($args);

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => "Rider number not recognised."
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];

// Now dealing with rider data

        $rider_start = $this->get_rider_start($rider_user_id);
// Should not be empty but just in case
        if (empty($rider_start)) {
            $rider_start = '2017-07-30 05:00';
        }
        $rider_limit = $this->get_rider_limit($rider_user_id);
// should not be empty but just in case
        if (empty($rider_limit)) {
            $rider_limit = '117:05';
        }

// Convert limit into hours and min in decimal form
        $tmp = explode(':', $rider_limit);
        $rider_tm_limit = (count($tmp) === 2 ? $tmp[0] + $tmp[1] / 60 : $tmp[0]);

// Set current timestamp

        $now = time() + 3600; // Convert to BST
        if (isset($params['timestamp'])) {
            $browser_timestamp = intval($params['timestamp']) + 3600;

            if ($browser_timestamp > 0 && $now - $browser_timestamp < 604800) {
                $now = $browser_timestamp;
            }
        }


        $now_formatted = date('Y-m-d H:i:s', $now);

        // Get latest tracking data
        $rider_tracking = $this->common->get_rider_tracking($rider_user_id);
// Check this is not a duplicate scan

        $last_scan = 0;
        if (count($rider_tracking) > 0) {
            $last = strtotime($rider_tracking[0]['timestamp']);
            // Scan may be delayed due to loss of Internet
            // so check if later scans already arrived.
            for ($i = 0; $i < count($rider_tracking); $i++) {
                $l = strtotime($rider_tracking[$i]['timestamp']);

                if ($l < $now && $rider_tracking[$i]['event'] !== 'Registration' && $rider_tracking[$i]['event'] !== 'Bag Drop Here'
                ) {
                    $last = $l;
                    $last_scan = $i;
                    break;
                }
            }


            if (abs($now - $last) < 15 && $rider_tracking[$i]["rider_id"] == $rider_user_id && $rider_tracking[0]["control_id"] == $selected_control && $action !== 'Bag-Drop') {
                $result = [
                    status => 'error',
                    help => $last . ' ' . $now,
                    msg => __('Duplicate scan, please wait 15 seconds and try again if not a duplicate.', 'LEL2017Plugin')
                ];
                return $result;
            }


            if ($rider_tracking[$last_scan]["action"] === "DNF" && $action !== 'Bag-Drop') {
// Should never happen if submitted from genuine page
                $result = [
                    status => 'error',
                    msg => "Rider is marked as Did Not Finish (DNF). Further scans for this rider are not permitted."
                ];
                return $result;
            }
        } else {
            if ($action !== 'Registration') {
                $result = [
                    status => 'error',
                    msg => "Rider has not registered. Scans for this rider are not permitted."
                ];
                return $result;
            }
        }

        $underway = 0;

        for ($i = 0; $i < count($rider_tracking); $i++) {
            if ($rider_tracking[$i]["action"] !== "Registration" && $rider_tracking[$i]["action"] !== "Bag Drop Here") {
                $underway = 1;
                break;
            }
        }



        if ($action === "Arrival-Departure" && $underway === 0) {

            $rider_action = "Entered Start Pen";
        } else {

            $rider_action = $this->determine_rider_action($rider_tracking, $action, $selected_control, $last_scan);
        }



        if ($action === 'Registration' | $action === 'Bag-Drop') {
            $rider_distance = 0;
            $rider_direction = '';
            $time_in_hand = 0;
            $average_speed = 0;
            $elapsed = 0;
            if (count($rider_tracking) > 0) {
                // Check not already registered
                if ($action === 'Registration') {
                    foreach ($rider_tracking as $tracking) {
                        if ($tracking["action"] === 'Registration') {
                            $result = [
                                status => 'error',
                                msg => "Rider has already been registered."
                            ];
                            return $result;
                        }
                    }
                }
            }
        } else {


            $rider_distance = $this->determine_latest_distance($rider_tracking, $control_distances, $last_scan);

            $rider_direction = ($rider_distance == $control_distances['southbound'] ? 'South' : 'North');
            $event_distance = $this->common->get_event_distance();

// Rider min speed in kmh
            $rider_min_speed = $event_distance / $rider_tm_limit;
// Rider max time to this control (in seconds)
            $rider_max_time = ($rider_distance / $rider_min_speed) * 3600;

// Work out rider elapsed time (in seconds)
            $start = strtotime($rider_start);
            $elapsed = $now - $start;
// Calculate average speed
            $average_speed = round($rider_distance / $elapsed * 3600, 2);

            if ($average_speed > 50 && $action !== "DNF") {
                $result = [
                    status => 'error',
                    msg => "Overall average exceeds 50km/h. Scan has been rejected as invalid."
                ];
                return $result;
            }

// Calculate average speed for leg if relevant
            if (count($rider_tracking) > 0 and action !== "DNF") {
                if ($rider_tracking[$last_scan]['distance'] < $rider_distance && $rider_tracking[$last_scan]['action'] !== 'Bag Drop Here') {
                    $elapsed_leg = $now - $last;
                    $distance_leg = $rider_distance - $rider_tracking[$last_scan]['distance'];
                    $average_speed_leg = round($distance_leg / $elapsed_leg * 3600, 2);
                }
            }
// Calculate time in hand (seconds)
            $time_in_hand = floor($rider_max_time - $elapsed);
        }
// Create tracking record
        global $wpdb;


        $data = [
            timestamp => $now_formatted,
            user_id => $user_id,
            rider_id => $rider_user_id,
            control_id => $selected_control,
            action => $rider_action,
            time_in_hand => $time_in_hand,
            average_speed => $average_speed,
            average_speed_leg => $average_speed_leg,
            distance => $rider_distance,
            elapsed => $elapsed,
            direction => $rider_direction
        ];

        // Get IP address that request was submitted from

        $headers = $request->get_headers();
        $ip = $headers['cf_connecting_ip'][0];
        $ip_binary = inet_pton($ip);


        if ($ip_binary) {
            $data['IP'] = bin2hex($ip_binary);
        }

// Insert tracking record
        $table_name = $wpdb->prefix . "tracking";

        $format = [
            '%s',
            '%d',
            '%d',
            '%d',
            '%s',
            '%f',
            '%f',
            '%f',
            '%f',
            '%s',
            '%s'
        ];

        if (!$wpdb->insert($table_name, $data, $format)) {
            $result = [
                status => 'error',
                msg => __('There was an error please refresh screen', 'LEL2017Plugin')
            ];
            return $result;
        }


        /*
         * Retrieve details for rider tracking list
         */
        $first_name = ucfirst(strtolower(get_user_meta($rider_user_id, 'first_name', true)));
        $last_name = ucfirst(strtolower(get_user_meta($rider_user_id, 'last_name', true)));

        $billing_country = get_user_meta($rider_user_id, 'billing_country', true);
        $country = $this->common->get_country($billing_country);
        $elapsed_hh = floor($elapsed / 3600);
        $elapsed_mm = floor(($elapsed - $elapsed_hh * 3600) / 60);
        $elapsed_fmt = $elapsed_hh . ' hours ' . $elapsed_mm . ' mins';
        $sign = $time_in_hand < 0 ? -1 : +1;
        $tih_hh = abs(intval($time_in_hand / 3600)) * $sign;
        $tih_mm = intval((abs($time_in_hand) - abs($tih_hh) * 3600) / 60);

        $sign_fmt = $sign === -1 && $tih_hh === 0 ? '-' : '';
        $tih_fmt = $sign_fmt . $tih_hh . ' hours ' . $tih_mm . ' mins';

        date_default_timezone_set('Europe/London');
        $timestamp = date('D d M H:i', $now - 3600);
        $result = [
            status => 'success',
            msg => __('Rider successfully scanned', 'LEL2017Plugin'),
            result => [
                [timestamp => $timestamp,
                    action => $rider_action,
                    ID => $wpdb->insert_id,
                    rider_id => $rider_id,
                    first_name => $first_name,
                    last_name => $last_name,
                    country => $country,
                    elapsed => $elapsed_fmt,
                    time_in_hand => $tih_fmt,
                    direction => $rider_direction
                ]
            ]
        ];

        /*
         * If Start Pen then generate a start record with their start
         * time
         */
        if ($rider_action === 'Entered Start Pen') {
            $average_speed = ($time_limit === 100 ? 25 : 22);
            $data = [
                timestamp => $rider_start,
                user_id => $user_id,
                rider_id => $rider_user_id,
                control_id => $selected_control,
                action => 'Departure',
                time_in_hand => 0,
                average_speed => $average_speed,
                average_speed_leg => 0,
                distance => $rider_distance,
                elapsed => $elapsed,
                direction => 'North',
                IP => bin2hex($ip_binary)
            ];

            $wpdb->insert($table_name, $data, $format);
        }

        // Clear the cache of tracking data for this rider
        wp_cache_delete('tracking' . $rider_user_id, 'Common');


        return $result;
    }

    public function TrackingUpdateNotes($request) {

        if (get_option('lock_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked. No updates are allowed.'
            ];
            return $result;
        }


// Get submitted params
        $params = $request->get_params();
        $rider_user_id = $params['rider'];
        $notes = $params['notes'];

        if (empty($rider_user_id) | !isset($notes)) {
// Should never happen if submitted from genuine page
            $result = [
                status => 'error',
                msg => "There was an error, please refresh the page and try again."
            ];
            return $result;
        }

        if (empty($notes)) {
            $result = [
                status => 'error',
                msg => "No new notes to add"
            ];
            return $result;
        }

        // Update notes on rider
        $user_id = get_current_user_id();
        if ($user_id === 0) {

            $result = [
                status => 'error',
                msg => "There was an error please refresh the page and try again"
            ];
            return $result;
        }

// Get the selected control of the user that sent update request

        $first_name = get_user_meta($user_id, 'first_name', true);
        $last_name = get_user_meta($user_id, 'last_name', true);

        $timestamp = "(Updated " . date('Y-m-d H:i:s', time() + 3600) . " by "
                . $first_name
                . " "
                . $last_name
                . ")\r\n";

        $old_notes = get_user_meta($rider_user_id, 'notes', true);
        $new_notes = $notes . "\r\n" . $timestamp . $old_notes;
        update_user_meta($rider_user_id, 'notes', $new_notes);

        // Get rider notes
        $notes = nl2br(get_user_meta($rider_user_id, 'notes', true));

        $first_name = get_user_meta($rider_user_id, 'first_name', true);
        $last_name = get_user_meta($rider_user_id, 'last_name', true);
        $phone = get_user_meta($rider_user_id, 'billing_phone', true);
        $country = $this->common->get_country(get_user_meta($rider_user_id, 'billing_country', true));

        $emergency_contact = get_user_meta($rider_user_id, 'emergency_contact', true);
        $emergency_phone = get_user_meta($rider_user_id, 'emergency_phone', true);
        $vegan = get_user_meta($rider_user_id, 'vegan', true);
        $vegetarian = get_user_meta($rider_user_id, 'vegetarian', true);
        $nut = get_user_meta($rider_user_id, 'nut', true);
        $gluten = get_user_meta($rider_user_id, 'gluten', true);
        $lactose = get_user_meta($rider_user_id, 'lactose', true);


        $rider_notes = [
            rider_user_id => $rider_user_id,
            notes => $notes,
            first_name => $first_name,
            last_name => $last_name,
            phone => $phone,
            country => $country,
            emergency_contact => $emergency_contact,
            emergency_phone => $emergency_phone,
            food_choices => ($vegan === 'yes' ? 'vegan, ' : '')
            . ($vegetarian === 'yes' ? 'vegetarian, ' : '')
            . ($nut === 'yes' ? 'nut allergy, ' : '')
            . ($gluten === 'yes' ? 'gluten intolerant, ' : '')
            . ($lactose === 'yes' ? 'lactose intolerant, ' : '')
        ];


        $result = [
            status => 'success',
            notes => $rider_notes,
            msg => __('Rider notes successfully updated', 'LEL2017Plugin'),
        ];

        return $result;
    }

// Delete a tracking entry
    public function TrackingDelete($request) {

        if (!current_user_can('manage_options')) {
            if (get_option('lock_tracking') === 'yes') {
                $result = [
                    status => 'error',
                    msg => 'Online tracking is now locked. No updates are allowed.'
                ];
                return $result;
            }
        }
// Check logged in and has a selected control
        $user_id = get_current_user_id();
        if ($user_id === 0) {

            $result = [
                status => 'error',
                msg => "There was an error please refresh the page and try again"
            ];
            return $result;
        }

// Get the selected control of the user that sent update request

        $selected_control = get_user_meta($user_id, 'selected_control', true);

        if (empty($selected_control)) {
            $result = [
                status => 'error',
                msg => "Please visit the My Control page to set the control you are working at."
            ];
            return $result;
        }



// Get submitted params
        $params = $request->get_params();

        $id = $params['id'];

// Now get tracking record
        global $wpdb;
        $table_name = $wpdb->prefix . 'tracking';
        $sql = 'SELECT * FROM ' . $table_name . ' WHERE id = %d';
        $sql = $wpdb->prepare($sql, $id);

        $tracking = $wpdb->get_row($sql);

        if (!current_user_can('manage_options')) {

            if ($tracking->control_id != $selected_control) {
                $result = [
                    status => 'error',
                    msg => "You may only delete tracking records for the control you are working at.",
                ];
                return $result;
            }
        }


        if (!is_null($tracking->rider_id)) {
// tracking record exists
            wp_cache_delete('tracking' . $tracking->rider_id, 'Common');
// Now delete tracking record
            $delete = $wpdb->delete($table_name, ['id' => $id], ['%d']);
            if (!$delete) {
                $result = [
                    status => 'error',
                    msg => 'An error occured when trying to delete entry'
                ];
            } else {
                // Clear the cache of tracking data for this rider
                wp_cache_delete('tracking' . $tracking->rider_id, 'Common');
                $result = [
                    status => 'success',
                    msg => 'Success - entry has been deleted',
                    id => $id
                ];
            }
        } else {
            $result = [
                status => 'error',
                msg => 'An error occured, entry not found in tracking database'
            ];
        }

        return $result;
    }

    /*
     * Read tracking data for a rider
     */

    public function TrackingNotes($request) {

// Get submitted params
        $params = $request->get_params();
        $rider_id = $params['rider'];

        if (empty($rider_id)) {
// Should never happen if submitted from genuine page
            $result = [
                status => 'error',
                msg => "There was an error, please refresh the page and try again."
            ];
            return $result;
        }
// Get internal user id from rider id
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        $users = get_users($args);

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => "Rider number not recognised."
            ];
            return $result;
        }



        $rider_user_id = (int) $users[0];
        // Get rider notes
        $notes = nl2br(get_user_meta($rider_user_id, 'notes', true));

        $first_name = get_user_meta($rider_user_id, 'first_name', true);
        $last_name = get_user_meta($rider_user_id, 'last_name', true);
        $phone = get_user_meta($rider_user_id, 'billing_phone', true);
        $country = $this->common->get_country(get_user_meta($rider_user_id, 'billing_country', true));


        $emergency_contact = get_user_meta($rider_user_id, 'emergency_contact', true);
        $emergency_phone = get_user_meta($rider_user_id, 'emergency_phone', true);
        $vegan = get_user_meta($rider_user_id, 'vegan', true);
        $vegetarian = get_user_meta($rider_user_id, 'vegetarian', true);
        $nut = get_user_meta($rider_user_id, 'nut', true);
        $gluten = get_user_meta($rider_user_id, 'gluten', true);
        $lactose = get_user_meta($rider_user_id, 'lactose', true);


        $rider_notes = [
            rider_user_id => $rider_user_id,
            notes => $notes,
            first_name => $first_name,
            last_name => $last_name,
            phone => $phone,
            country => $country,
            emergency_contact => $emergency_contact,
            emergency_phone => $emergency_phone,
            food_choices => ($vegan === 'yes' ? 'vegan, ' : '')
            . ($vegetarian === 'yes' ? 'vegetarian, ' : '')
            . ($nut === 'yes' ? 'nut allergy, ' : '')
            . ($gluten === 'yes' ? 'gluten intolerant, ' : '')
            . ($lactose === 'yes' ? 'lactose intolerant, ' : '')
        ];

// Get control names
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
            $controls[$post->ID] = $post->post_title;
        };

// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);

        usort($results, array($this, 'sort_by_timestamp'));

        // Now assemble tracking data to return;
        $tracking = [];
        foreach ($results as $result) {


            $time_in_hand = intval($result['time_in_hand']);
            $sign = $time_in_hand < 0 ? -1 : +1;

            $tih_hh = abs(intval($time_in_hand / 3600)) * $sign;


            $tih_mm = intval((abs($time_in_hand) - abs($tih_hh) * 3600) / 60);
            $sign_fmt = $sign === -1 && $tih_hh === 0 ? '-' : '';
            $tih_fmt = $sign_fmt . $tih_hh . ' hours ' . $tih_mm . ' mins';



            $tmp = [
                'timestamp' => $result['timestamp'],
                'action' => $result['action'],
                'control' => $controls[$result['control_id']],
                'distance' => $result['distance'],
                'time_in_hand' => $tih_fmt,
                'direction' => $result['direction'],
                'ID' => $result['id']
            ];
            $tracking[] = $tmp;
        };


        $result = [
            status => 'success',
            msg => "Tracking data found",
            result => $tracking,
            notes => $rider_notes
        ];


        return $result;
    }

//Shortcodes here

    public function set_my_control() {
// Check user is authorised to see this content
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return '<div class="alert alert-danger">You must be logged in to access this content</div>';
        }
        if (!current_user_can('access_volunteer_area')) {
            return '<div class="alert alert-danger">You do not have authority to access this content</div>';
        }
// OK they are authorised, now generate output
        $out = '';

        $id_1 = get_user_meta($user_id, 'assigned_control_1', true);
        $id_2 = get_user_meta($user_id, 'assigned_control_2', true);


        $assigned_control_1 = ($id_1 > 0 ? get_the_title($id_1) : '');
        $assigned_control_2 = ($id_2 > 0 ? get_the_title($id_2) : '');

        if ($assigned_control_1 === '' && $assigned_control_2 === '') {
            $out .= "<h4>You do not have any assigned controls</h4>";
        } else {
            $out .= "<h4>Your assigned control(s) are</h4>"
                    . "<ul>"
                    . ($assigned_control_1 !== '' ? "<li>" . $assigned_control_1 . "</li>" : '' )
                    . ($assigned_control_2 !== '' ? "<li>" . $assigned_control_2 . "</li>" : '' )
                    . "</ul>";
        }


        $out .= "<h4>You are currently working at control</h4>";

// Now display current selected control (if set)
        $selected_control = (int) get_user_meta($user_id, 'selected_control', true);



        if (empty($selected_control)) {
            $result = '<div class="alert alert-danger" id="my-control-info">'
                    . 'You do not have a currently selected control.'
                    . ' Please (carefully) select the control you are working at and click confirm'
                    . '</div>';
            $out .= $result;
        } else {
            $control = $this->get_control($selected_control);
            $out .= '<div class="alert alert-info" id="my-control-info">'
                    . $control
                    . '</div>';
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
        if (count($posts) > 0) {
            $out .= '<form id="set-my-control">';
            $out .= '<select name="selected-control" class="form-control">';


            foreach ($posts as $post) {
                $ID = $post->ID;
                $title = $post->post_title;
                $out .= '<option value="' . $ID . '"';
                if ($selected_control === $ID) {
                    $out .= ' selected';
                }
                $out .= '>' . $title . '</option>';
            }
            $out .= '</select>';
            $out .= '<br>';
            $out .= '<div class="lel-message" ></div>';
            $out .= '<button type="submit" class="btn btn-primary">Confirm</button><br><br>';
            $out .= '</form>';
        }

        $X_WP_Nonce = wp_create_nonce('wp_rest');
        wp_localize_script('set-my-control', 'wp_var', array(
            'resturl' => get_rest_url() . 'lel/v1/',
            'method' => 'POST',
            'X_WP_Nonce' => $X_WP_Nonce,
        ));

        wp_enqueue_script('set-my-control');


        return $out;
    }

    public function get_my_control() {
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return '<div class="alert alert-danger">You must be logged in to access this content</div>';
        }
        if (!current_user_can('access_volunteer_area') && !current_user_can('access_tracking_area') && !current_user_can('access_registration_area')) {

            return '<div class="alert alert-danger">You do not have authority to access this content</div>';
        }
        $selected_control = get_user_meta($user_id, 'selected_control', true);
        if (empty($selected_control)) {
            $result = '<div class="alert alert-danger" id="my-control-info">'
                    . 'You do not have a currently selected control.'
                    . ' Please visit my control page and set your control'
                    . '</div>';
            return $result;
        }

        $result = '<div class="alert alert-info" id="my-control-info">'
                . $this->get_control($selected_control, false)
                . '</div>';
        return $result;
    }

    public function bar_code($atts) {

        $args = shortcode_atts(array(
            'action' => 'arrival-departure',
            'trigger_event' => ''
                )
                , $atts);

        $action = $args['action'];
        $trigger_event = $args['trigger_event'];

        $X_WP_Nonce = wp_create_nonce('wp_rest');
        wp_localize_script('bar-code', 'wp_var', array(
            'resturl' => get_rest_url() . 'lel/v1/',
            'method' => 'POST',
            'X_WP_Nonce' => $X_WP_Nonce,
            'trigger_event' => $trigger_event
        ));

        wp_enqueue_script('bar-code');


        $out = '<form id="bar-code" >'
                . '<input type="hidden" value="' . $action . '" name="action">'
                . '<div class="input-group">'
                . '<input type="text" name="rider" placeholder="Barcode scan  (Do not type in this field)" class="form-control" size="10" autofocus> '
                . '<span class="input-group-btn">&nbsp;'
                . '<button class="btn btn-standard" type="button">Manual Entry</button>'
                . '</span>'
                . '</div>'
                . '</form>';

        return $out;
    }

    public function search_rider($atts) {

        $args = shortcode_atts(array(
            'hidden' => 'no',
            'action' => 'arrival-departure',
            'rest_method' => 'POST',
            'rest_path' => 'tracking/',
            'public' => 'no'
                )
                , $atts);

        $hidden = $args['hidden'];
        $action = $args['action'];
        $rest_method = $args['rest_method'];
        $rest_path = $args['rest_path'];
        $public = $args['public'];

        $X_WP_Nonce = wp_create_nonce('wp_rest');
        wp_localize_script('volunteer-search', 'wp_var', array(
            'resturl' => get_rest_url() . 'lel/v1/',
            'method' => 'GET',
            'rest_method' => $rest_method,
            'rest_path' => $rest_path,
            'X_WP_Nonce' => $X_WP_Nonce,
            'public' => $public,
            'security_token' => __('Security token expired. Please refresh the page.', 'LEL2017Plugin'),
            'tracking_rate' => __('Please slow down rate of tracking requests or you will be banned for a period', 'LEL2017Plugin')
        ));

        wp_enqueue_script('volunteer-search');

        $hide_class = ($hidden === 'yes' ? ' class="hidden"' : '');


        $out = '<div id="rider-search"'
                . $hide_class
                . '>'
                . '<form id="riderid-search" >'
                . '<div class="input-group">'
                . '<input type="text" name="rider" placeholder="'
                . __('Enter rider ID, for example A35', 'LEL2017Plugin')
                . '" class="form-control" size="35"> '
                . '<input type="hidden" value="' . $action . '" name="action">'
                . '<span class="input-group-btn">&nbsp;'
                . '<input type="submit" value="'
                . __('Find', 'LEL2017Plugin')
                . '" class="btn btn-standard">'
                . '</span>'
                . '</div>'
                . '</form>'
                . '<br>'
                . '<form id="lastname-search" >'
                . '<div class="input-group">'
                . '<input type="text" name="last_name" placeholder="'
                . __('Enter last name', 'LEL2017Plugin')
                . '" class="form-control"> '
                . '<input type="hidden" value="' . $action . '" name="action">'
                . '<span class="input-group-btn">&nbsp;'
                . '<input type="submit" value="'
                . __('Find', 'LEL2017Plugin')
                . '" class="btn btn-standard">'
                . '</span>'
                . '</div>'
                . '</form>'
                . '<br>'
                . '</div>'
                . '<div id="search-results"></div>'
                . '<div id="lel-search-message" ></div>';


        return $out;
    }

    public function checkin_checkout() {
        $args = shortcode_atts(array(
            'id' => '#my-control-info',
                )
                , $atts);

        $id = $args['id'];


        wp_localize_script('checkin-checkout', 'wp_var', array(
            'id' => $id
        ));

        wp_enqueue_script('checkin-checkout');
    }

    public function register_custom_posttypes() {
        register_post_type('lel_control', [
            'labels' => [
                'name' => __('Controls'),
                'singular_name' => __('Control'),
            ],
            'public' => true,
            'has_archive' => true,
                ]
        );
    }

// Internal functions here

    private function get_control($id, $full = true) {
        $control = get_post($id);
        $content = get_field('address');

        $result = "<h4>" . $control->post_title . "</h4>";
        if ($full) {
            $result .= $content;
        }


        return $result;
    }

    private function get_control_distances($id) {
        $control = get_fields($id);

        if (empty($control)) {
            return [];
        }

        $distance_northbound = $control['distance_northbound'];
        $distance_southbound = $control['distance_southbound'];

        $result = [
            northbound => $distance_northbound,
            southbound => $distance_southbound
        ];


        return $result;
    }

    private function get_rider_start($rider) {
        $designated_start = get_user_meta($rider, 'designated_start', true);

        return $designated_start;
    }

    private function get_rider_limit($rider) {
        $time_limit = get_user_meta($rider, 'designated_limit', true);


        return $time_limit;
    }

    private function getRiderWaves($array = false) {
// userid will be 0 is not signed in

        $result = wp_cache_get('riderStartWaves', 'Rider');

        if (false !== $result) {
            if ($array) {
                return $result;
            }
            wp_send_json($result);
        }

        global $wpdb;
        $result = [];
        $table_name = $wpdb->prefix . "startwaves";

        $sql = "SELECT * FROM " . $table_name;

        $rows = $wpdb->get_results($sql);

        foreach ($rows as $row) {

            $result[] = [
                id => esc_html($row->id),
                start_time => esc_html($row->start_time),
                description => esc_html($row->description),
                time_limit => esc_html($row->time_limit),
                requested_places => esc_html($row->requested_places),
                total_places => esc_html($row->total_places)
            ];
        }
// Cache the result for 1 hour
        wp_cache_set('riderStartWaves', $result, 'Rider', 3600);


        if ($array) {
            return $result;
        }
        wp_send_json($result);
    }

    public function get_distance() {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'properties',
            'meta_key' => 'development',
            'meta_value' => $development_id,
        );
        $properties_query = new WP_Query($args);
        $prices = array();

        if ($properties_query->have_posts()):
            while ($properties_query->have_posts()) : $properties_query->the_post();
                $price = get_field('price');
                if (isset($price) && !empty($price)) {
                    $prices[] = $price;
                }
            endwhile;
            $max_price = max($prices);
            $min_price = min($prices);

        endif;
        wp_reset_query();
    }

    /*
     * Restrict access to volunteer pages
     */

    public function auth_redirect() {

// Base pages for volunteer
        $volunteerPages = [
            'volunteer-my-details',
            'volunteer-merchandise',
            'volunteer-my-control',
        ];
        $slug = the_slug();


        if (in_array($slug, $volunteerPages) && !current_user_can('access_volunteer_area')) {
            wp_logout();
            auth_redirect();
        }
// Tracking pages used by volunteers
        $trackingPages = [
            'volunteer-arrival-departure',
            'volunteer-sleep',
            'volunteer-dnf',
            'volunteer-bag-drop',
            'volunteer-control-dashboard',
            'volunteer-event-dashboard',
            'volunteer-rider-dashboard'
        ];

        if (in_array($slug, $trackingPages) && !current_user_can('access_tracking_area')) {
            wp_logout();
            auth_redirect();
        }

        // Registration pages used by volunteers
        $registrationPages = [
            'volunteer-registration',
        ];

        if (in_array($slug, $registrationPages) && !current_user_can('access_registration_area')) {
            wp_logout();
            auth_redirect();
        }
    }

    public function determine_latest_distance($rider_tracking, $control_distances, $last_scan) {
// No tracking data yet
        if (count($rider_tracking) === 0) {
            return min($control_distances);
        }
        /*
         * Tracking either indicates rider is returning to control having
         * been further north or this is first visit
         */
        $i = $last_scan;
        while ($rider_tracking[$i]['action'] === 'Registration' or
        $rider_tracking[$i]['action'] === 'Bag Drop Here'
        ) {
            $i++;
            if ($i > count($rider_tracking) - 1) {
                break;
            }
        }

        if ($i > count($rider_tracking) - 1) {
            return min($control_distances);
        }
        if ($rider_tracking[$i]['distance'] > min($control_distances)) {
// Second visit
            return max($control_distances);
        } else {
// First visit
            return min($control_distances);
        }
    }

    /*
     * Determine rider action based on previous action
     */

    public function determine_rider_action($rider_tracking, $action, $selected_control, $last_scan) {
// Valid actions
        $actions = [
            'Registration' => ['Registration', 'Registration'],
            'Arrival-Departure' => ['Arrival', 'Departure'],
            'Sleep' => ['Sleep Start', 'Sleep End'],
            'DNF' => ['DNF', 'DNF'],
            'Bag-Drop' => ['Bag Drop Here', 'Bag Drop Here']
        ];
// Action not recognised

        if (!in_array($action, ['Registration', 'Arrival-Departure', 'Sleep', 'DNF', 'Bag-Drop'])) {
            return 'Unknown';
        }
        // Simple Registration
        if ($action === "Registration") {
            return $actions['Registration'][0];
        }
        // Simple Bag Drop
        if ($action === "Bag-Drop") {
            return $actions['Bag-Drop'][0];
        }
// Simple DNF
        if ($action === "DNF") {
            return $actions['DNF'][0];
        }
// No tracking data yet so return first action
        if (count($rider_tracking) === 0) {
            return $actions[$action][0];
        }
// Last action was at this control so lets see what they have
// done

        $i = $last_scan;
        while ($rider_tracking[$i]['action'] === 'Registration' or
        $rider_tracking[$i]['action'] === 'Bag Drop Here'
        ) {
            $i++;
            if ($i > count($rider_tracking) - 1) {
                break;
            }
        }

        if ($i > count($rider_tracking) - 1) {
            return $actions[$action][0];
        }

        $last_scan = $i; // Last scan not including registration or bag drop



        if ($rider_tracking[$last_scan]['control_id'] == $selected_control) {
            $choice = 0;
            $i = $last_scan;
            $prev_actions = [];
            while ($i < count($rider_tracking) && $rider_tracking[$i]['control_id'] == $selected_control) {
                $prev_actions[] = $rider_tracking[$i]['action'];
                $i++;
            }
// Oldest actions will be at end of array
// we work from newest to the oldest determining what the next
// action should be


            $k = count($prev_actions) - 1;
            $match = false;
            for ($j = 0; $j <= $k; $j++) {
                if ($action === 'Arrival-Departure') {

                    switch ($prev_actions[$j]) {
                        case 'Arrival':
                            $result = 'Departure';
                            $match = true;
                            break;
                        case 'Departure':
                            $result = 'Arrival';
                            $match = true;
                            break;
                    }
                }
                if ($action === 'Sleep') {
                    switch ($prev_actions[$j]) {
                        case 'Sleep Start':
                            $result = 'Sleep End';
                            $match = true;
                            break;
                        case 'Sleep End':
                            $result = 'Sleep Start';
                            $match = true;
                            break;
                    }
                }


                if ($match === true) {
                    break;
                }
            }
        }
        if (empty($result)) {
            $result = $actions[$action][0];
        }

        return $result;
    }

    private function sort_by_timestamp($a, $b) {

        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
    }

    /*
     * Export volunteers and their personal details
     */

    function csv_export_volunteers_personal_details() {

        $filename = 'lel-volunteer-personal-details-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'Email',
            'First Name',
            'Last Name',
            'Control 1',
            'Control 2',
            'Tshirt Size',
            'Phone',
            'Gender',
            'Address Line 1',
            'Address Line 2',
            'City',
            'Postcode',
            'Country',
            'Emergency Contact',
            'Emergency Phone'
        ];


        $fh = @fopen('php://output', 'w');
//fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fh, $header_row);



        switch_to_blog(1);

// Arguments to retrieve Volunteers

        $args = [
            'role' => 'Volunteer'
        ];
// Retrieve riders
        $volunteers = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($volunteers->results as $volunteer) {

            $control_1 = $volunteer->assigned_control_1 ? get_the_title($volunteer->assigned_control_1) : 'None';
            $control_2 = $volunteer->assigned_control_2 ? get_the_title($volunteer->assigned_control_2) : 'None';


            $row = [
            $volunteer->user_email,
            $volunteer->first_name,
            $volunteer->last_name,
            $control_1,
            $control_2,
            $volunteer->tshirt_size ?? 'None',
            "'" . $volunteer->billing_phone,
            $volunteer->gender,
            $volunteer->billing_address_1,
            $volunteer->billing_address_2,
            $volunteer->billing_city,
            $volunteer->billing_postcode,
            $volunteer->billing_country,
            $volunteer->emergency_contact,
            "'" . $volunteer->emergency_phone
            ];

            $data_rows[] = $row;
        }



        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    private function translations() {
        // Just so we can add the translations for the actions
        // function not to be called.
        $t = __('Registration', 'LEL2017Plugin');
        $t = __('Entered Start Pen', 'LEL2017Plugin');
        $t = __('Arrival', 'LEL2017Plugin');
        $t = __('Departure', 'LEL2017Plugin');
        $t = __('Sleep Start', 'LEL2017Plugin');
        $t = __('Sleep End', 'LEL2017Plugin');
        $t = __('Bag Drop Arrival', 'LEL2017Plugin');
        $t = __('Bag Drop Departure', 'LEL2017Plugin');
    }

}
