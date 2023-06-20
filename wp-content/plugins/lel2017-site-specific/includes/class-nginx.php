<?php

/**
 * The nginx functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_Nginx {

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
        $this->post_types = ['page', 'post']; // post types to handle
    }

    /*
     * Purge the post from the nginx cache when it is created / updated / deleted
     */

    public function purge_cache($ID) {
        $post_type = get_post_type($ID);
        if (in_array($post_type, $this->post_types)) {
            $url = get_permalink($ID);
            $url_parsed = wp_parse_url($url);
            $purge_url = "http://"
                    . '172.31.24.210'
                    . '/purge'
                    . $url_parsed['path'];
            $purge_home_url = "http://"
                    . '172.31.24.210'
                    . '/purge'
                    . '/';
            $purge_news_url = "http://"
                    . '172.31.24.210'
                    . '/purge'
                    . '/news/';
            $headers = [
                'host' => $url_parsed['host']
            ];
            $args = [
                'headers' => $headers
            ];
            wp_remote_get($purge_url, $args);
            if ($post_type = 'post') {
                wp_remote_get($purge_home_url, $args);
                wp_remote_get($purge_news_url, $args);
            }
        }
    }

}
