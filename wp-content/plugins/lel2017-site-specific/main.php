<?php

/**
 * The plugin core file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             0.4
 * @package           lel2017
 * @author            Phil Whitehurst
 *
 * @wordpress-plugin
 * Plugin Name:       LEL 2017 
 * Description:       London Edinburgh London specific functionality
 * Version:           1.1
 * Author:            Phil Whitehurst
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       LEL2017Plugin
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lel-single-order-activator.php
 */
function activate_lel_2017() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
    LEL_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lel-single-order-deactivator.php
 */
function deactivate_lel_2017() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-deactivator.php';
    LEL_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_lel_2017');
register_deactivation_hook(__FILE__, 'deactivate_lel_2017');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-main.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_lel_2017() {

    $plugin = new LEL_2017('LEL2017Plugin', 1.1);
    $plugin->run();
}

run_lel_2017();
