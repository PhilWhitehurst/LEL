<?php
/**
 * File Security Check
 */
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}

/**
 * Set theme support for woocommerce
 */
add_theme_support( 'woocommerce' );

/**
 * Don't use default css
 */
if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
} else {
	define( 'WOOCOMMERCE_USE_CSS', false );
}

add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Run on init
	 */
	function woocommerce_core_on_init() {
		
		add_filter( 'breadcrumb_trail_items', 'woocommerce_core_breadcrumb_trail_items', 2 , 10);
		add_filter( 'image_size_names_choose', 'woocommerce_core_remove_image_size_options' );
		add_action( 'template_redirect', 'woocommerce_core_hybrid_layout' );

		add_filter( 'loop_shop_columns', 'loop_columns', 999);
		add_filter( 'woocommerce_cross_sells_columns', 'loop_columns', 999 );
		add_filter ( 'woocommerce_product_thumbnails_columns', 'thumb_cols', 999 );
		
		/**
		 * Set limit for number of related products to show
		 */
		add_filter( 'woocommerce_related_products_args', 'woo_related_products_limit' );
		add_filter( 'woocommerce_product_related_posts', 'woo_related_products_limit' );

	}

	add_action( 'widgets_init', 'woocommerce_core_sidebar' );
	add_action( 'init', 'woocommerce_core_on_init' );
	add_action( 'after_switch_theme', 'woocommerce_image_dimensions', 1 );
	add_action( 'woocommerce_init', 'woocommerce_loaded_function' );

	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	remove_action( 'woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );
	add_action( 'woocommerce_review_comment_text', 'woocommerce_core_review_display_comment_text', 10 );
}


/**
 * Override theme default specification for product # per row
 */
function loop_columns() {
	return apply_filters( 'nudie_loop_columns', 4 ); // 4 products per row
}

/**
 * Override theme default specification for thumbnail # per row
 */
function thumb_cols() {
	return apply_filters( 'nudie_thumbnail_columns', 3 ); // 3 products per row
}

/**
 * Override the number of related products to display
 */
function woo_related_products_limit( $args ) {
	$args['posts_per_page'] = 4;
	return $args;
}

/**
 * 	Remove WC generator tag from <head>
 */
function woocommerce_loaded_function() {
 
	remove_action( 'wp_head', 'wc_generator_tag' );

}

/**
 * Add shop sidebar
 */
function woocommerce_core_sidebar() {

	/* Get the theme textdomain. */
	$domain = hybrid_get_parent_textdomain();

	register_sidebar(array(
			'name' =>	_x( 'WooCommerce', 'sidebar', $domain ),
			'id' => 'woocommerce',
			'description' =>	__( 'The main (primary) widget area loaded within the WooCommerce store component of the site.', $domain ),
			'before_widget' => 	'<div id="%1$s" class="widget %2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => 		'</div></div>',
			'before_title' => 		'<h3 class="widget-title">',
			'after_title' => 		'</h3>'
	));
		
}


/**
 * Remove image sizes from the list of available sizes in the media uploader
 */	
function woocommerce_core_remove_image_size_options( $sizes ){
 
	unset($sizes['shop_catalog']);
	unset($sizes['shop_thumbnail']);
	unset($sizes['shop_single']);
	return $sizes;
 
}

/**
 * Gets the items for the breadcrumb trail if woocommerce is installed.
 *
 */
function woocommerce_core_breadcrumb_trail_items( $trail = array() , $args = array() ) {

	global $post, $wp_query;

	$permalinks   = get_option( 'woocommerce_permalinks' );
	$shop_page_id = wc_get_page_id( 'shop' );
	$shop_page    = get_post( $shop_page_id );
	
	if ( is_woocommerce() )	{

		/* Set up a new items */
		$trail = array();

		if ( ! is_front_page() ) {
			$trail[] = '<a href="' . apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">Home</a>';
		}

		if ( ! is_front_page() && is_shop() ) {
			if ( get_query_var( 'paged' ) ) {
				$trail[] = '<a href="' . get_permalink( $shop_page ) . '">' . $shop_page->post_title . '</a>';
			} else {
				$trail[] = $shop_page->post_title;
			}
		}

		/* If permalinks contain the shop page in the URI prepend the breadcrumb with shop */
		if ( $shop_page_id && $shop_page && ! is_shop() && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && get_option( 'page_on_front' ) != $shop_page_id ) {

			if ( is_product_category() || is_product_tag() || is_product() ) {
				$trail[] = '<a href="' . get_permalink( $shop_page ) . '">' . $shop_page->post_title . '</a>';
			} else {
				$trail[] = $shop_page->post_title;
			}
		
		}
	}

	if ( is_account_page() ) {

		array_pop ( $trail );

		$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
		
		if ( $myaccount_page_id ) {
			$myaccount_page_url = get_permalink( $myaccount_page_id );
			$myaccount_page_title = get_the_title( $myaccount_page_id );
		}

		if ( is_wc_endpoint_url() ) {
			$trail[] = '<a href="' . $myaccount_page_url . '" title="' . esc_attr( $myaccount_page_title ) . '" rel="home" class="trail-begin">' . esc_attr( $myaccount_page_title ) . '</a>';
		} else {
			$trail[] = esc_attr( $myaccount_page_title );
		}

		if ( is_wc_endpoint_url() && ( $endpoint = WC()->query->get_current_endpoint() ) && ( $endpoint_title = WC()->query->get_endpoint_title( $endpoint ) ) ) {
			$trail[] = $endpoint_title;
		}

		// get around in_the_loop() https://github.com/Prospress/woocommerce-subscriptions/blob/2134e1861dbdcd7c53d4a550c65f27f181f3379e/includes/class-wcs-query.php#L58
		if ( is_wc_endpoint_url() && ( $endpoint = WC()->query->get_current_endpoint() ) && ( $endpoint == 'view-subscription' ) ) {
			global $wp;
			$subscription = wcs_get_subscription( $wp->query_vars['view-subscription'] );
			$trail[]      = ( $subscription ) ? sprintf( _x( 'Subscription #%s', 'hash before order number', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) : '';
		}
	}
	
	/* If is product category archive */		
	if ( is_product_category() ) {

		$current_term = $wp_query->get_queried_object();

		$ancestors = array_reverse( get_ancestors( $current_term->term_id, 'product_cat' ) );

		foreach ( $ancestors as $ancestor ) {

			$ancestor = get_term( $ancestor, 'product_cat' );

			$trail[] = '<a href="' . get_term_link( $ancestor->slug, 'product_cat' ) . '">' . esc_html( $ancestor->name ) . '</a>';

		}

		$queried_object = $wp_query->get_queried_object();
		
		$trail[] = $queried_object->name;

	}		

	/* If is product tag archive */
	if ( is_product_tag() ) {

		$queried_object = $wp_query->get_queried_object();
				
		$trail[] =  __('Products tagged &ldquo;', 'woocommerce') . $queried_object->name . '&rdquo;' ;

	}

	/* If is single product */
	if ( is_product() ) {

		if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {

			$main_term = $terms[0];

			$ancestors = get_ancestors( $main_term->term_id, 'product_cat' );

			$ancestors = array_reverse( $ancestors );

			foreach ( $ancestors as $ancestor ) {

				$ancestor = get_term( $ancestor, 'product_cat' );

				if ( ! is_wp_error( $ancestor ) && $ancestor ) {
					$trail[] = '<a href="' . get_term_link( $ancestor ) . '">' . $ancestor->name . '</a>';
				}

			}

			$trail[] = '<a href="' . get_term_link( $main_term ) . '">' . $main_term->name . '</a>';
		
		}

		$trail[] =  get_the_title() ;

	}

	if ( maybe_woocommerce() && is_search() && 'product' === $_GET['post_type'] ) {
		$trail[] = sprintf( __( 'Search results for &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );
	}

	if ( maybe_woocommerce() && get_query_var( 'paged' ) ) {
		$trail[] = sprintf( __( 'Page %d', 'woocommerce' ), get_query_var( 'paged' ) );
	}

	/* Return the woocommerce breadcrumb trail items. */
	return apply_filters( 'woocommerce_core_breadcrumb_trail_items', $trail, $args );
	
}

function woocommerce_core_hybrid_layout() {

	/**
 	* Disable sidebar if no widgets set on the woocommerce sidebar, otherwise if shop or woocommerce taxonomy use the woocommerce shop page layout option
 	*/
    if ( maybe_woocommerce() && ! is_active_sidebar( 'woocommerce' ) ) {

        add_filter( 'get_theme_layout', 'my_theme_layout_one_column' );

    } elseif ( is_shop() || is_product_category() || is_product_tag() ) {
		
		/**
		 * Return shop page layout or default if none set
		 */
		add_filter( 'get_theme_layout', 'woocommerce_core_get_shop_layout' );
	
	}

}

/**
 * Return shop page layout or default if none set
 */
function woocommerce_core_get_shop_layout() {

	/* Get the available theme layouts. */
	$layouts = theme_layouts_get_layouts();

	/* Get the theme layout arguments. */
	$args = theme_layouts_get_args();

	/* Get the layout for the shop page */
	$layout = get_post_layout( get_option( 'woocommerce_shop_page_id' ) );

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, $layouts ) || 'default' == $layout )
		$layout = $args['default'];

	return 'layout-' . $layout; 

}

/**
* Define image sizes
*/
function woocommerce_image_dimensions() {

	global $pagenow;
	
	if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
		return;
	}

	$catalog = array(
		'width' => '236',	// px
		'height'	=> '236',	// px
		'crop'	=> 1 // true
	);
	 
	$single = array(
		'width' => '527',	// px
		'height'	=> '527',	// px
		'crop'	=> 1 // true
	);

	$thumbnail = array(
		'width' => '153',	// px
		'height'	=> '153',	// px
		'crop'	=> 1 // false
	);
 
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); // Product category thumbs
	update_option( 'shop_single_image_size', $single ); // Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); // Image gallery thumbs

}

/**
 * Display the review content - with p's etc.
 */
function woocommerce_core_review_display_comment_text() {
	echo '<div itemprop="description" class="description">';
	comment_text();
	echo '</div>';
}