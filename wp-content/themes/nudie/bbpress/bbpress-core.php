<?php
/**
 * File Security Check
 */
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}

/**
 * Check if bbpress is active
 **/
if ( in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Run on init
	 */
	function bbpress_core_on_init() {

		add_filter( 'bbp_default_styles', 'maybe_load_bbpress_styles' );
		add_action( 'template_redirect', 'bbpress_one_column' );
		add_filter( 'is_bbpress', 'bbpress_page_template_checks' );
		add_filter( 'bbp_show_lead_topic', '__return_true' );
		add_filter( 'bbp_before_get_user_subscribe_link_parse_args', 'bbpress_remove_pipe_before');

		remove_filter( 'wp_title', 'hybrid_wp_title', 1 );
		add_filter( 'wp_title', 'hybrid_wp_title', 10, 3 );

		add_filter( 'hybrid_content_template_hierarchy', 'bbpress_filter_hybrid_templates' );

	}

	add_action( 'widgets_init', 'bbpress_sidebar' );
	add_action( 'init', 'bbpress_core_on_init' );

}

/**
 * Only load the bbpress styles on bbpress pages
 */
function maybe_load_bbpress_styles( $styles ){

	//wp_dequeue_style( 'bbp-default-bbpress' );
	//wp_deregister_style( 'bbp-default-bbpress' );

    if ( is_bbpress() )
	//	return $styles;
		return array();
	else
		return array();

}


/**
 * Add bbpress sidebar
 */
function bbpress_sidebar() {

	/* Get the theme textdomain. */
	$domain = hybrid_get_parent_textdomain();

	register_sidebar(array(
			'name' =>	_x( 'bbPress', 'sidebar', $domain ),
			'id' => 'bbpress',
			'description' =>	__( 'The main (primary) widget area loaded within the bbPress section of the site.', $domain ),
			'before_widget' => 	'<div id="%1$s" class="widget %2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => 		'</div></div>',
			'before_title' => 		'<h3 class="widget-title">',
			'after_title' => 		'</h3>'
	));
		
}

/**
 * Disable sidebar if no widgets
 */
function bbpress_one_column() {

    if ( is_bbpress() && ! is_active_sidebar( 'bbpress' ) ) {

        add_filter( 'get_theme_layout', 'my_theme_layout_one_column' );

    }

}

/**
 * Add test for bbpress page templates to is_bbpress()
 */
function bbpress_page_template_checks( $retval ) {

	if ( is_page_template('bbpress/page-create-topic.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-forum-statistics.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-front-forums.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-front-topics.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-topic-tags.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-topics-no-replies.php') ) {
		$retval = true;

	} elseif ( is_page_template('bbpress/page-user-login.php') ) {
		$retval = true;
		
	} elseif ( is_page_template('bbpress/page-user-lost-pass.php') ) {
		$retval = true;
		
	} elseif ( is_page_template('bbpress/page-user-register.php') ) {
		$retval = true;
	}

	return (bool) $retval;

}

/**
 * Remove default before "pipe" character
 */
function bbpress_remove_pipe_before( $args ) {

	$args['before'] = '';

	return $args;

}

/**
 * Filter bbpress search templates
 */
function bbpress_filter_hybrid_templates( $templates ) {

	if ( bbp_is_search() ) {
		$templates = array( 'bbpress/content-search.php' );
	}

	return $templates;

}