<?php

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// Caching

define('WP_CACHE', true);

// Object Cache (memcached)
// tolmanz memcached plugin
global $memcached_servers;
// Entry per server
$memcached_servers = array(
    array(
        '172.31.24.210', //  web server 1 Memcached server IP address
        11211        // Memcached server port
    )
);
// WordPress memory limit
define('WP_MEMORY_LIMIT', '312M');


// Disable admin edits of themes (SECURITY)
define('DISALLOW_FILE_EDIT', true);

// Cron settings
define('DISABLE_WP_CRON', true);

if (file_exists(dirname(__FILE__) . '/local-config.php')) {
    include( dirname(__FILE__) . '/local-config.php' );
    define('WP_LOCAL_DEV', true);
} else {
// ** MySQL settings - You can get this info from your web host ** //
//** The name of the database for WordPress */

    define('DB_NAME', 'lel2017_wordpress_w3Tr5L');

    /** MySQL database username  - aws */
    define('DB_USER', 'awsdblel2017live');

    /** MySQL database password */
    define('DB_PASSWORD', '2Ng51V6D4P');


    /** MariaDB Hostname * */
    define('DB_HOST', 'lel2017-mariadb-wordpress.cdmzd9b5dvw1.eu-west-1.rds.amazonaws.com');


    /** Database Charset to use in creating database tables. */
    define('DB_CHARSET', 'utf8');

    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');
}
/* * #@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '{~X0~%5GQ^X^+v?g85V#@pb)&a]GCO_I/M4-3Gxg(-(-|z5RxIk?xE90|^jt+Y?F');
define('SECURE_AUTH_KEY', 'LaN=1y-M=s2D`6_krwQ- k/XK6d%5oeqLRrCTsa5M{qQen(9XkEb5*}=b1Uj:4:,');
define('LOGGED_IN_KEY', '7j0[`jE?94E7M=SX,X@dyf2DM|=.U)|mW1m_^S|.a|Xe5E|43HV4`0U-Lk9mXn0I');
define('NONCE_KEY', '?WQWBQ-olt43;W5COE *6#cZVP9aiOL|y|wIL6+waS7N~m>8DuiWb+#L62!Nb &A');
define('AUTH_SALT', 'e4Xi<L@ejr w|gfV9A93}ex:fzb5<A.kX~8~8w.`^EiM|?U9r(mRaXJ!!ZHL5`||');
define('SECURE_AUTH_SALT', '1b1dcc+/B!/PY:&Oyjcq9LUP^&^O|lC^h/3y6|QcbEz!+*?|@3lkR5?)gqxzP=$U');
define('LOGGED_IN_SALT', 'q/dBq|UVV{}O`vZJX_t f~I!`/{;b<]r|u/>;Xb5[8f{2wd~n,fuYxHzc82.FB~`');
define('NONCE_SALT', 'DU%K[nCM^6^(.04>^|Q{ocke#a-~;tlf.Be/:}?_h//y^t=L@[aEw|92Z1|e?G+!');

/* * #@- */

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
//define('WP_DEBUG', true);
//define('WP_DEBUG_LOG', true);
//define('WP_DEBUG_DISPLAY', false);



define('WP_POST_REVISIONS', 10);

/*
 * Language
 */

define('WPLANG', 'en_GB');


/* Multisite */
define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);


if (defined('WP_LOCAL_DEV')) {

    define('DOMAIN_CURRENT_SITE', 'lel2017.test');
} else {
    define('DOMAIN_CURRENT_SITE', 'londonedinburghlondon.com');
}

define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

/* force ssl */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $_SERVER['HTTPS'] = 'on';

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

# Disables all core updates. 
define('WP_AUTO_UPDATE_CORE', false);
