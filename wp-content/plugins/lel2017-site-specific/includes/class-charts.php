<?php

/**
 * The chartfunctionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_Charts {

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
        $this->cache = 5; //  default chart cache for 5 seconds
        $this->step = 25; // default step size (km) for forecasting
        $this->common = new LEL_Common($this->plugin_name, $this->version);
    }

    public function bagDropAllocations() {


        $result = wp_cache_get('bagDropAllocations', 'Chart');
        if (false !== $result) {

            return $result;
        }

        $locations = $this->common->bagDropLocations(true);

        $labels = [];
        $IDs = [];
        $drop_1 = [];
        $drop_2 = [];
//Always work under English blog for data retrieval
        switch_to_blog(1);
        foreach ($locations as $location) {

            if (get_field('bag_drops', $location['ID'])[0] == 'Yes') {
                $labels[] = $location['post_title'];
                $IDs[] = $location['ID'];
                $drop_1[] = 0;
                $drop_2[] = 0;
            }
        }

// Now get Bag drop 1

        global $wpdb;

        $table_name = $wpdb->prefix . "usermeta";


        $sql = 'SELECT meta_value, count(*) as total FROM ' . $table_name
                . ' WHERE meta_key = "bag_drop_1"'
                . ' GROUP BY meta_value';

        $rows = $wpdb->get_results($sql);


        foreach ($rows as $row) {

            for ($i = 0; $i < count($IDs); ++$i) {
                if ($row->meta_value == $IDs[$i]) {
                    $drop_1[$i] = (int) $row->total;
                }
            }
        }
// Now get Bag drop 2
        $sql = 'SELECT meta_value, count(*) as total FROM ' . $table_name
                . ' WHERE meta_key = "bag_drop_2"'
                . ' GROUP BY meta_value';

        $rows = $wpdb->get_results($sql);
        foreach ($rows as $row) {
            for ($i = 0; $i < count($IDs); ++$i) {
                if ($row->meta_value == $IDs[$i]) {
                    $drop_2[$i] = (int) $row->total;
                }
            }
        }
// restore for language translation
        restore_current_blog();

        $drop1 = (object) [];

        $drop1->label = __('Bag Drop', 'LEL2017Plugin') . ' 1';
        $drop1->data = $drop_1;
        $drop1->backgroundColor = 'rgba(51,62,72,0.2)';
        $drop1->borderColor = 'rgba(51,62,72,1)';
        $drop1->borderWidth = 1;
// Add data to dataset collection
        $datasets = [];
        $datasets[] = $drop1;

        $drop2 = (object) [];
        $drop2->label = __('Bag Drop', 'LEL2017Plugin') . ' 2';

        $drop2->data = $drop_2;
        $drop2->backgroundColor = 'rgba(228,33,39,0.2)';
        $drop2->borderColor = 'rgba(228,33,39,1)';
        $drop2->borderWidth = 1;
        $datasets[] = $drop2;

        $result = (object) [];

        $result->type = 'bar';
        $result->data = (object) [];
        $result->data->labels = $labels;
        $result->data->datasets = $datasets;
// Set the X and Y axis options
        $xAxes = (object) [];
        $xAxes->stacked = true;
        $yAxes = (object) [];
        $yAxes->stacked = true;
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $result->options = (object) [];
        $result->options->scales = (object) [];
        $result->options->scales->xAxes = [$xAxes];
        $result->options->scales->yAxes = [$yAxes];

// Cache the result
        wp_cache_set('bagDropAllocations', $result, 'Chart', $this->cache);



        return $result;
    }

    public function riderStartTimes() {
// Try cache first
        $result = wp_cache_get('riderStartTimes', 'Chart');
        if (false !== $result) {
            return $result;
        }

        $rows = $this->common->get_start_waves();

        $labels = [];
        $IDs = [];
        $requested_places = [];
        $total_places = [];


        foreach ($rows as $row) {

            $date = strtotime($row->start_time);
            $IDs[] = intval($row->id);
            $labels[] = date('Hi', $date);
            $total_places[] = intval($row->total_places);
            $requested_places[] = 0;
        }

// Work against English blog for db access
        switch_to_blog(1);
        global $wpdb;
        $table_name = $wpdb->prefix . "usermeta";
// Now get Bag drop 2
        $sql = 'SELECT meta_value, count(*) as total FROM ' . $table_name
                . ' WHERE meta_key = "chosen_wave"'
                . ' GROUP BY meta_value';

        $rows2 = $wpdb->get_results($sql, 'ARRAY_A');

        usort($rows2, array($this, 'sort_by_meta_value_num'));


        $i = 0;
        $l = count($IDs);
        foreach ($rows2 as $row) {
            $wave_id = intval($row['meta_value']);
            while ($i < $l && $wave_id !== $IDs[$i]) {
                ++$i;
            }

// reset to start of array if rogue start time encountered;
            $i = ($i < $l ? $i : 0);
            if (intval($row['meta_value']) == $IDs[$i]) {
                $requested_places[$i] = intval($row['total']);
            }
        }



// Restore to current language site for translations
        restore_current_blog();

        $x1 = (object) [];

        $x1->label = __('Requested Places', 'LEL2017Plugin');
        $x1->data = $requested_places;
        $x1->backgroundColor = 'rgba(51,62,72,0.2)';
        $x1->borderColor = 'rgba(51,62,72,1)';
        $x1->borderWidth = 1;
// Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;

        $x2 = (object) [];
        $x2->label = __('Available Places', 'LEL2017Plugin');
        $x2->type = "line";

        $x2->pointRadius = 2;
        $x2->pointHoverRadius = 6;
        $x2->data = $total_places;
        $x2->backgroundColor = 'rgba(255,255,255,0.2)';
        $x2->borderColor = 'rgba(228,33,39,1)';
        $x2->borderWidth = 1;
        $datasets[] = $x2;

        $result = (object) [];

        $result->type = 'bar';

        $result->data = (object) [];
        $result->data->labels = $labels;
        $result->data->datasets = $datasets;

        $Axes = (object) [];
        $Axes->stacked = true;

// Cache the result 
        wp_cache_set('riderStartTimes', $result, 'Chart', $this->cache);



        return $result;
    }

// Tracking rider time in hand
    public function trackingTimeInHand($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];

        switch_to_blog(1);
        $users = get_users($args);

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => __('Rider number not recognised.', 'LEL2017Plugin')
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];
// Get privacy settings for user
        $hide_tracking = ['Registration', 'Bag Drop Here'];
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
        $labels = [];
        $time_in_hand = [];

// Now assemble chart
        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {
                $labels[] = $controls[$result['control_id']] . ' ' . __($result['action'], 'LEL2017Plugin');
                $time_in_hand[] = intval($result['time_in_hand'] / 60);
            }
        }


        $x1 = (object) [];

        $x1->label = __('Time in Hand (mins)', 'LEL2017Plugin');
        $x1->data = $time_in_hand;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;
        $x1->pointRadius = 2;
        $x1->pointHoverRadius = 6;
        $x1->type = "line";
// Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;


        $chart = (object) [];

        $chart->type = 'line';

// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 30;

        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];

        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        if (count($results) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin")
            ];
        } else {

            $result = [
                status => 'success',
                msg => __("Tracking data found", "LEL2017Plugin"),
                chart => $chart
            ];
        }

        return $result;
    }

// Tracking speed vs. time
    public function trackingSpeed($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
                msg => __('Rider number not recognised.', 'LEL2017Plugin')
            ];
            return $result;
        }
        switch_to_blog(1);
        $rider_user_id = (int) $users[0];
// Get privacy settings for user
        $hide_tracking = ['Registration', 'Bag Drop Here'];
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
        restore_current_blog();
// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);

        usort($results, array($this, 'sort_by_timestamp'));

        $labels = [];
        $speed = [];
        $speed_leg = [];

// Now assemble chart
        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {

                $labels[] = $result['timestamp'];
                $speed[] = $result['average_speed'];

                $speed_leg[] = $result['average_speed_leg'] === null ? null : $result['average_speed_leg'];
            }
        }


// Overall average speed
        $x1 = (object) [];
        $x1->label = __('Overall Average Speed (km/h)', 'LEL2017Plugin');
        $x1->data = $speed;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;
        $x1->pointRadius = 2;
        $x1->pointHoverRadius = 6;
        $x1->type = "line";

// Leg average speed
        $x2 = (object) [];
        $x2->label = __('Average Speed from last control (km/h)', 'LEL2017Plugin');

        $x2->spanGaps = true;
        $x2->fill = false;
        $x2->data = $speed_leg;

        $x2->borderColor = 'rgba(51,62,72,1)';
        $x2->borderWidth = 1;
        $x2->pointRadius = 2;
        $x2->pointHoverRadius = 6;
        $x2->type = "line";



// Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;
        $datasets[] = $x2;

        $chart = (object) [];

        $chart->type = 'line';
// xAxes
        $xAxes = (object) [];
        $xAxes->type = 'time';
        $xAxes->time = (object) [];

// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 30;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];
        $chart->options->scales->xAxes = [$xAxes];

// data and labels

        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;

        if (count($results) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin")
            ];
        } else {

            $result = [
                status => 'success',
                msg => __("Tracking data found", "LEL2017Plugin"),
                chart => $chart
            ];
        }
        return $result;
    }

    public function trackingSpeedDistance($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        switch_to_blog(1);
        $users = get_users($args);
        restore_current_blog();

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => __("Rider number not recognised.", 'LEL2017Plugin')
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];
        switch_to_blog(1);
// Get privacy settings for user
        $hide_tracking = ['Registration', 'Bag Drop Here'];


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
        restore_current_blog();
// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);

        usort($results, array($this, 'sort_by_timestamp'));

        $points = [];
        $points2 = [];


// Now assemble chart
        $prev_distance = 0;
        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {
                $point = [];
                $point['x'] = $result['distance'];
                $point['y'] = $result['average_speed'];
                $points[] = $point;

                if ($result['average_speed_leg'] !== null) {
                    $point2 = [];

                    $point2['x'] = $result['distance'];
                    $point2['y'] = $result['average_speed_leg'];
                    $points2[] = $point2;
                }
            }
        }


        $x1 = (object) [];

        $x1->label = __('Overall Average Speed (km/h)', 'LEL2017Plugin');

        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;
        $x1->pointRadius = 2;
        $x1->pointHoverRadius = 6;
        $x1->type = "line";
        $x1->data = $points;


// Leg average speed
        $x2 = (object) [];
        $x2->label = __('Average Speed from last control (km/h)', 'LEL2017Plugin');

        $x2->spanGaps = true;

        $x2->fill = false;
        $x2->data = $points2;

        $x2->borderColor = 'rgba(51,62,72,1)';
        $x2->borderWidth = 1;
        $x2->pointRadius = 2;
        $x2->pointHoverRadius = 6;
        $x2->type = "line";



        $chart = (object) [];

        $chart->type = 'line';
// xAxes
        $xAxes = (object) [];
        $xAxes->type = 'linear';
        $xAxes->position = 'bottom';
        $xAxes->ticks = (object) [];
        $xAxes->ticks->suggestedMax = 100;


// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 30;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->xAxes = [$xAxes];
        $chart->options->scales->yAxes = [$yAxes];

// data and labels

        $chart->data = (object) [];

        $chart->data->datasets[] = $x1;
        $chart->data->datasets[] = $x2;



        if (count($results) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin")
            ];
        } else {

            $result = [
                status => 'success',
                msg => __("Tracking data found", "LEL2017Plugin"),
                chart => $chart
            ];
        }
        return $result;
    }

    public function trackingElapsedDistance($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        switch_to_blog(1);
        $users = get_users($args);
        restore_current_blog();

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => __('Rider number not recognised.', 'LEL2017Plugin')
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];
        switch_to_blog(1);
// Get privacy settings for user
        $hide_tracking = ['Registration', 'Bag Drop Here'];
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
        restore_current_blog();
// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);

        usort($results, array($this, 'sort_by_timestamp'));

        $points = [];


// Now assemble chart
        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {
                $point = [];
                $point['x'] = $result['distance'];
                $point['y'] = intval($result['elapsed'] / 3600);
                $points[] = $point;
            }
        }


        $x1 = (object) [];

        $x1->label = __('Elapsed (hours)', 'LEL2017Plugin');

        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;
        $x1->pointRadius = 2;
        $x1->pointHoverRadius = 6;
        $x1->type = "line";
        $x1->data = $points;


        $chart = (object) [];

        $chart->type = 'line';
// xAxes
        $xAxes = (object) [];
        $xAxes->type = 'linear';
        $xAxes->position = 'bottom';
        $xAxes->ticks = (object) [];
        $xAxes->ticks->suggestedMax = 100;


// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->xAxes = [$xAxes];
        $chart->options->scales->yAxes = [$yAxes];

// data and labels

        $chart->data = (object) [];

        $chart->data->datasets[] = $x1;


        if (count($results) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin")
            ];
        } else {

            $result = [
                status => 'success',
                msg => __("Tracking data found", "LEL2017Plugin"),
                chart => $chart
            ];
        }
        return $result;
    }

// Tracking rider
    public function trackingDistance($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $args = [
            'role' => 'rider',
            'meta_key' => 'rider_id',
            'meta_value' => $rider_id,
            'meta_compare' => '=',
            'fields' => 'ID',
        ];
        switch_to_blog(1);
        $users = get_users($args);
        restore_current_blog();

        if (count($users) === 0) {
            $result = [
                status => 'error',
                msg => __('Rider number not recognised.', 'LEL2017Plugin')
            ];
            return $result;
        }

        $rider_user_id = (int) $users[0];
        switch_to_blog(1);

// Get privacy settings for user
        $hide_tracking = ['Registration', 'Bag Drop Here'];
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
        restore_current_blog();
// Now retrieve tracking data
        $results = $this->common->get_rider_tracking($rider_user_id);

        usort($results, array($this, 'sort_by_timestamp'));

        $labels = [];
        $distance = [];

// Now assemble chart
        foreach ($results as $result) {
            if (!in_array($result['action'], $hide_tracking)) {

                $labels[] = $result['timestamp'];
                $distance[] = $result['distance'];
            }
        }


        $x1 = (object) [];

        $x1->label = __('Distance (km)', 'LEL2017Plugin');
        $x1->data = $distance;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;
        $x1->pointRadius = 2;
        $x1->pointHoverRadius = 6;
        $x1->type = "line";
// Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;


        $chart = (object) [];

        $chart->type = 'line';
// xAxes
        $xAxes = (object) [];
        $xAxes->type = 'time';
        $xAxes->time = (object) [];


// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 100;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];
        $chart->options->scales->xAxes = [$xAxes];

// data and labels

        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;

        if (count($results) === 0) {
            $result = [
                status => 'error',
                msg => __("No tracking data found", "LEL2017Plugin")
            ];
        } else {

            $result = [
                status => 'success',
                msg => __("Tracking data found", "LEL2017Plugin"),
                chart => $chart
            ];
        }
        return $result;
    }

    // Tracking Control numbers
    public function trackingControls($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $controls_array = [];

        foreach ($posts as $post) {
            $post = (array) $post;
            $post_id = $post['ID'];
            $custom_fields = get_fields($post_id);
            $post = array_merge($post, $custom_fields);

            if ($post['mandatory'][0] === 'Yes') {

                $control = [
                    'ID' => $post['ID'],
                    'post_title' => $post['post_title'],
                    'distance_northbound' => $post['distance_northbound'] ? $post['distance_northbound'] : null,
                    'distance_southbound' => $post['distance_southbound'] ? $post['distance_southbound'] : null,
                    'sequence' => $post['sequence']
                ];
                $posts_array[] = $control;
            }
        }

        usort($posts_array, array($this->common, 'sort_by_sequence'));


        $controls = [];
        $labels = [];
        $north = [];
        $south = [];

        foreach ($posts_array as $post) {
            $controls[$post['ID']] = $post['post_title'];
            $labels[] = $post['post_title'];
            $north[] = 0;
            $south[] = 0;
        }

// Now retrieve tracking data
        $results = $this->common->get_control_tracking();

// Now assemble chart

        foreach ($results as $result) {
            $control = $controls[$result['control_id']];

            $position = intval(array_search($control, $labels));

            if ($result['direction'] === 'North') {
                $north[$position] = intval($result['total']);
            } else {
                $south[$position] = intval($result['total']);
            }
        }

        //Northbound
        $x1 = (object) [];

        $x1->label = __('Northbound', 'LEL2017Plugin');
        $x1->data = $north;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;

        // Southbound

        $x2 = (object) [];
        $x2->label = __('Southbound', 'LEL2017Plugin');

        $x2->data = $south;
        $x2->borderColor = 'rgba(51,62,72,1)';
        $x2->borderWidth = 1;



// Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;
        $datasets[] = $x2;

        $chart = (object) [];

        $chart->type = 'bar';

// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;
        $yAxes->stacked = true;

        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking Control numbers by last scan
    public function trackingControlScans($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $controls_array = [];

        foreach ($posts as $post) {
            $post = (array) $post;
            $post_id = $post['ID'];
            $custom_fields = get_fields($post_id);
            $post = array_merge($post, $custom_fields);

            if ($post['mandatory'][0] === 'Yes') {

                $control = [
                    'ID' => $post['ID'],
                    'post_title' => $post['post_title'],
                    'distance_northbound' => $post['distance_northbound'] ? $post['distance_northbound'] : null,
                    'distance_southbound' => $post['distance_southbound'] ? $post['distance_southbound'] : null,
                    'sequence' => $post['sequence']
                ];
                $posts_array[] = $control;
            }
        }

        usort($posts_array, array($this->common, 'sort_by_sequence'));


        $controls = [];
        $labels = [];
        $data = [];
        $colours = [
            'Arrival' => '#E42127',
            'Departure' => '#EA9F39',
            'Sleep Start' => '#EADF39',
            'Sleep End' => '#51EA39',
            'DNF' => '#3977EA'
        ];
        $i = 0;
        foreach ($posts_array as $post) {
            $controls[$post['ID']] = $post['post_title'];
            $labels[] = $post['post_title'];
            $data['Arrival'][$i] = 0;
            $data['Departure'][$i] = 0;
            $data['Sleep Start'][$i] = 0;
            $data['Sleep End'][$i] = 0;
            $data['DNF'][$i] = 0;
            $i++;
        }

        $events = [
            'Arrival',
            'Departure',
            'Sleep Start',
            'Sleep End',
            'DNF'];

// Now retrieve tracking data
        $results = $this->common->get_control_tracking_scans();

// Now assemble chart

        foreach ($results as $result) {
            $control = $controls[$result['control_id']];
            $position = intval(array_search($control, $labels));
            if (in_array($result['action'], $events)) {
                $data[$result['action']][$position] = intval($result['total']);
            }
        }



        // Add data to dataset collection
        $datasets = [];


        foreach ($data as $key => $item) {

            //Northbound
            $x1 = (object) [];

            $x1->label = __($key, 'LEL2017Plugin');
            $x1->data = $item;
            $x1->backgroundColor = $this->hex2rgba($colours[$key], 0.1);
            $x1->borderColor = $this->hex2rgba($colours[$key], 1);
            $x1->borderWidth = 1;
            $datasets[] = $x1;
        }


        $chart = (object) [];

        $chart->type = 'bar';

// yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;
        $yAxes->stacked = true;

        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking Control numbers
    public function trackingForecastControls($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $controls_array = [];

        foreach ($posts as $post) {
            $post = (array) $post;
            $post_id = $post['ID'];
            $custom_fields = get_fields($post_id);
            $post = array_merge($post, $custom_fields);

            if ($post['mandatory'][0] === 'Yes') {

                $control = [
                    'ID' => $post['ID'],
                    'post_title' => $post['post_title'],
                    'distance_northbound' => $post['distance_northbound'] ? $post['distance_northbound'] : null,
                    'distance_southbound' => $post['distance_southbound'] ? $post['distance_southbound'] : null,
                    'sequence' => $post['sequence']
                ];
                $controls_array[] = $control;
            }
        }

        $controls = [];
        $colours = [
            '#E42127',
            '#EA9F39',
            '#EADF39',
            '#51EA39',
            '#3977EA'
        ];
        $period = [
            'Now',
            '1 hour',
            '2 hours',
            '3 hours',
            '4 hours',
        ];



        $distance_lookup = [];
        $totals_by_time = [];
        $labels = [];

        // Sort controls into sequence
        usort($controls_array, array($this->common, 'sort_by_sequence'));

        // Build control lookup arrays
        $i = 0;
        foreach ($controls_array as $control) {

            $labels[] = $control['post_title'];
            $controls[] = $control['ID'];

            if (!empty($control['distance_northbound'])) {
                $distance = floor($control['distance_northbound'] / $this->step) * $this->step;
                $distance_lookup[$distance] = $control['ID'];
            }
            if (!empty($control['distance_southbound'])) {
                $distance = floor($control['distance_southbound'] / $this->step) * $this->step;
                $distance_lookup[$distance] = $control['ID'];
            }

            for ($j = 0; $j < count($period); $j++) {
                $totals_by_time[$j][$i] = 0;
            }
            $i++;
        }

        krsort($distance_lookup, SORT_NUMERIC);



// Now retrieve tracking data
        $results = $this->common->get_tracking_forecast();


// Now assemble chart
        // Array of times      
        $now = time();

        $times[0] = $now;
        for ($i = 1; $i < count($period); $i++) {
            $times[$i] = $now + $i * 3600;
        }


        foreach ($results as $result) {

            if ($result['action'] === 'Sleep Start' or $result['action'] === 'DNF') {
                continue;
            }
            if ($result['action'] === 'Arrival' or $result['action'] === 'Sleep End') {
                $result['timestamp'] = $result['timestamp'] + 1800; // assume another 30 mins in control
            }

            $current_control_id = $result['control_id'];


            $prev_control_id = null;
            // Work out where people will be to the nearest step distance.
            for ($i = 0; $i < count($period); $i++) {
                $elapsed_hours = round(($times[$i] - $result['timestamp'] ) / 3600);

                $distance = floor(($result['distance'] + $result['average_speed_leg'] * $elapsed_hours) / $this->step) * $this->step;
                // Have we reached next control?
                /*
                  foreach ($distance_lookup as $control_distance => $id) {
                  if ($distance >= $control_distance && $id !== $current_control_id) {
                  $control_id = $id;
                  $break;
                  }
                  }
                 */

                $control_id = $distance_lookup[$distance];
                if ($control_id) {
                    $pos = intval(array_search($control_id, $controls, true));
                    if ($control_id !== $prev_control_id) {
                        $totals_by_time[$i][$pos] = $totals_by_time[$i][$pos] + 1;
                        $result['timestamp'] = $result['timestamp'] + 1800; // 30 mins
                    }
                    $prev_control_id = $control_id;
                }
            }
        }



        // Now loop round the controls creating totals for graph
        // Add data to dataset collection
        $datasets = [];

        for ($i = count($period) - 1; $i >= 0; $i--) {
            //Totals

            $x1 = (object) [];
            $x1->label = $period[$i];
            $x1->backgroundColor = $this->hex2rgba($colours[$i], 0.05);
            $x1->borderColor = $this->hex2rgba($colours[$i], 1);
            $x1->borderWidth = 1;
            $x1->data = $totals_by_time[$i];
            $datasets[] = $x1;
        }


        $chart = (object) [];

        $chart->type = 'line';

// yAxes

        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking Control numbers
    public function trackingForecastControl($request) {
        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
        }


        $user_id = get_current_user_id();
        if ($user_id === 0) {
            $result = [];
            return $result;
        }

// Get the selected control of the user that sent update request

        $selected_control = get_user_meta($user_id, 'selected_control', true);

        if (empty($selected_control)) {
            $result = [];
            return $result;
        }


        // Get control details
        switch_to_blog(1);
        $control = get_post($selected_control, 'ARRAY_A');

        $custom_fields = get_fields($selected_control);
        $control = array_merge($control, $custom_fields);


        // Round control distances according to forecast step size
        if ($control['distance_northbound']) {
            $control['distance_northbound'] = floor($control['distance_northbound'] / $this->step) * $this->step;
        }
        if ($control['distance_southbound']) {
            $control['distance_southbound'] = floor($control['distance_southbound'] / $this->step) * $this->step;
        }

        $period = [
            'Now',
            '1 hour',
            '2 hours',
            '3 hours',
            '4 hours'
        ];


        // Build base totals array
        $totals_arrival = [];
        $totals_still_here = [];

        for ($j = 0; $j < count($period); $j++) {
            $totals_arrival[$j] = 0;
            $totals_still_here[$j] = 0;
        }

// Now assemble chart
        // Array of times
        $now = time();


        for ($i = 0; $i < count($period); $i++) {
            $times[$i] = $now + $i * 3600;
        }

        // Retrieve tracking data
        $results = $this->common->get_tracking_forecast();
        foreach ($results as $result) {


            if ($result['action'] === 'DNF') {
                continue;
            }
            if ($result['action'] === 'Sleep Start') {
                $result['timestamp'] = $result['timestamp'] + 14400; // assume another 30 mins in control
            }

            if ($result['action'] === 'Arrival' | $result['action'] === 'Sleep End') {
                $result['timestamp'] = $result['timestamp'] + 1800; // assume another 30 mins in control
            }

            $last_distance = null;
            // Work out where people will be to the nearest step distance.
            for ($i = 0; $i < count($period); $i++) {
                $elapsed_hours = round(($times[$i] - $result['timestamp'] ) / 3600);

                $distance = floor(($result['distance'] + $result['average_speed_leg'] * $elapsed_hours) / $this->step) * $this->step;

                if ($distance === $control['distance_southbound']) {

                    if ($distance !== $last_distance) {
                        $totals_arrival[$i] = $totals_arrival[$i] + 1;
                    }
                    $last_distance = $distance;

                    $result['timestamp'] = $result['timestamp'] + 1800; // 30 mins
                }
                if ($distance === $control['distance_northbound']) {

                    if ($distance !== $last_distance) {
                        $totals_arrival[$i] = $totals_arrival[$i] + 1;
                    }
                    $last_distance = $distance;

                    $result['timestamp'] = $result['timestamp'] + 1800; // 30 mins
                }
            }
        }



        // Add data to dataset collection
        $datasets = [];

        //Totals

        $x1 = (object) [];
        $x1->label = 'Rider Arrivals';
        $x1->backgroundColor = $this->hex2rgba('#E42127', 0.2);
        $x1->borderColor = $this->hex2rgba('#E42127', 1);
        $x1->borderWidth = 1;

        $x1->data = $totals_arrival;

        $datasets[] = $x1;

        $chart = (object) [];

        $chart->type = 'line';

// yAxes

        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];


        $chart->data = (object) [];
        $chart->data->labels = $period;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking active riders
    public function trackingActiveRiders($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $controls_array = [];

        foreach ($posts as $post) {
            $post = (array) $post;
            $post_id = $post['ID'];
            $custom_fields = get_fields($post_id);
            $post = array_merge($post, $custom_fields);

            if ($post['mandatory'][0] === 'Yes') {

                $control = [
                    'ID' => $post['ID'],
                    'post_title' => $post['post_title'],
                    'sequence' => $post['sequence']
                ];
                $controls_array[] = $control;
            }
        }

        // Sort controls into sequence
        usort($controls_array, array($this->common, 'sort_by_sequence'));

        $labels = [];
        $north = [];
        $south = [];
        $active = [];

        // Retrieve tracking data
        $results = $this->common->get_tracking_active();

        // Initialise base data

        foreach ($controls_array as $post) {
            $controls[$post['ID']] = $post['post_title'];
            $labels[] = $post['post_title'];
            $north[] = 0;
            $south[] = 0;
            $active[] = $results['active'];
        }





// Now assemble chart
        // Controls




        foreach ($results['controls'] as $result) {
            $control = $controls[$result['control_id']];

            $position = intval(array_search($control, $labels));

            if ($result['direction'] === 'North') {
                $north[$position] = intval($result['total']);
            } else {
                $south[$position] = intval($result['total']);
            }
        }

        //Northbound
        $x1 = (object) [];

        $x1->label = __('Northbound', 'LEL2017Plugin');
        $x1->data = $north;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;

        // Southbound

        $x2 = (object) [];
        $x2->label = __('Southbound', 'LEL2017Plugin');

        $x2->data = $south;
        $x2->backgroundColor = 'rgba(51,62,72,0.1)';
        $x2->borderColor = 'rgba(51,62,72,1)';
        $x2->borderWidth = 1;

        // active

        $x3 = (object) [];
        $x3->label = __('Riders', 'LEL2017Plugin');
        $x3->type = 'line';
        $x3->data = $active;
        $x3->backgroundColor = 'rgba(255,0,0,1)';
        $x3->borderColor = 'rgba(255,0,0,1)';
        $x3->borderWidth = 1;
        $x3->fill = false;
        $x3->borderWidth = 1;
        $x3->pointRadius = 0;





        // Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;
        $datasets[] = $x2;
        $datasets[] = $x3;

        $chart = (object) [];

        $chart->type = 'bar';

        // yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking active riders
    public function trackingActiveRidersControl($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
        }

        switch_to_blog(1);

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



        // Retrieve tracking data
        $results = $this->common->get_tracking_active();

        // Set labels and initialise data
        $labels = ['Riders', 'Northbound', 'Southbound'];

        $riders = [intval($results['active']), 0, 0];



        foreach ($results['controls'] as $result) {
            if ($result['control_id'] === $selected_control) {
                if ($result['direction'] === 'North') {
                    $riders[1] = intval($result['total']);
                } else {
                    $riders[2] = intval($result['total']);
                }
            }
        }

        //Northbound
        $x1 = (object) [];

        //$x1->label = __('Riders', 'LEL2017Plugin');
        $x1->data = $riders;
        $x1->backgroundColor = ['rgba(228,33,39,0.1)', 'rgba(51,62,72,0.1)', 'rgba(255,0,0,0.5)'];
        $x1->borderColor = ['rgba(228,33,39,1)', 'rgba(51,62,72,1)', 'rgba(255,0,0,1)'];
        $x1->borderWidth = 1;





        // Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;


        $chart = (object) [];

        $chart->type = 'bar';

        // yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];
        $chart->options->legend = (object) [];
        $chart->options->legend->display = false;


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking active riders
    public function trackingRecentRidersControl($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
        }

        $params = $request->get_params();
        $action = $params['action'];


        switch_to_blog(1);

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

        // Retrieve tracking data
        $results = $this->common->get_tracking_recent();


        foreach ($results as $result) {
            if ($result['control_id'] === $selected_control) {
                if ($result['action'] === $action) {
                    $labels[] = $result['timeint'];
                    $totals[] = intval($result['total']);
                }
            }
        }

        //Northbound
        $x1 = (object) [];

        $x1->label = __($action, 'LEL2017Plugin');
        $x1->data = $totals;
        $x1->backgroundColor = 'rgba(228,33,39,0.1)';
        $x1->borderColor = 'rgba(228,33,39,1)';
        $x1->borderWidth = 1;


        // Add data to dataset collection
        $datasets = [];
        $datasets[] = $x1;


        $chart = (object) [];

        $chart->type = 'bar';

        // yAxes
        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMin = 0;
        $yAxes->ticks->suggestedMax = 10;


        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];
        $chart->options->legend = (object) [];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    // Tracking Forecast Distance
    public function trackingForecastDistance($request) {

        if (get_option('lock_public_tracking') === 'yes') {
            $result = [
                status => 'error',
                msg => 'Online tracking is now locked.'
            ];
            return $result;
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
        $controls_array = [];

        foreach ($posts as $post) {
            $post = (array) $post;
            $post_id = $post['ID'];
            $custom_fields = get_fields($post_id);
            $post = array_merge($post, $custom_fields);

            if ($post['mandatory'][0] === 'Yes') {

                $control = [
                    'ID' => $post['ID'],
                    'post_title' => $post['post_title'],
                    'distance_northbound' => $post['distance_northbound'] ? $post['distance_northbound'] : null,
                    'distance_southbound' => $post['distance_southbound'] ? $post['distance_southbound'] : null,
                    'sequence' => $post['sequence']
                ];
                $controls_array[] = $control;
            }
        }

        $controls = [];
        $colours = [
            '#E42127',
            '#EA9F39',
            '#EADF39',
            '#51EA39',
            '#3977EA',
            '#9939EA'
        ];
        $period = [
            'Now',
            '1 hour',
            '2 hours',
            '3 hours',
            '4 hours'
        ];



        $distance_lookup = [];
        $control_distances = [];
        $totals_by_distance = [];
        $labels = [];

        // Sort controls into sequence
        usort($controls_array, array($this->common, 'sort_by_sequence'));

        // Build control lookup arrays

        foreach ($controls_array as $control) {


            $controls[$control['ID']] = $control['post_title'];

            if (!empty($control['distance_northbound'])) {
                $distance = floor($control['distance_northbound'] / $this->step) * $this->step;
                $distance_lookup[$distance] = $control['ID'];
                $control_distances[] = $distance;
            }
            if (!empty($control['distance_southbound'])) {
                $distance = floor($control['distance_southbound'] / $this->step) * $this->step;
                $distance_lookup[$distance] = $control['ID'];
                $control_distances[] = $distance;
            }
        }


// Now assemble chart
        // Array of times
        $now = time();

        $times[0] = $now;
        for ($i = 1; $i < count($period); $i++) {
            $times[$i] = $now + $i * 3600;
        }
        // Generate distance labels
        $max_distance = max($control_distances);
        $distances = range(0, $max_distance, $this->step);

        // Generate default zero totals for each distance
        for ($i = 0; $i < count($period); $i++) {
            $j = 0;
            foreach ($distances as $distance) {
                $totals_by_distance[$i][$j] = 0;
                $j++;
            }
        }

        // Retrieve tracking data
        $results = $this->common->get_tracking_forecast();

        foreach ($results as $result) {

            if ($result['action'] === 'Sleep Start' or $result['action'] === 'DNF') {
                continue;
            }
            if ($result['action'] === 'Arrival' or $result['action'] === 'Sleep End') {
                $result['timestamp'] = $result['timestamp'] + 1800; // assume another 30 mins in control
            }
            // Work out where people will be to the nearest step distance.
            $last_pos = null;
            for ($i = 0; $i < count($period); $i++) {
                $elapsed_hours = round(($times[$i] - $result['timestamp'] ) / 3600);

                $distance = floor(($result['distance'] + $result['average_speed_leg'] * $elapsed_hours) / $this->step) * $this->step;

                $pos = array_search($distance, $distances, true);

                if ($pos) {
                    if ($pos !== $last_pos) {
                        $totals_by_distance[$i][$pos] = $totals_by_distance[$i][$pos] + 1;
                        $last_pos = $pos;

                        if (isset($distance_lookup[$pos])) {
                            $result['timestamp'] = $result['timestamp'] + 1800; // 15 mins
                        }
                    }
                }
            }
        }

        $labels = $distances;
        for ($i = 0; $i < count($labels); $i++) {
            $control_id = $distance_lookup[$distances[$i]];
            if ($control_id) {
                $labels[$i] = $controls[$control_id];
            } else {
                $labels[$i] = $labels[$i] . ' km';
            }
        }



        // Now loop round the controls creating totals for graph
        // Add data to dataset collection
        $datasets = [];

        for ($i = count($period) - 1; $i >= 0; $i--) {
            //Totals

            $x1 = (object) [];
            $x1->label = $period[$i];
            $x1->backgroundColor = $this->hex2rgba($colours[$i], 0.05);
            $x1->borderColor = $this->hex2rgba($colours[$i], 1);
            $x1->borderWidth = 1;

            $x1->data = $totals_by_distance[$i];
            $datasets[] = $x1;
        }


        $chart = (object) [];

        $chart->type = 'line';


// yAxes

        $yAxes = (object) [];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->suggestedMax = 10;

        $xAxes = (object) [];
        $xAxes->ticks = (object) [];
        $xAxes->ticks->autoSkip = false;



        $chart->options = (object) [];
        $chart->options->scales = (object) [];
        $chart->options->scales->yAxes = [$yAxes];
        $chart->options->scales->xAxes = [$xAxes];


        $chart->data = (object) [];
        $chart->data->labels = $labels;
        $chart->data->datasets = $datasets;


        count($results) === 0 ? $result = [] : $result = $chart;


        return $result;
    }

    /*
     * Time in hand across all riders
     */

    public function ridersTimeinHand() {
// Try cache first
        $result = wp_cache_get('ridersTimeinHand', 'Chart');
        if (false !== $result) {
            return $result;
        }

        $rows = $this->common->get_tracking_riders();

        $datasets = [];
        $last_rider_id = 0;
        $finished = false;



        foreach ($rows as $row) {

            if ($row->rider_id !== $last_rider_id) {

                if ($last_rider_id !== 0) {
                    $line = (object) [];

                    $line->pointRadius = 0;
                    $line->pointHoverRadius = 0;

                    if ($finished) {
                        $line->borderColor = 'rgba(228,33,39,1)';
                        $line->borderWidth = 0.15;
                    } else {
                        $line->borderColor = 'rgba(51,62,72,1)';
                        $line->borderWidth = 0.15;
                    }
                    $line->fill = false;
                    $line->data = $data;
                    $datasets[] = $line;
                }
                $last_rider_id = $row->rider_id;
                $data = [];
                $last_distance = -999;
                $finished = false;
            }
            $time_in_hand = (float) $row->time_in_hand;
            //$time_in_hand = ($time_in_hand > 15 ? 15 : $time_in_hand);
            //$time_in_hand = ($time_in_hand < -15 ? -15 : $time_in_hand);

            $distance = (int) $row->distance;

            if ($distance !== $last_distance) {
                $data[] = [x => $distance, y => $time_in_hand];
                $last_distance = $distance;
            }
            $last_distance = $distance;




            if ($distance > 1400 and $time_in_hand >= 0) {
                $finished = true;
            }
        }


        $result = (object) [];

        $result->type = 'scatter';
        $result->options = (object) [];
        $result->options->legend = (object) [];
        $result->options->legend->display = false;
        $result->options->tooltips = (object) [];
        $result->options->tooltips->enabled = false;

        $yAxes = (object) [];
        $yAxes->scaleLabel = (object) [
                    display => true,
                    labelString => 'Time in Hand (hours)'
        ];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->max = 15;
        $yAxes->ticks->min = -15;


        $xAxes = (object) [];
        $xAxes->scaleLabel = (object) [
                    display => true,
                    labelString => 'Distance (km)'
        ];
        $xAxes->type = 'linear';
        $xAxes->ticks = (object) [];
        $xAxes->ticks->max = 1500;


        $result->options->scales->yAxes = [$yAxes];
        $result->options->scales->xAxes = [$xAxes];

        $result->data = (object) [];

        $result->data->datasets = $datasets;


// Cache the result
        wp_cache_set('riderTimeinHand', $result, 'Chart', $this->cache);



        return $result;
    }

    /*
     * Time in hand across all riders
     */

    public function ridersElapsed() {
// Try cache first
        $result = wp_cache_get('ridersElapsed', 'Chart');
        if (false !== $result) {
            return $result;
        }

        $rows = $this->common->get_tracking_riders();

        $datasets = [];
        $last_rider_id = 0;
        $finished = false;



        foreach ($rows as $row) {

            if ($row->rider_id !== $last_rider_id) {

                if ($last_rider_id !== 0) {
                    $line = (object) [];

                    $line->pointRadius = 0;
                    $line->pointHoverRadius = 0;

                    if ($finished) {
                        $line->borderColor = 'rgba(228,33,39,1)';
                        $line->borderWidth = 0.15;
                    } else {
                        $line->borderColor = 'rgba(51,62,72,1)';
                        $line->borderWidth = 0.15;
                    }
                    $line->fill = false;
                    $line->data = $data;
                    $datasets[] = $line;
                }
                $last_rider_id = $row->rider_id;
                $data = [];
                $last_distance = -999;
                $finished = false;
            }
            $time_in_hand = (float) $row->time_in_hand;
            $elapsed = (float) $row->elapsed / 3600;

            $distance = (int) $row->distance;

            if ($distance !== $last_distance) {
                $data[] = [x => $distance, y => $elapsed];
                $last_distance = $distance;
            }
            $last_distance = $distance;




            if ($distance > 1400 and $time_in_hand >= 0) {
                $finished = true;
            }
        }


        $result = (object) [];

        $result->type = 'scatter';
        $result->options = (object) [];
        $result->options->legend = (object) [];
        $result->options->legend->display = false;
        $result->options->tooltips = (object) [];
        $result->options->tooltips->enabled = false;

        $yAxes = (object) [];
        $yAxes->scaleLabel = (object) [
                    display => true,
                    labelString => 'Elapsed (hours)'
        ];
        $yAxes->ticks = (object) [];
        $yAxes->ticks->max = 125;
        $yAxes->ticks->min = 0;


        $xAxes = (object) [];
        $xAxes->scaleLabel = (object) [
                    display => true,
                    labelString => 'Distance (km)'
        ];
        $xAxes->type = 'linear';
        $xAxes->ticks = (object) [];
        $xAxes->ticks->max = 1500;


        $result->options->scales->yAxes = [$yAxes];
        $result->options->scales->xAxes = [$xAxes];

        $result->data = (object) [];

        $result->data->datasets = $datasets;


// Cache the result
        wp_cache_set('riderElapsed', $result, 'Chart', $this->cache);



        return $result;
    }

    /* Convert hexdec color string to rgb(a) string */

    function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';


//Return default if no color provided
        if (empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }

    public function bubble_chart($atts) {

        extract(shortcode_atts(array(
            'chart' => 'ridersByCountry',
            'female' => 'no')
                        , $atts));
        $nonce = wp_create_nonce('myajax-' . $chart);
        $action = '?action=' . $chart . '&female=' . $female . '&verify=' . $nonce;
        /*
         * Generate random div id to avoid clashes if added more than once
         */
        $length = 10;
        $divid = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);




        add_action('wp_enqueue_scripts', wp_enqueue_style('lel-charts'));
        add_action('wp_enqueue_scripts', wp_enqueue_script('lel-bubble'));

        wp_localize_script('lel-bubble', 'wp', array(
// URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url('admin-ajax.php')
                )
        );



        $script = '<script type="text/javascript">';
        $script .= 'jQuery(document).ready(function() {lel_bubble("#' . $divid . '" , "' . $action . '")});';
        $script .= '</script>';
        return $script . '<div id="' . $divid . '"></div>';
    }

    public function world_map() {
        wp_enqueue_style('lel-charts');
        wp_enqueue_script('lel-world');

        return '<div id="world-map"></div>';
    }

    public function ridersByCountry() {
// In our file that handles the request, verify the nonce.

        $nonce = $_REQUEST['verify'];

        if (!wp_verify_nonce($nonce, 'myajax-ridersByCountry')) {

            wp_send_json([
                'title' => 'Failed security validation!',
                'value' => 0
            ]);
        } else {

            global $wpdb;
            /*
             * SQL for selecting female only entries
             */
            $female = sanitize_text_field($_GET['female']);
            $femaleSql = ' AND user_id IN (SELECT user_id FROM ' . $wpdb->dbname . '.wp_usermeta where meta_key = "gender" and meta_value = "female" ) ';
            $addWhere = ($female === 'yes' ? $femaleSql : ' ');
            /*
             * Get full country name
             */
            $addCountry = wp_mlp_languages;

            $sql = 'SELECT meta_value as title, b.name as full_title , count(*) as value FROM ' . $wpdb->dbname . '.wp_usermeta a'
                    . ' LEFT JOIN ' . $wpdb->dbname . '.countries b '
                    . 'ON a.meta_value = b.iso '
                    . ' WHERE meta_key = "billing_country" AND user_id > 9 and user_id < 525'
                    . $addWhere . 'GROUP BY meta_value';

            $posts = $wpdb->get_results($sql);
            wp_send_json($posts);
        }
    }

    public function js_chart($atts) {

        $args = shortcode_atts(array(
            'chart' => 'chart/bagdropallocations',
            'listen_events' => ' ',
            'auto' => 'yes',
            'public' => ''
                ), $atts);

        $wp_rest = wp_create_nonce('wp_rest');

        /*
         * Generate random div id to avoid clashes if added more than once
         */
//$length = 10;
//$divid = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

        $divid = str_replace('/', '', $args['chart']);

        wp_register_script($args['chart'], plugins_url('../js/js-chart.js', __FILE__), array('js-chart'), '', true);




        wp_localize_script($args['chart'], 'wp_chart', array(
            'divid' => '#' . $divid,
            'resturl' => get_rest_url() . 'lel/v1/' . ($args['public'] !== '' ? $args['public'] . '/' : '') . 'chart/' . $args['chart'],
            'wp_rest' => $wp_rest,
            'auto' => $args['auto'],
            'listen_events' => [$args['listen_events']]
                )
        );


        add_action('wp_enqueue_scripts', wp_enqueue_script($args['chart']));

        return '<canvas id="' . $divid . '"></canvas>';
    }

    public function register_scripts() {

        wp_register_script('d3', plugins_url('../js/d3.js', __FILE__), array('jquery'), '', true);
        wp_register_script('jschart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js', array('jquery'), '2.6.0', true);
        wp_register_script('d3-tips', plugins_url('../js/d3.tip.v0.6.3.js', __FILE__), array('d3'), '', true);

        wp_register_script('topojson.d3', plugins_url('../js/topojson.d3.js', __FILE__), array('d3'), '', true);
// My chart scripts
        wp_register_script('c3', plugins_url('../js/c3.min.js', __FILE__), array('d3'), '', true);

        wp_register_script('lel-bubble', plugins_url('../js/bubble-chart.js', __FILE__), array('d3'), '', true);
        wp_register_script('d3-barchart', plugins_url('../js//flight-components/D3BarChartComponent.js', __FILE__), array('c3', 'd3-tips', 'flight'), '', true);
        wp_register_script('js-chart', plugins_url('../js//flight-components/jsChartComponent.js', __FILE__), array('jschart', 'flight'), '2.3', true);

        wp_register_script('lel-bar', plugins_url('../js/bar-chart.js', __FILE__), array('d3-barchart'), '', true);
        wp_register_script('lel-js-chart', plugins_url('../js/js-chart.js', __FILE__), array('js-chart'), '1.2', true);

        wp_register_script('lel-world', plugins_url('../js/world-map.js', __FILE__), array('topojson.d3'), '', true);
    }

    public function register_styles() {
        wp_register_style('lel-charts', plugins_url('../css/lel-charts.css', __FILE__), '', '', false);
        wp_register_style('c3-css', plugins_url('../css/c3.min.css', __FILE__), '', '', false);
    }

    public function rest_api_init() {
// Bag Drop Allocations
        register_rest_route('lel/v1', '/chart/bagdropallocations/', array(
            'methods' => 'GET',
            'callback' => [$this, 'bagDropAllocations'],
            'permission_callback' => function () {
        return current_user_can('access_rider_area');
    }
        ));
// Rider Wave Places
        register_rest_route('lel/v1', '/chart/riderstarttimes/', array(
            'methods' => 'GET',
            'callback' => [$this, 'riderStartTimes'],
            'permission_callback' => function () {
        return current_user_can('access_rider_area');
    }
        ));
// tracking


        register_rest_route('lel/v1', '/public/chart/tracking/timeinhand/rider/(?P<rider>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingTimeInHand'],
            'permission_callback' => [$this, 'valid_nonce']
        ));

        register_rest_route('lel/v1', '/public/chart/tracking/distance/rider/(?P<rider>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingDistance'],
            'permission_callback' => [$this, 'valid_nonce']
        ));

        register_rest_route('lel/v1', '/public/chart/tracking/speed/rider/(?P<rider>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingSpeed'],
            'permission_callback' => [$this, 'valid_nonce']
        ));

        register_rest_route('lel/v1', '/public/chart/tracking/speeddistance/rider/(?P<rider>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingSpeedDistance'],
            'permission_callback' => [$this, 'valid_nonce']
        ));

        register_rest_route('lel/v1', '/public/chart/tracking/elapseddistance/rider/(?P<rider>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingElapsedDistance'],
            'permission_callback' => [$this, 'valid_nonce']
        ));

        register_rest_route('lel/v1', '/public/chart/tracking/controls', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingControls'],
            'permission_callback' => [$this, 'valid_nonce']
        ));


        register_rest_route('lel/v1', '/chart/tracking/controls', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingControls'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));

        register_rest_route('lel/v1', '/chart/tracking/controlscans', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingControlScans'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));
        register_rest_route('lel/v1', '/chart/tracking/controls/forecast', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingForecastControls'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));


        register_rest_route('lel/v1', '/chart/tracking/control/forecast', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingForecastControl'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));
        register_rest_route('lel/v1', '/chart/tracking/distance/forecast', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingForecastDistance'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));
        register_rest_route('lel/v1', '/chart/tracking/active', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingActiveRiders'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));
        register_rest_route('lel/v1', '/chart/tracking/active/control', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingActiveRidersControl'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));
        register_rest_route('lel/v1', '/chart/tracking/recent/control/(?P<action>[\w]+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'trackingRecentRidersControl'],
            'permission_callback' => function () {

        if (current_user_can('access_tracking_area') | current_user_can('access_registration_area')) {
            return true;
        }
    }
        ));

        // Time in hand
        register_rest_route('lel/v1', '/cache/chart/riderstimeinhand/', array(
            'methods' => 'GET',
            'callback' => [$this, 'ridersTimeinHand']
        ));
        // Elapsed
        register_rest_route('lel/v1', '/cache/chart/riderselapsed/', array(
            'methods' => 'GET',
            'callback' => [$this, 'ridersElapsed']
        ));
    }

// Check rest request came with valid nonce
    public function valid_nonce($request) {
// Check Nonce
        $headers = $request->get_headers();
        $nonce = $headers['x_wp_nonce'][0];
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }
        return true;
    }

    private function sort_by_meta_value_num($a, $b) {

        return (int) $a['meta_value'] - (int) $b['meta_value'];
    }

    private function sort_by_timestamp($a, $b) {

        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
    }

}
