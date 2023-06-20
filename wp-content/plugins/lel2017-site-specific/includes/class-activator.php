<?php

/**
 * Fired during plugin activation
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author Phil Whitehurst
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class LEL_Activator {

    /**
     * Code to executed during plugin activation
     *
     * Call all the functions required during plugin activation
     *
     * @since    1.0.0
     */
    public static function activate() {

        self::add_role_caps();
        self::add_options();
        self::create_tables();
    }

    function add_role_caps() {
        // gets the controller role
        $role = get_role('controller');
        $role->add_cap('access_volunteer_area');
        $role->add_cap('access_tracking_area');
        // gets the volunteer role
        $role = get_role('volunteer');
        $role->add_cap('access_volunteer_area');
        // gets the volunteer role
        $role = get_role('tracking');
        $role->add_cap('access_volunteer_area');
        $role->add_cap('access_tracking_area');


        // get rider role
        $role = get_role('rider');
        $role->add_cap('access_rider_area');
    }

    /*
     * Add site wide options
     */

    private function create_tables() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        /*
         * Tracking table
         */
        $table_name = $wpdb->prefix . "tracking";



        $sql = "CREATE TABLE $table_name (
  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  user_id BIGINT(20) NOT NULL,
  rider_id CHAR(4) NOT NULL,
  control_id BIGINT(20) NOT NULL,
  action CHAR(20) NOT NULL,
  time_in_hand INT(10) NOT NULL DEFAULT 0,
  average_speed DECIMAL(5,2) NOT NULL DEFAULT 0,
  average_speed_leg DECIMAL(5,2) NOT NULL DEFAULT 0,
  elapsed INT(10) NOT NULL DEFAULT 0,
  distance INT(4) NOT NULL DEFAULT 0,
  direction CHAR(5) NOT NULL,
  IP CHAR(32) NOT NULL

  PRIMARY KEY (id),
  KEY time (timestamp),
  KEY rider (rider_id),
  KEY control (control_id)

) $charset_collate;";


        dbDelta($sql);


        /*
         * Start Waves table
         */
        $table_name = $wpdb->prefix . "startwaves";



        $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  total_places INT NOT NULL,
  start_time DATETIME NOT NULL,
  description VARCHAR(40) NOT NULL,
  time_limit VARCHAR(20) NOt NULL
  UNIQUE KEY id (id)
) $charset_collate;";


        dbDelta($sql);
    }

}
