<?php

/**
 * The Ninja Forms functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_Ninja {

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
    }

    /**
     * Add custom actions types to Ninja Forms
     *
     * @since     1.0.0
     * @return    null
     */
    public function custom_nf_actions($types) {

        //  $types['subscription'] = require_once( plugin_dir_path(dirname(__FILE__)) . '/includes/class_nf_action_subscribe.php' );

        return $types;
    }

}
