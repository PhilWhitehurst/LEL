<?php

/**
 * The rider specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_Rider {

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
        $this->cache = 10; // default number of seconds to cache for

        $this->common = new LEL_Common($this->plugin_name, $this->version);
    }

    public function auth_redirect() {
        $riderPages = [
            'rider-my-details',
            'rider-start-time',
            'rider-bag-drop',
            'rider-merchandise'
        ];
        $slug = the_slug();
        if (in_array($slug, $riderPages) && !current_user_can('access_rider_area')) {

            wp_logout();
            auth_redirect();
        }
    }

    public function bag_drops() {
        $user_id = get_current_user_id();
        if ($user_id != 0) {


            add_action('wp_enqueue_scripts', wp_enqueue_script('lel-update-bag-drops'));
            wp_localize_script('lel-update-bag-drops', 'wp', array(
// URL to wp-admin/admin-ajax.php to process the request
                'ajaxurl' => admin_url('admin-ajax.php'),
                'flight_path' => plugin_dir_url(__FILE__) . 'lel-rider/js/flight-components/'
                    )
            );
            $user_info = get_userdata($user_id);
// Choices locked?
            $output = '';
            if (get_option('lock_bag_drops') === 'yes') {
                $output .= '<p><strong>' . __('Your bag drop choices are now locked', 'LEL2017') . '</strong></p>';
            }
// get choices
            $bag_drop_1 = (int) $user_info->bag_drop_1;
            $bag_drop_2 = (int) $user_info->bag_drop_2;
            $bag_drops = $this->common->bagDropLocations(true);
            $output .= '<div type="info-bagdrop" class="alert alert-info">';
            $bag_text = '';
            If ($bag_drop_1 > 1) {
                $output .= __('You have chosen', 'LEL2017Plugin') . ' ';

                foreach ($bag_drops as $bag_drop) {
                    if ($bag_drop["ID"] === $bag_drop_1) {

                        $bag_text_1 .= $bag_drop["post_title"];
                    }
                    if ($bag_drop["ID"] === $bag_drop_2 and $bag_drop_2 > 1) {

                        $bag_and .= ' ' . __('and', 'LEL2017Plugin') . ' ';

                        $bag_text_2 .= $bag_drop["post_title"];
                    }
                }
                $bag_text = $bag_text_1 . $bag_and . $bag_text_2;
            }
            if (empty($bag_text)) {
                $bag_text = __('You have chosen no bag drops', 'LEL2017Plugin');
            }



            $output .= $bag_text . '</div>';
            if (get_option('lock_bag_drops') !== 'yes') {

                $output .= '<form action="updateBagDrops">';
                $output .= '<label for="BagDrop1">' . __('Bag Drop', 'LEL2017Plugin') . ' 1:</label> ';
// Create a dropdown
                $output .= '<select class="form-control" name="BagDrop1">';


                foreach ($bag_drops as $bag_drop) {
                    $output .= '<option value="' . $bag_drop["ID"] . '"' . ($bag_drop["ID"] === $bag_drop_1 ? ' selected ' : '') . '>' . $bag_drop["post_title"] . '</option>';
                }
                $output .= '</select>';
                $output .= '<label for="BagDrop2">' . __('Bag Drop', 'LEL2017Plugin') . ' 2:</label> ';

// Create a dropdown
                $output .= '<select class="form-control" name="BagDrop2">';

                foreach ($bag_drops as $bag_drop) {
                    $output .= '<option value="' . $bag_drop["ID"] . '"' . ($bag_drop["ID"] === $bag_drop_2 ? ' selected ' : '') . '>' . $bag_drop["post_title"] . '</option>';
                }
                $output .= '</select><br>';

                $output .= wp_nonce_field('update_bag_drops', '_verify');


                $output .= '<input type="submit" class="btn btn-primary" value="' . __('Update', 'LEL2017Plugin') . '">';
                $output .= '</form><br>';
                $output .= '<div type="message-bagdrop" ></div>';
            }
            return $output;
        }
    }

    public function start_waves() {
        /*
         * Function for outputting either designated start and wave or forms
         * for user to update their chosen wave
         */
// Must be logged in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return '';
        }
// Scripts for page
        add_action('wp_enqueue_scripts', wp_enqueue_script('lel-rider-wave'));
        wp_localize_script('lel-rider-wave', 'wp', array(
// URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url('admin-ajax.php'),
                )
        );
// Get designated start and chosen wave (if set)
        $user_info = get_userdata($user_id);
        $start_locked = get_option('lock_start_times');
        $designated_start = esc_html($user_info->designated_start);
        $time_limit = esc_html($user_info->designated_limit);
        $rider_id = esc_html($user_info->rider_id);


        $chosen_wave = intval($user_info->chosen_wave);


// Generate output
        $output = '<div id="lel-wave-div">';
// output designated start if set

        if (!empty($designated_start)) {
            $output .= '<div id="lel-extra" class="alert alert-info">';
            $date = DateTime::createFromFormat('Y-m-d H:i', $designated_start);
// $designated_start = date_format($date, "l d F Y ") . ' ' . date_format($date, "H:i");
            $designated_start = date_i18n("l d F Y ", date_timestamp_get($date)) . ' ' . date_format($date, "H:i");

            $output .= '<p>' . __('Your designated start time is', 'LEL2017Plugin') . ' ' . $designated_start . ', ' . __('time limit', 'LEL2017Plugin') . ' ' . $time_limit . '. '
                    . __('Your rider number is ', 'LEL2017Plugin') . ' ' . $rider_id . '.</p>';
            $output .= '</div>';
        }

        if (empty($designated_start) && $start_locked === 'yes') {
            $output .= '<p><strong>' . __('Your preferred start time is now locked', 'LEL2017Plugin') . '</strong></p>';
        }
        if (empty($designated_start) && $start_locked !== 'yes') {
            $output .= '<p><strong>' . __('Please choose a preferred start time', 'LEL2017Plugin') . '</strong></p>';
        }


        if (empty($designated_start)) {
            $output .= '<div id="lel-wave-detail" ';



            if ($chosen_wave >= 1) {
// Get wave details


                $wave_text = __('You have chosen', 'LEL2017Plugin');


                $wave_row = $this->getRiderWaves(true, $chosen_wave);
                $date = strtotime($wave_row[0]["start_time"]);

                $wave_detail = __($wave_text, 'LEL2017Plugin') . ' '
                        . date('H:i', $date)
                        . ', '
                        . $wave_row[0]["description"]
                        . ', ' . __('time limit', 'LEL2017Plugin')
                        . ' '
                        . $wave_row[0]["time_limit"];

                $output .= ' ';
            } else {
                $wave_detail = __('You have no preferred start time', 'LEL2017Plugin');
            }

            $output .= 'class="alert alert-info">'
                    . $wave_detail
                    . '</div>';
        }
        if (empty($designated_start) && $start_locked !== 'yes') {
// Get the available wave information

            $available_waves = $this->getRiderWaves(true);
            array_shift($available_waves);
            $output .= '<form action="chooseRiderWave">';
            $output .= '<select id="ChosenWave" name="ChosenWave" class="form-control">';
            $output .= '<option value="0">' . __('You have no preferred start time', 'LEL2017Plugin') . '</option>';

            foreach ($available_waves as $a_wave) {

                $output .= '<option value="'
                        . $a_wave["id"]
                        . '"';
                if ($a_wave["id"] == $chosen_wave) {
                    $output .= ' selected';
                }

                $date = strtotime($a_wave["start_time"]);

                $output .= '>'
                        . date('H:i', $date)
                        . ', '
                        . $a_wave["description"]
                        . ', ' . __('time limit', 'LEL2017Plugin')
                        . ' '
                        . $a_wave["time_limit"]
                        . '</option>';
            }
            $output .= '</select>';

            $output .= wp_nonce_field('choose_rider_wave', '_verify');
            $output .= '<br><input class="btn btn-primary" type="submit" value="' . __('Update', 'LEL2017Plugin') . '">';

            $output .= '</form><br>';
            $output .= '<div id="lel-response-message"></div>';
        }

        $output .= '</div>';
        return $output;
    }

    public function admin_start_waves() {
// Check user is authorised to see this content
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return '<div class="alert alert-danger">You must be logged in to access this content</div>';
        }
        if (!current_user_can('update_rider_start_waves')) {
            return '<div class="alert alert-danger">You do not have authority to access this content</div>';
        }
// Enqueue the javascript required

        add_action('wp_enqueue_scripts', wp_enqueue_script('lel-admin-start-waves'));
        wp_localize_script('lel-admin-start-waves', 'wp', array(
// URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url('admin-ajax.php')
                )
        );
// Create form for adding start times
        $output = '<div id="lel-rider-waves"></div>';
        $output .= '<form id="lel-admin-start-wave" name="lel-admin-start-wave">';
        $output .= '<input type="hidden" name="WaveID" id="WaveID">';
        $output .= '<label for="Description">Description:</label> ';
        $output .= '<input type="text" class="form-control" name="Description" id="Description" placeholder="For example, Tourist">';
        $output .= '<label for="StartTime">Start Time:</label> ';
        $output .= '<input type="text" class="form-control" name="StartTime" id="StartTime" placeholder="yyyy-mm-dd hh:mm, For example, 2017-07-30 14:15">';
        $output .= '<label for="TimeLimit">Time Limit:</label> ';
        $output .= '<input type="text" class="form-control" name="TimeLimit" id="TimeLimit" placeholder="For example, 116:40 or 100">';


        $output .= '<label for="TotalPlaces">Total Places:</label> ';
        $output .= '<input type="text" class="form-control" name="TotalPlaces" id="TotalPlaces" placeholder="Enter the total number of places in this start wave, for example 300"><br>';

        $output .= wp_nonce_field('admin_rider_wave', '_verify');
        $output .= '</form>';
        $output .= '<div id="lel-update-message" ></div>';
        $output .= '<button class="btn btn-primary" id="add-wave-submit">Add Wave</button>';
        $output .= '<button class="btn btn-primary hidden" id="update-wave-submit">Update Wave</button>';
        $output .= ' <button class="btn btn-primary hidden" id="delete-wave-submit">Delete Wave</button>';
        $output .= ' <button class="btn btn-primary hidden" id="cancel-wave-submit">Cancel</button>';


        $output .= '<div id="lel-rider-waves"></div>';


        return $output;
    }

    public function updateBagDrops() {
        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'update_bag_drops')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


        if (get_option('lock_bag_drops') === 'yes') {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Your bag drop choices are now locked', 'LEL2017Plugin')
            ]);
        }

// userid will be 0 is not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }



// Update user data

        $userdata = [
            bag_drop_1 => (int) $_POST['BagDrop1'],
            bag_drop_2 => (int) $_POST['BagDrop2']
        ];

// Validate bag drops


        if ($userdata["bag_drop_2"] !== 0 and
                $userdata["bag_drop_1"] == 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Please choose a Control for Bag Drop 1 or set Bag Drop 2 to None', 'LEL2017Plugin')
            ]);
        }

        /*
         * Update bag drops
         */


        foreach ($userdata as $key => $value) {

            update_user_meta($user_id, $key, $value);
        }

        $bag_drops = $this->common->bagDropLocations(true);

        $bag_text = '';
        If ($userdata["bag_drop_1"] > 1) {
            $output .= __('You have chosen', 'LEL2017Plugin') . ' ';
            foreach ($bag_drops as $bag_drop) {
                if ($bag_drop["ID"] === $userdata["bag_drop_1"]) {

                    $bag_text_1 .= $bag_drop["post_title"];
                }
                if ($bag_drop["ID"] === $userdata["bag_drop_2"] and $userdata["bag_drop_2"] > 1) {
                    $bag_and = ' ' . __('and', 'LEL2017Plugin') . ' ';
                    $bag_text_2 .= $bag_drop["post_title"];
                }
            }

            $bag_text = $bag_text_1 . $bag_and . $bag_text_2;
        }
        if (empty($bag_text)) {
            $bag_text = __('You have chosen no bag drops', 'LEL2017Plugin');
        }
        $output .= $bag_text;


        $msg = __('Your bag drop details have been updated', 'LEL2017Plugin');
        $result = [
            'status' => 'success',
            'info' => $output,
            'msg' => $msg
        ];
        wp_send_json($result);
    }

    public function addRiderWave() {
        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'admin_rider_wave')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }
// userid will be 0 is not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

        if (!current_user_can('update_rider_start_waves')) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


// Validate the wave data
        $description = sanitize_text_field($_POST['Description']);
        if (empty($description)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Description cannot be empty', 'LEL2017Plugin')
            ]);
        }
        $start_time = sanitize_text_field($_POST['StartTime']);
        $pattern = '/\d{4,4}-\d{2,2}-\d{2,2} \d{2,2}:\d{2,2}/im';
        if (preg_match($pattern, $start_time) === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Start time not in yyyy-mm-dd hh:mm format', 'LEL2017Plugin')
            ]);
        }

        $time_limit = sanitize_text_field($_POST['TimeLimit']);
        $pattern = '/\A\d{2,3}:{0,1}\d{0,2}\z/im';
        if (preg_match($pattern, $time_limit) === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('time limit not valid', 'LEL2017Plugin')
            ]);
        }
        $total_places = sanitize_text_field($_POST['TotalPlaces']);
        if (!is_numeric($total_places)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Total Places must be numeric', 'LEL2017Plugin')
            ]);
        }

// Add wave to database

        global $wpdb;

        $data = [
            description => $description,
            start_time => $start_time,
            time_limit => $time_limit,
            total_places => $total_places,
            requested_places => 0
        ];



        $table_name = $wpdb->prefix . "startwaves";

        $format = [
            '%s',
            '%s',
            '%s',
            '%d',
            '%d'
        ];

        if (!$wpdb->insert($table_name, $data, $format)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There was an error adding the wave', 'LEL2017Plugin')
            ]);
        }

        wp_send_json([
            'status' => 'success',
            'msg' => __('Rider Wave has been added', 'LEL2017Plugin')
        ]);
    }

    public function getRiderWaves($array = false, $id = 0) {
// userid will be 0 is not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


        if (intval($_GET['id']) !== 0) {
            $id = intval($_GET['id']);
        }

// Always work against English blog
        switch_to_blog(1);
        $result = false;
        if ($id === 0) {
            $result = wp_cache_get('riderStartWaves', 'Rider');
        }


        if (false !== $result) {
            restore_current_blog();
            if ($array) {
                return $result;
            }
            wp_send_json($result);
        }


        global $wpdb;
        $result = [];
        $table_name = $wpdb->prefix . "startwaves";

        $sql = "SELECT * FROM " . $table_name;

        if ($id !== 0) {

            if (is_numeric($id)) {
                $sql .= ' WHERE id = %d';
                $sql = $wpdb->prepare($sql, $id);
            }
        }
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

        if ($id === 0) {

            wp_cache_set('riderStartWaves', $result, 'Rider', $this->cache);
        }

        restore_current_blog();
        if ($array) {
            return $result;
        }
        wp_send_json($result);
    }

    public function updateRiderWave() {


        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'admin_rider_wave')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


// userid will be 0 is not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

        if (!current_user_can('update_rider_start_waves')) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }



// Validate the wave data

        $id = sanitize_text_field($_POST['WaveID']);

        if (!is_numeric($id)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }



        $description = sanitize_text_field($_POST['Description']);
        if (empty($description)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Description cannot be empty', 'LEL2017Plugin')
            ]);
        }


        $start_time = sanitize_text_field($_POST['StartTime']);
        $pattern = '/\d{4,4}-\d{2,2}-\d{2,2} \d{2,2}:\d{2,2}/im';



        if (preg_match($pattern, $start_time) === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Start time not in yyyy-mm-dd hh:mm format', 'LEL2017Plugin')
            ]);
        }
        $time_limit = sanitize_text_field($_POST['TimeLimit']);
        $pattern = '/\A\d{2,3}:{0,1}\d{0,2}\z/im';
        if (preg_match($pattern, $time_limit) === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('time limit not valid', 'LEL2017Plugin')
            ]);
        }
        $total_places = sanitize_text_field($_POST['TotalPlaces']);
        if (!is_numeric($total_places)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Total Places must be numeric', 'LEL2017Plugin')
            ]);
        }



// Add wave to database

        global $wpdb;

        $data = [
            description => $description,
            start_time => $start_time,
            time_limit => $time_limit,
            total_places => $total_places,
        ];



        $table_name = $wpdb->prefix . "startwaves";

        $format = [
            '%s',
            '%s',
            '%s',
            '%d'
        ];

        $where = ['id' => $id];

        $where_format = [
            '%d'
        ];

        if ($wpdb->update($table_name, $data, $where, $format, $where_format) === false) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There was an error updating the wave', 'LEL2017Plugin')
            ]);
        }

        wp_cache_delete('riderStartWaves', 'Rider');

        wp_send_json([
            'status' => 'success',
            'msg' => __('Rider Wave has been updated', 'LEL2017Plugin')
        ]);
    }

    public function deleteRiderWave() {
        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'admin_rider_wave')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }
// userid will be 0 is not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

        if (!current_user_can('update_rider_start_waves')) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


// Validate the wave data
        $id = sanitize_text_field($_POST['WaveID']);
        if (empty($id)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


//  Delete wave from database

        global $wpdb;


        $table_name = $wpdb->prefix . "startwaves";
        $where = ['id' => $id];

        $where_format = [
            '%d'
        ];



        $wpdb->delete($table_name, $where, $where_format);

        if ($wpdb->delete($table_name, $where, $where_format) === false) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There was an error deleting the wave', 'LEL2017Plugin')
            ]);
        }

        wp_send_json([
            'status' => 'success',
            'msg' => __('Rider Wave has been deleted', 'LEL2017Plugin')
        ]);
    }

    public function chooseRiderWave() {
        /*
         * Updates a riders chosen start wave
         */

// Check submission is valid
        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'choose_rider_wave')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

        if (get_option('lock_start_times') === 'yes') {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Your preferred start time is now locked', 'LEL2017Plugin')
            ]);
        }

// Userid will be 0 if not signed in
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }
// Check to see if designated has been set

        $user_info = get_userdata($user_id);


        $designated_start = sanitize_text_field($user_info->designated_start);
        $designated_start = sanitize_text_field($user_info->designated_start);

        if (is_numeric($designated_start)) {

            if (!empty($designated_start)) {
                $date = DateTime::createFromFormat('Y-m-d h:i', $designated_start);
                $designated_start = date_format($date, "l d F Y ") . ' ' . date_format($date, "h:i");

                $extra .= __('Your designated start time is', 'LEL2017Plugin') . ' ' . $designated_start;
            }
            if ($chosen_wave > 1 or $designated_start > 1) {
// Get wave details
                global $wpdb;

                $wave = ($designated_start !== 0 ? $designated_start : $chosen_wave );
                $wave_text = ($designated_start !== 0 ? __('You are in', 'LEL2017Plugin') : __('You have chosen', 'LEL2017Plugin'));

                $wave_row = $this->getRiderWaves(true, $wave);

                $date = strtotime($wave_row[0]["start_time"]);

                $wave_detail = __($wave_text, 'LEL2017Plugin') . ' '
                        . date('H:i', $date)
                        . ', '
                        . $wave_row[0]["description"]
                        . ', ' . __('time limit', 'LEL2017Plugin')
                        . ' '
                        . $wave_row[0]["time_limit"];

                $info = $wave_detail;
            }



            wp_send_json([
                'status' => 'success',
                'info' => $info,
                'event' => 'designatedWaveSet',
                'extra' => $extra,
                'msg' => __('Designated time has been set', 'LEL2017Plugin')
            ]);
        }


// Validate submited data

        $new_wave = (int) sanitize_text_field(($_POST['ChosenWave']));

        if (!is_numeric($new_wave)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }



// Update chosen wave

        $key = 'chosen_wave';
        if ($new_wave === 0) {
            delete_user_meta($user_id, $key);
        } else {
            update_user_meta($user_id, $key, $new_wave);
        }


        $wave_row = $this->getRiderWaves(true, $new_wave);
        $wave_text = 'You have chosen';
        if ($new_wave === 0) {
            $info = __('You have no preferred start time', 'LEL2017Plugin');
        } else {
            $date = strtotime($wave_row[0]["start_time"]);
            $info = __($wave_text, 'LEL2017Plugin') . ' '
                    . date('H:i', $date)
                    . ', '
                    . $wave_row[0]["description"]
                    . ', ' . __('time limit', 'LEL2017Plugin')
                    . ' '
                    . $wave_row[0]["time_limit"];
        }

        wp_send_json([
            'status' => 'success',
            'info' => $info,
            'event' => 'wavesUpdated',
            'msg' => __('Your preferred start time has been updated', 'LEL2017Plugin')
        ]);
    }

    public function register_scripts() {



        wp_register_script('lel-update-bag-drops', plugins_url('../js/update-bag-drops.js', __FILE__), array('flight-ajax', 'flight-message', 'flight-info'), '', true);

        wp_register_script('lel-rider-wave', plugins_url('../js/rider-wave.js', __FILE__), array('flight-ajax', 'flight-message', 'flight-info', 'flight-extra', 'flight-hideshow'), '', true);

        wp_register_script('lel-admin-start-waves', plugins_url('../js/admin-start-waves.js', __FILE__), array('jquery'), '', true);



        wp_register_script('rider-list-template', plugins_url('../js/underscore-templates/RiderListTemplate.js', __FILE__), array('underscore'), '', true);

        wp_register_script('rider-ajax', plugins_url('../js/flight-components/AjaxComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-ajaxsearch', plugins_url('../js/flight-components/AjaxSearchComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-copy', plugins_url('../js/flight-components/CopyComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-paste', plugins_url('../js/flight-components/PasteComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-hideshow', plugins_url('../js/flight-components/HideShowComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-info', plugins_url('../js/flight-components/InfoComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-message', plugins_url('../js/flight-components/MessageComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-prev', plugins_url('../js/flight-components/PrevComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-next', plugins_url('../js/flight-components/NextComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-offset', plugins_url('../js/flight-components/OffsetComponent.js', __FILE__), array('flight'), '', true);
        wp_register_script('rider-resultsperpage', plugins_url('../js/flight-components/ResultsPerPageComponent.js', __FILE__), array('flight'), '', true);
// wp_register_script('rider-list', plugins_url('../js/flight-components/RiderListComponentOld.js', __FILE__), array('flight'), '1.1', true);


        wp_register_script('lel-admin-designated-starts', plugins_url('../js/admin-designated-starts.js', __FILE__), array('rider-list-template', 'rider-ajax', 'rider-ajaxsearch', 'rider-copy', 'rider-paste', 'rider-hideshow', 'rider-info', 'rider-message', 'rider-prev', 'rider-next', 'rider-offset', 'rider-resultsperpage', 'rider-list'), '', true);
    }

    public function register_styles() {
        
    }

    public function admin_designated_starts() {
        /*
         * Output the forms and enqueue the scripts to allow selection of
         * riders and update of designated start
         */

// Check user is authorised to see this content
        $user_id = get_current_user_id();
        if ($user_id === 0) {
            return '<div class = "alert alert-danger">You must be logged in to access this content</div>';
        }
        if (!current_user_can('update_designated_start_waves')) {
            return '<div class = "alert alert-danger">You do not have authority to access this content</div>';
        }



// Enqueue the javascript required

        add_action('wp_enqueue_scripts', wp_enqueue_script('lel-admin-designated-starts'));
        wp_localize_script('lel-admin-designated-starts', 'wp', array(
// URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url('admin-ajax.php')
                )
        );

        $output = $this->admin_search_designated_forms();
        $output .= $this->admin_copy_designated_form();
        $output .= $this->admin_rider_designated_starts_form();


        /*
         * Now output the content
         */
        return $output;
    }

    public function admin_rider_designated_starts_form() {
        /*
         * Display the outline form into which rider designated wave
         * details will be populated.
         */

        $output = '<div class = "row"><br>';
        $output .= '<h4>Riders</h4>';

        /*
         * Form
         */
        $output .= '<form id = "lel-rider-page-options" class = "form-inline" role = "form">'
                . '<div class = "col-md-12">'
                . '<div class = "form-group">'
                . '<select id = "resultsPerPage" class = "form-control">'
                . '<option value = 25>25 riders per page</option>'
                . '<option value = 30 selected>30 riders per page</option>'
                . '<option value = 50>50 riders per page</option>'
                . '<option value = 100>100 riders per page</option>'
                . '</select>'
                . '</div>'
                . '<div class = "form-group">'
                . '<label>&nbsp;
                offset &nbsp;
                </label>'
                . '<input class = "form-control" type = "text" size = 5 id = "resultsOffset" value = 0>'
                . '</div>'
                . '<div class = "input-group">'
                . '<span class = "input-group-btn">'
                . '<btn class = "btn btn-primary" id = "lel-js-prev"><</btn>'
                . '</span>'
                . '<span class = "input-group-btn">'
                . '<btn class = "btn btn-primary" id = "lel-js-next">></btn>'
                . '</span>'
                . '</div>'
                . '</form>'
                . '</div>'
                . '</div>';
        /*
         * Message for riders returned
         */
        $output .= '<div id = "lel-rider-return-msg"></div>';
        /*
         * Rider results form
         */


        $output .= '<form class = "form-inline" id = "js-rider-designated-starts-form" action = "updateDesignatedStarts">'
                . wp_nonce_field('updateRiderDesignatedStarts', '_verify')
                . '<span class = "col-md-3"><strong>'
                . __('Name', 'LEL2017Plugin')
                . '</strong></span>'
                . '<span class = "col-md-2"><strong>'
                . __('Team', 'LEL2017Plugin')
                . '</strong></span>'
                . '<span class = "col-md-2"><strong>'
                . __('Preferred Start', 'LEL2017Plugin')
                . '</strong></span>'
                . '<span class = "col-md-2"><strong>'
                . __('Designated Start', 'LEL2017Plugin')
                . '</strong></span><br><br>'
                . '<div id = "lel-rider-wave-content"></div>'
                . '<span class = "clearfix">&nbsp;
                </span>'
                . '<input class = "btn btn-primary hidden" type = "submit" id = "lel-designated-submit" value = "Update">'
                . '</form><br>'
                . '<div id = "lel-designated-update-msg"></div>';

        /*
         * Finish off and return output
         */




        return $output;
    }

    public function admin_copy_designated_form() {

        $output = '<div class = "row" id = "lel-start-wave-copy"><br>';
        $output .= '<h4>Copy</h4>';
        $output .= '<p>You may copy the designated start to every rider shown on this page</p>';

        /*
         * Designated Wave to copy
         */
        $output .= '<span class = "col-md-4">';
        $available_waves = $this->getRiderWaves(true);
        $output .= '<form id = "lel-designated-start-copy-form" >';

        $output .= '<span class = "input-group">';
        $output .= '<select id = "CopyWave" name = "CopyWave" class = "form-control">';
        foreach ($available_waves as $a_wave) {
            $output .= '<option value = '
                    . $a_wave["id"]
                    . '>'
                    . substr($a_wave["start_time"], 0, 16)
                    . " "
                    . $a_wave["description"]
                    . '</option>';
        }

        $output .= '</select>';

        $output .= '<span class = "input-group-btn">'
                . '<btn class = "btn btn-primary" >Copy</btn>'
                . '</span>'
                . "</span>";
        $output .= '</form>';

        $output .= '</span>';


        /*
         * Finish off and return content
         */
        $output .= '</div>';



        return $output;
    }

    public function admin_search_designated_forms() {

        $output = '<div class = "row">';
        $output .= '<h4>Search</h4>';
        $output .= '<p>You may retrieve riders by preferred start time, designated start time(set or not set), surname, or team name </p>';

        /*
         * Retrieve by Chosen Wave
         */
        $output .= '<span class = "col-md-4">';
        $output .= '<form class = "form-inline" action = "searchChosenWaveRiders" type = "search">';
        $output .= wp_nonce_field('searchChosenWave', '_verify');
        $output .= '<span class = "input-group">';
        $available_waves = $this->getRiderWaves(true);
        $output .= '<select id = "ChosenWave" name = "ChosenWave" class = "form-control">';
        foreach ($available_waves as $a_wave) {

            $output .= '<option value = '
                    . $a_wave["id"]
                    . '>'
                    . substr($a_wave["start_time"], 0, 16)
                    . " "
                    . $a_wave["description"]
                    . '</option>';
        }
        $output .= '</select>';
        $output .= '<span class = "input-group-btn">'
                . '<input class = "btn btn-primary" type = "submit" value = "Go">'
                . '</span>'
                . '</span>';
        $output .= '</form>';
        $output .= '</span>';


        /*
         * Search by Surname
         */

        $output .= '<span class = "col-md-4">';
        $output .= '<form class = "form-inline" action = "searchSurnameRiders" type = "search">';
        $output .= wp_nonce_field('searchSurname', '_verify');
        $output .= '<span class = "input-group">';
        $output .= '<input type = "text" class = "form-control" name = "searchSurname" placeholder = "Enter Surname">';
        $output .= '<span class = "input-group-btn">'
                . '<input class = "btn btn-primary" type = "submit" value = "Go">'
                . '</span>'
                . '</span>';
        $output .= '</form>';

        $output .= '</span>';
        /*
         * Retrieve by Team name
         */
        $output .= '<span class = "col-md-4">';
        $output .= '<form class = "form-inline" action = "searchTeamRiders" type = "search">';
        $output .= wp_nonce_field('searchTeam', '_verify');
        $output .= '<span class = "input-group">';
        $output .= '<input type = "text" class = "form-control" name = "searchTeam" placeholder = "Enter Team">';
        $output .= '<span class = "input-group-btn">'
                . '<input class = "btn btn-primary" type = "submit" value = "Go">'
                . '</span>'
                . '</span>';
        $output .= '</form>';

        $output .= '</span>';

        /*
         * Retrieve by Designated Start
         */
        $output .= '<span class = "col-md-4">';
        $output .= '<form class = "form-inline" action="searchDesignatedStartRiders" type = "search">';
        $output .= wp_nonce_field('searchDesignatedStart', '_verify');
        $output .= '<span class = "input-group">';
        $available_waves = $this->getRiderWaves(true);
        $output .= '<select id = "searchDesignatedStart" name="searchDesignatedStart" class = "form-control">';
        $output .= '<option value=0>' . __('None', 'LEL2017Plugin') . '</option>';
        foreach ($available_waves as $a_wave) {

            $output .= '<option value = '
                    . $a_wave["id"]
                    . '>'
                    . substr($a_wave["start_time"], 0, 16)
                    . " "
                    . $a_wave["description"]
                    . '</option>';
        }
        $output .= '</select>';

        $output .= '<span class = "input-group-btn">'
                . '<input class = "btn btn-primary" type = "submit" value = "Go">'
                . '</span>'
                . '</span>';
        $output .= '</form>';

        $output .= '</span>';



        /*
         * Finish off and return output
         */

        $output .= '</div>';

        return $output;
    }

    public function searchSurnameRiders() {
        /*
         * Return an array of riders matching surname search criteria
         */

// Check submission is valid

        /*
         * userid will be 0 if not signed in
         */

        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                ['status' => 'error',
                    'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
                ]
            ]);
        }

        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'searchSurname')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }




// Get the max number of riders to be returned in result
        $resultsPerPage = (int) $_POST["resultsPerPage"];
        $resultsPerPage === ($resultsPerPage == 0 ? 30 : $resultsPerPage);
// Get the number of riders to skip in the results
        $resultsOffset = (int) $_POST["resultsOffset"];
// Get the surname to match on
        $searchSurname = sanitize_text_field($_POST["searchSurname"]);
        $searchSurname = (strlen($searchSurname) === 0 ? ' ' : $searchSurname );

        $args = [
            'role' => 'rider',
            'meta_key' => 'last_name',
            'meta_compare' => ' = ',
            'meta_value' => $searchSurname,
            'offset' => $resultsOffset,
            'number' => $resultsPerPage,
            'fields' => 'all'
        ];
        $users = get_users($args);
        /*
         * How process and return result
         */
        $riders = [];
        foreach ($users as $user) {
            if ($user->first_name !== '') {
                $chosen_wave = '';

                if ($user->chosen_wave != '') {
                    $chosen_wave = $this->getRiderWaves(true, $user->chosen_wave);
                }

                $fields = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'team_name' => $user->team_name,
                    'chosen_wave' => $chosen_wave[0]["description"],
                    'designated_start' => $user->designated_start
                ];
                $riders[] = $fields;
            }
        }

        $msg = (count($riders) > 0 ? 'Matching riders found' : 'No matching riders found');

        wp_send_json(
                ['status' => 'success',
                    'msg' => $msg,
                    'offset' => $resultsOffset,
                    'action' => 'searchSurnameRiders',
                    'riders' => $riders]
        );
    }

    public function searchChosenWaveRiders() {
        /*
         * Return an array of riders matching chosen wave search criteria
         */

// Check submission is valid

        /*
         * userid will be 0 if not signed in
         */

        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                ['status' => 'error',
                    'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
                ]
            ]);
        }

        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'searchChosenWave')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


// Get the max number of riders to be returned in result
        $resultsPerPage = (int) $_POST["resultsPerPage"];
        $resultsPerPage === ($resultsPerPage == 0 ? 30 : $resultsPerPage);
// Get the number of riders to skip in the results
        $resultsOffset = (int) $_POST["resultsOffset"];
// Get the wave start to match on
        $searchWave = (int) $_POST["searchChosenWave"];


        $args = [
            'role' => 'rider',
            'meta_key' => 'chosen_wave',
            'meta_compare' => ' = ',
            'meta_value' => $searchWave,
            'offset' => $resultsOffset,
            'number' => $resultsPerPage,
            'fields' => 'all'
        ];
        $users = get_users($args);
        /*
         * How process and return result
         */
        $riders = [];
        foreach ($users as $user) {
            if ($user->first_name !== '') {
                $chosen_wave = '';

                if ($user->chosen_wave != '') {
                    $chosen_wave = $this->getRiderWaves(true, $user->chosen_wave);
                }

                $fields = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'team_name' => $user->team_name,
                    'chosen_wave' => $chosen_wave[0]["short_description"],
                    'designated_start' => $user->designated_start,
                    'designated_start' => $user->designated_start
                ];
                $riders[] = $fields;
            }
        }

        $msg = (count($riders) > 0 ? 'Matching riders found' : 'No matching riders found');

        wp_send_json(
                ['status' => 'success',
                    'msg' => $msg,
                    'offset' => $resultsOffset,
                    'action' => 'searchChosenWaveRiders',
                    'riders' => $riders]
        );
    }

    public function searchDesignatedStartRiders() {
        /*
         * Return an array of riders matching designated wave search criteria
         */

// Check submission is valid

        /*
         * userid will be 0 if not signed in
         */

        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                ['status' => 'error',
                    'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
                ]
            ]);
        }

        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'searchDesignatedStart')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


// Get the max number of riders to be returned in result
        $resultsPerPage = (int) $_POST["resultsPerPage"];
        $resultsPerPage === ($resultsPerPage == 0 ? 30 : $resultsPerPage);
// Get the number of riders to skip in the results
        $resultsOffset = (int) $_POST["resultsOffset"];
// Get the wave to match on
        $searchWave = (int) $_POST["searchDesignatedStart"];

        $meta_compare = ($searchWave != 0 ? ' = ' : 'NOT EXISTS');



        $args = [
            'role' => 'rider',
            'meta_key' => 'designated_start',
            'meta_compare' => $meta_compare,
            'meta_value' => $searchWave,
            'offset' => $resultsOffset,
            'number' => $resultsPerPage,
            'fields' => 'all'
                ]

        ;
        $users = get_users($args);
        /*
         * How process and return result
         */
        $riders = [];
        foreach ($users as $user) {
            if ($user->first_name !== '') {
                $chosen_wave = '';

                if ($user->chosen_wave != '') {
                    $chosen_wave = $this->getRiderWaves(true, $user->chosen_wave);
                }

                $fields = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'team_name' => $user->team_name,
                    'chosen_wave' => $chosen_wave[0]["short_description"],
                    'designated_start' => $user->designated_start
                ];
                $riders[] = $fields;
            }
        }

        $msg = (count($riders) > 0 ? 'Matching riders found' : 'No matching riders found');

        wp_send_json(
                ['status' => 'success',
                    'msg' => $msg,
                    'offset' => $resultsOffset,
                    'action' => 'searchDesignatedStartRiders',
                    'riders' => $riders]
        );
    }

    public function searchTeamRiders() {
        /*
         * Return an array of riders matching team search criteria
         */

// Check submission is valid

        /*
         * userid will be 0 if not signed in
         */

        $user_id = get_current_user_id();
        if ($user_id === 0) {
            wp_send_json([
                ['status' => 'error',
                    'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
                ]
            ]);
        }

        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'searchTeam')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

// Get the max number of riders to be returned in result
        $resultsPerPage = (int) $_POST["resultsPerPage"];
        $resultsPerPage === ($resultsPerPage == 0 ? 30 : $resultsPerPage);
// Get the number of riders to skip in the results
        $resultsOffset = (int) $_POST["resultsOffset"];
// Get the surname to match on
        $searchTeam = sanitize_text_field($_POST["searchTeam"]);
        $searchTeam = (strlen($searchTeam) === 0 ? ' ' : $searchTeam );



        $args = [
            'role' => 'rider',
            'meta_key' => 'team_name',
            'meta_compare' => ' = ',
            'meta_value' => $searchTeam,
            'offset' => $resultsOffset,
            'number' => $resultsPerPage,
            'fields' => 'all'
        ];
        $users = get_users($args);
        /*
         * How process and return result
         */
        $riders = [];
        foreach ($users as $user) {
            if ($user->first_name !== '') {
                $chosen_wave = '';

                if ($user->chosen_wave != '') {
                    $chosen_wave = $this->getRiderWaves(true, $user->chosen_wave);
                }

                $fields = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'team_name' => $user->team_name,
                    'chosen_wave' => $chosen_wave[0]["short_description"],
                    'designated_start' => $user->designated_start,
                    'designated_start' => $user->designated_start
                ];
                $riders[] = $fields;
            }
        }

        $msg = (count($riders) > 0 ? 'Matching riders found' : 'No matching riders found');

        wp_send_json(
                ['status' => 'success',
                    'msg' => $msg,
                    'offset' => $resultsOffset,
                    'action' => 'searchTeamRiders',
                    'riders' => $riders]
        );
    }

    public function updateDesignatedStarts() {
        /*
         * Update batch of rider start times and waves
         */

        /*
         * Verify that request has come from legitimate source
         */
        $nonce = $_POST['_verify'];

        if (!wp_verify_nonce($nonce, 'updateRiderDesignatedStarts')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }
        /*
         * Verify that user is authorised to update rider waves
         */

        if (!current_user_can('update_rider_start_waves')) {

            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }


        $response = [];
        $users = [];
        $startWaves = [];
        $startTimes = [];
        foreach ($_POST as $key => $value) {
            if (strstr($key, 'User')) {
                $users[] = (int) $value;
            }
            if (strstr($key, 'DesignatedStart')) {

                $startWaves[] = (int) $value;
            }
        }

        /*
         * Now check start wave and if invalid
         */

        foreach ($startWaves as $startWave) {

            if ($startWave === 0) {
                wp_send_json([
                    'status' => 'error',
                    'msg' => __('Designated Start not set', 'LEL2017Plugin')
                ]);
            }
        }

        if (count($startWaves) === 0) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('Designated Start not set', 'LEL2017Plugin')
            ]);
        }

        /*
         * Check we have the same number of users as starts
         */
        if (count($users) != count($startWaves)) {
            wp_send_json([
                'status' => 'error',
                'msg' => __('There has been an error, please refresh page', 'LEL2017Plugin')
            ]);
        }

        /*
         * Now update all the Start Waves and Times for the riders / users
         */

        $l = count($users);
        for ($i = 0; $i < $l; $i ++) {
            update_user_meta($users[$i], 'designated_start', $startWaves[$i]);
        }



        wp_send_json(
                ['status' => 'success',
                    'msg' => 'Designated Starts updated successfully',
                ]
        );
    }

    /*
     * Export riders and their start choices
     */

    function csv_export_riders() {

        $filename = 'lel-rider-start-times-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'Username',
            'Email',
            'First Name',
            'Last Name',
            'Team Name',
            'Team Size',
            'random_sort_key',
            'Chosen Start',
            'Designated Start',
            'Time Limit',
            'Rider Number',
            'Country'
        ];


        $fh = @fopen('php://output', 'w');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));


        fputcsv($fh, $header_row);

// Build a list of team names and sizes
        global $wpdb;
        $table_name = $wpdb->prefix . "usermeta";
        $sql = "SELECT meta_value AS 'team_name', count(*) AS size FROM "
                . $table_name
                . " WHERE meta_key = 'team_name' AND meta_value <> ''"
                . "GROUP BY meta_value";
        $rows = $wpdb->get_results($sql);
        $team_names = [];
        foreach ($rows as $row) {
            $team_name = strtolower($row->team_name);
// Now strip out extra characters
            $team_name = preg_replace("/[^A-Za-z0-9]/", '', $team_name);

            $size = intval($row->size);
            if (isset($team_names[$team_name])) {
                $team_names[$team_name] = $team_names[$team_name] + $size;
            } else {
                $team_names[$team_name] = $size;
            }
        }


// arguments to retrieve riders

        $args = [
            'role' => 'Rider'
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {
            $chosen_wave = intval($rider->chosen_wave);
            $start_time = '';
            if ($chosen_wave >= 1) {
                $wave_row = $this->getRiderWaves(true, $rider->chosen_wave);
                if ($wave_row) {
                    $start_time = $wave_row[0]["start_time"];
                }
            }
            $team_name = strtolower($rider->team_name);
// Now strip out extra characters
            $team_name = preg_replace("/[^A-Za-z0-9]/", '', $team_name);

            if ($team_name !== '') {
                $tsize = $team_names[$team_name];
            } else {
                $tsize = 1;
            }



            $row = [
            $rider->user_login,
            $rider->user_email,
            $rider->first_name,
            $rider->last_name,
            $team_name,
            $tsize,
            wp_generate_password(10, false),
            $start_time,
            $rider->designated_start ?? '',
            $rider->designated_limit,
            $rider->rider_id,
            $rider->billing_country
            ];

            $data_rows[] = $row;
        }

        usort($data_rows, array($this, 'sort_by_starttime'));


        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    function csv_export_rider_finish_list() {

        $filename = 'lel-rider-finish-lists-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'First Name',
            'Last Name',
            'Country',
            'Time',
            'AUK Membership No.',
        ];


        $fh = @fopen('php://output', 'w');



        fputcsv($fh, $header_row);



// Get a list of finisher riders
        $riders = $this->get_rider_finishers();

// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {

            $elapsed_hh = floor($rider->elapsed / 3600);
            $elapsed_mm = floor(($rider->elapsed - $elapsed_hh * 3600) / 60);
            $elapsed_fmt = $elapsed_hh . ' hours ' . $elapsed_mm . ' mins';



            $data_row = [
                "first_name" => ucfirst(strtolower($rider->first_name)),
                "last_name" => ucfirst(strtolower($rider->last_name)),
                "country" => $this->common->get_country($rider->billing_country),
                "Time" => $elapsed_fmt,
                "AUK" => $rider->auk_membership_number
            ];
            $data_rows[] = $data_row;
        }

        // Get a list of DNS riders
        $riders = $this->get_rider_DNS();
        foreach ($riders->results as $rider) {

            $data_row = [
                "first_name" => ucfirst(strtolower($rider->first_name)),
                "last_name" => ucfirst(strtolower($rider->last_name)),
                "country" => $this->common->get_country($rider->billing_country),
                "Time" => "DNS",
                "AUK" => $rider->auk_membership_number
            ];
            $data_rows[] = $data_row;
        }
        // Get a list of DNF rider ids
        $riders = $this->get_rider_DNF();
        foreach ($riders->results as $rider) {

            $data_row = [
                "first_name" => ucfirst(strtolower($rider->first_name)),
                "last_name" => ucfirst(strtolower($rider->last_name)),
                "country" => $this->common->get_country($rider->billing_country),
                "Time" => "DNF",
                "AUK" => $rider->auk_membership_number
            ];
            $data_rows[] = $data_row;
        }


        usort($data_rows, array($this, 'sort_by_last_first_name'));

        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    /*
     * Export riders and their bag drop choices
     */

    function csv_export_riders_bag_drops() {

        $filename = 'lel-rider-bag-drops-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'Email',
            'First Name',
            'Last Name',
            'Bag Drop 1',
            'Bag Drop 2',
            'Rider ID',
            'Country'
        ];


        $fh = @fopen('php://output', 'w');
//fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fh, $header_row);



        switch_to_blog(1);

// arguments to retrieve riders

        $args = [
            'role' => 'Rider'
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {
            $bag_drop_1 = $rider->bag_drop_1 ? get_the_title($rider->bag_drop_1) : 'None';
            $bag_drop_2 = $rider->bag_drop_2 ? get_the_title($rider->bag_drop_2) : 'None';


            $row = [
                $rider->user_email,
                $rider->first_name,
                $rider->last_name,
                $bag_drop_1,
                $bag_drop_2,
                $rider->rider_id,
                $rider->billing_country
            ];

            $data_rows[] = $row;
        }



        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    /*
     * Export riders and their bag drop choices
     */

    function csv_export_riders_frame_cards() {

        $filename = 'lel-rider-frame_cards-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'Email',
            'First Name',
            'Last Name',
            'Rider Number',
            'Country'
        ];


        $fh = @fopen('php://output', 'w');
//fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fh, $header_row);



        switch_to_blog(1);

// arguments to retrieve riders

        $args = [
            'role' => 'Rider'
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {


            $row = [
                $rider->user_email,
                $rider->first_name,
                $rider->last_name,
                $rider->rider_id,
                $rider->billing_country
            ];

            $data_rows[] = $row;
        }



        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    /*
     * Export riders and their personal details
     */

    function csv_export_riders_personal_details() {

        $filename = 'lel-rider-personal-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'Email',
            'First Name',
            'Last Name',
            'Phone',
            'Address line 1',
            'Address Line 2',
            'City',
            'Postcode',
            'Country',
            'Emergency Contact',
            'Emergency Phone',
            'Rider Number',
            'Audax UK Number'
        ];


        $fh = @fopen('php://output', 'w');

        fputcsv($fh, $header_row);

        switch_to_blog(1);

// arguments to retrieve riders

        $args = [
            'role' => 'Rider'
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {

            $row = [
                $rider->user_email,
                $rider->first_name,
                $rider->last_name,
                "'" . $rider->billing_phone,
                $rider->billing_address_1,
                $rider->billing_address_2,
                $rider->billing_city,
                $rider->billing_postcode,
                $rider->billing_country,
                $rider->emergency_contact,
                "'" . $rider->emergency_phone,
                $rider->rider_id,
                $rider->auk_membership_number,
            ];

            $data_rows[] = $row;
        }



        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    /*
     * Export riders and their start choices
     */

    function csv_export_riders_start_lists() {

        $filename = 'lel-rider-start-lists-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'First Name',
            'Last Name',
            'Country',
            'Designated Start',
            'Rider Number',
        ];


        $fh = @fopen('php://output', 'w');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));


        fputcsv($fh, $header_row);

// Build a list of team names and sizes
        global $wpdb;
        $table_name = $wpdb->prefix . "tracking";
        $sql = "SELECT rider_id FROM "
                . $table_name
                . " WHERE action = 'Registration'";

        $rows = $wpdb->get_results($sql);
        $registered = [-1];
        foreach ($rows as $row) {
            $registered[] = $row->rider_id;
        }

// arguments to retrieve riders

        $args = [
            'role' => 'Rider',
            'include' => $registered
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Now generate the data rows for the CSV
        $data_rows = [];
        foreach ($riders->results as $rider) {


            $row = [
                $rider->first_name,
                $rider->last_name,
                $this->common->get_country($rider->billing_country),
                $rider->designated_start,
                $rider->rider_id,
            ];

            $data_rows[] = $row;
        }

        usort($data_rows, array($this, 'sort_by_starttime_only'));


        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    function get_rider_finishers() {
        switch_to_blog(1);

        $riders = wp_cache_get('riderFinishers', 'Rider');

        if ($riders) {
            restore_current_blog();
            return $riders;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT rider_id, elapsed FROM '
                . $table_name
                . ' WHERE '
                . 'distance = 1441 '
                . ' AND action = "Arrival"'
                . ' AND time_in_hand >= 0'
                . ' AND rider_id NOT IN '
                . ' (SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "DNF") AND '
                . ' rider_id IN ( '
                . 'SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "Departure" AND distance = 0);';

        $rows = $wpdb->get_results($sql);

        $finished = [-1];
        $rider_elapsed = [-1];
        foreach ($rows as $row) {
            $finished[] = $row->rider_id;
            $rider_elapsed[$row->rider_id] = $row->elapsed;
        }

// arguments to retrieve riders

        $args = [
            'role' => 'Rider',
            'include' => $finished
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);
// Add the elapsed time to the returned data
        foreach ($riders->results as $rider) {
            $rider->elapsed = $rider_elapsed[$rider->ID];
        }


        wp_cache_set('riderFinishers', $riders, 'Rider', $this->cache);
        restore_current_blog();
        return $riders;
    }

    /*
     * Get riders who did not start
     */

    function get_rider_DNS() {
        switch_to_blog(1);

        $riders = wp_cache_get('riderDNS', 'Rider');

        if ($riders) {
            restore_current_blog();
            return $riders;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT rider_id FROM '
                . $table_name
                . ' WHERE '
                . ' action = "Departure"'
                . ' AND distance = 0';

        $rows = $wpdb->get_results($sql);

        $started = [-1];

        foreach ($rows as $row) {
            $started[] = $row->rider_id;
        }

// arguments to retrieve riders

        $args = [
            'role' => 'Rider',
            'exclude' => $started
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);



        wp_cache_set('riderDNS', $riders, 'Rider', $this->cache);
        restore_current_blog();
        return $riders;
    }

    /*
     * Get rider DNF
     */

    function get_rider_DNF() {
        switch_to_blog(1);

        $riders = wp_cache_get('riderDNF', 'Rider');

        if ($riders) {
            restore_current_blog();
            return $riders;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "tracking";
        $sql = 'SELECT rider_id FROM '
                . $table_name
                . ' WHERE '
                . ' (rider_id NOT IN '
                . ' (SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "Arrival" and distance = 1441 and time_in_hand >= 0) OR '
                . ' rider_id IN '
                . ' (SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "DNF")) AND '
                . ' rider_id IN ('
                . 'SELECT rider_id FROM '
                . $table_name
                . ' WHERE action = "Departure" and distance = 0);';

        $rows = $wpdb->get_results($sql);

        $DNF = [-1];

        foreach ($rows as $row) {
            $DNF[] = $row->rider_id;
        }

// arguments to retrieve riders

        $args = [
            'role' => 'Rider',
            'include' => $DNF
        ];
// Retrieve riders
        $riders = new WP_User_Query($args);


        wp_cache_set('riderDNF', $riders, 'Rider', $this->cache);
        restore_current_blog();
        return $riders;
    }

    function rider_finish_list() {

// Get a list of rider ids
        $riders = $this->get_rider_finishers();


        $data_rows = [];
        foreach ($riders->results as $rider) {

            $data_row = [
                "first_name" => ucfirst(strtolower($rider->first_name)),
                "last_name" => ucfirst(strtolower($rider->last_name)),
                "country" => $this->common->get_country($rider->billing_country)
            ];
            $data_rows[] = $data_row;
        }

        usort($data_rows, array($this, 'sort_by_last_first_name'));
        $output = '<table class="table table-striped">'
                . '<thead>'
                . '<tr><th>'
                . __('Name', 'LEL2017Plugin')
                . '</th><th>'
                . __('Country', 'LEL2017Plugin')
                . '</th></tr>'
                . '</thead></tbody>';

        foreach ($data_rows as $data_row) {
            $output .= '<tr>'
                    . '<td>' . $data_row["first_name"] . ' ' . $data_row["last_name"] . '</td>'
                    . '<td>' . $data_row["country"]
                    . '</tr>';
        }
        $output .= '</tbody></table>';
        return $output;
    }

    /*
     * Sort by last then first name
     */

    public function sort_by_last_first_name($a, $b) {
// last name
        if ($a["last_name"] > $b["last_name"]) {
            return 1;
        }
        if ($a["last_name"] < $b["last_name"]) {
            return -1;
        }
// first name
        if ($a["first_name"] > $b["first_name"]) {
            return 1;
        }
        if ($a["first_name"] < $b["first_name"]) {
            return -1;
        }
// keys are equal (should never happen)
        return 0;
    }

    /*
     * sort by start time and then random sort key
     */

    public function sort_by_starttime($a, $b) {
// chosen start time
        if ($a[7] > $b[7]) {
            return 1;
        }
        if ($a[7] < $b[7]) {
            return -1;
        }
// random sort key
        if ($a[6] > $b[6]) {
            return 1;
        }
        if ($a[6] < $b[6]) {
            return -1;
        }
// keys are equal (should never happen)
        return 0;
    }

    /*
     * Sort by start time, used by starting lists
     */

    public function sort_by_starttime_only($a, $b) {
// chosen start time
        if ($a[4] > $b[4]) {
            return 1;
        }
        if ($a[4] < $b[4]) {
            return -1;
        }
        // Rider Number
        if ($a[5] > $b[5]) {
            return 1;
        }
        if ($a[5] < $b[5]) {
            return -1;
        }

// keys are equal (shouldn't happen)
        return 0;
    }

// Rest route definitions

    public function rest_api_init() {
// Rider CSV export
        register_rest_route('lel/v1', '/csv/riders', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_riders'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        register_rest_route('lel/v1', '/csv/ridersbagdrops', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_riders_bag_drops'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        register_rest_route('lel/v1', '/csv/ridersframecards', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_riders_frame_cards'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        register_rest_route('lel/v1', '/csv/riderspersonaldetails', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_riders_personal_details'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        // Rider Start List CSV export
        register_rest_route('lel/v1', '/csv/ridersstartlists', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_riders_start_lists'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        // Rider Finish List
        register_rest_route('lel/v1', '/csv/ridersfinishlists', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_rider_finish_list'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );
    }

}
