<?php

/**
 * File Security Check
 */
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

function bootstrap_get_product_search_form( $form ) {

	$form = '<form role="search" method="get" id="searchform" class="form-inline" action="' . esc_url( home_url( '/'  ) ) . '">
		<div class="form-group">
			<label class="sr-only" for="s">' . __( 'Search for:', 'woocommerce' ) . '</label>
			<input type="text" value="' . get_search_query() . '" name="s" id="s" class="form-control" placeholder="' . __( 'Search for products', 'woocommerce' ) . '" />
		</div>
		<div class="form-group">
			<input type="submit" id="searchsubmit" class="btn btn-default" value="'. esc_attr__( 'Search', 'woocommerce' ) .'" />
			<input type="hidden" name="post_type" value="product" />
		</div>
	</form>';

	return $form;

}

add_filter( 'get_product_search_form', 'bootstrap_get_product_search_form' );

/**
 * Filter the woocommerce add to cart message to be more bootstrap-y
 */
function wc_bootstrap_add_to_cart_message( $html ) {

	$dom = new DOMDocument();

	@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

	$x = new DOMXPath($dom);

	foreach($x->query("//a") as $node) {   
		$node->setAttribute("class","alert-link");
	}

	$newHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());

	return $newHtml;

}

add_filter( 'wc_add_to_cart_message', 'wc_bootstrap_add_to_cart_message', 10 );


/**
 * Filter the woocommerce order button html to add bootstrap classes
 */
function wc_bootstrap_woocommerce_order_button_html( $html ) {

	$dom = new DOMDocument();

	@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

	$x = new DOMXPath($dom);

	foreach($x->query("//input") as $node) {   

		$classes = explode(" ", $node->attributes->getNamedItem("class")->nodeValue);

		if ( ! empty( $classes ) ) {

			array_push( $classes, 'btn', 'btn-primary' );

			$new_classes = implode(" ", $classes );

			$node->setAttribute("class", $new_classes );

		} 

	}

	$newHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());

	return $newHtml;

}

add_filter( 'woocommerce_order_button_html', 'wc_bootstrap_woocommerce_order_button_html' );

/**
 * Add bootstrap form-group and form-control classes to woocommerce form fields
 */
function bootstrap_woocommerce_form_field_args( $args, $key, $value ) {

	switch ( $args['type'] ) {
			case 'checkbox' :
				$args['class'][] = 'checkbox';
				break;
			case 'radio' :
				$args['class'][] = 'radio';
				break;
			default:
				$args['class'][] = 'form-group';
				$args['input_class'][] = 'form-control';
	}
	return $args;

}

add_filter( 'woocommerce_form_field_args', 'bootstrap_woocommerce_form_field_args', 10, 3 );


/**
 * Handle the hard coded country select fields in the js
 */
function bootstrap_country_select() { ?>

<script type="text/javascript">

jQuery(document).ready(function($) {	

	$('select.country_to_state, input.country_to_state').change(function(){

		$(this).closest('form').find('.input-text, .state_select').addClass( "form-control" );

	});

});

</script>

<?php }

add_action('wp_footer', 'bootstrap_country_select',99 );

/**
 * Remove the default add to cart button for variations to replace without our own
 */
function bootstrap_woocommerce_single_variation_add_to_cart_button() {
	global $product;
	?>
	<div class="variations_button">
		<?php woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) ); ?>
		<button type="submit" class="single_add_to_cart_button btn btn-primary"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
		<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->id ); ?>" />
		<input type="hidden" name="product_id" value="<?php echo absint( $product->id ); ?>" />
		<input type="hidden" name="variation_id" class="variation_id" value="" />
	</div>
	<?php
}
remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
add_action( 'woocommerce_single_variation', 'bootstrap_woocommerce_single_variation_add_to_cart_button', 20 );

/**
 * Remove the default product link open function and add our own
 */
function bootstrap_woocommerce_template_loop_product_link_open() {
	echo '<a href="' . get_the_permalink() . '" class="thumbnail">';
}
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'bootstrap_woocommerce_template_loop_product_link_open', 10 );

/**
 * Remove the product link close function and put it back where it makes sense for us
 */
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

/**
 * Remove the default category titles and add our own
 */
function bootstrap_woocommerce_template_loop_category_title( $category ) {
	?>
	<h3><a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">
		<?php
			if ( $category->count > 0 )
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <span class="count badge pull-right">' . $category->count . '</span>', $category );

			echo $category->name;
		?>
	</a></h3>
	<?php
}
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
add_action( 'woocommerce_shop_loop_subcategory_title', 'bootstrap_woocommerce_template_loop_category_title', 10 );

/**
 * Remove the default category link open function and add our own
 */
function bootstrap_woocommerce_template_loop_category_link_open( $category ) {
	echo '<a href="' . get_term_link( $category->slug, 'product_cat' ) . '" class="thumbnail">';
}
remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
add_action( 'woocommerce_before_subcategory', 'bootstrap_woocommerce_template_loop_category_link_open', 10 );

/**
 * Remove the default category link close function and put it back where it makes sense for us
 */
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
add_action( 'woocommerce_before_subcategory_title', 'woocommerce_template_loop_category_link_close', 15 );


function bootstrap_woocommerce_open_row() {

	global $woocommerce_loop, $bootstrap_close_row;

	$loop    = ! empty( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : 0;
	$columns = ! empty( $woocommerce_loop['columns'] ) ? $woocommerce_loop['columns'] : apply_filters( 'loop_shop_columns', 4 );

	if ( 0 === $loop || 0 === $loop % $columns || 1 === $columns ) {
		echo '<div class="row">';
		$bootstrap_close_row = true;
	}
}

add_action( 'nudie_before_loop_product', 'bootstrap_woocommerce_open_row' );

function bootstrap_woocommerce_close_row() {

	global $woocommerce_loop;

	$loop    = ! empty( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : 1;
	$columns = ! empty( $woocommerce_loop['columns'] ) ? $woocommerce_loop['columns'] : apply_filters( 'loop_shop_columns', 4 );

	// clear fix
	if ( 0 == $loop % 2 ) {
		echo '<div class="clearfix visible-xs"></div>';
	}

	if ( 0 === $loop % $columns ) {
		echo '</div><!-- .row -->';
		$bootstrap_close_row = false;
	}
}

add_action( 'nudie_after_loop_product', 'bootstrap_woocommerce_close_row' );

function bootstrap_woocommerce_product_classes() {
	return apply_filters( 'bootstrap_product_classes', array( 'col-xs-6 col-sm-3' ) );
}


/**
 * Display product sub categories as thumbnails.
 */
function bootstrap_woocommerce_product_subcategories( $args = array() ) {
	global $wp_query;
	$defaults = array(
		'before'        => '',
		'after'         => '',
		'force_display' => false
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	// Main query only
	if ( ! is_main_query() && ! $force_display ) {
		return;
	}
	// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
	if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() ) ) {
		return;
	}
	// Check categories are enabled
	if ( is_shop() && '' === get_option( 'woocommerce_shop_page_display' ) ) {
		return;
	}
	// Find the category + category parent, if applicable
	$term 			= get_queried_object();
	$parent_id 		= empty( $term->term_id ) ? 0 : $term->term_id;
	if ( is_product_category() ) {
		$display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );
		switch ( $display_type ) {
			case 'products' :
				return;
			break;
			case '' :
				if ( '' === get_option( 'woocommerce_category_archive_display' ) ) {
					return;
				}
			break;
		}
	}
	// NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( https://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
	$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
		'parent'       => $parent_id,
		'menu_order'   => 'ASC',
		'hide_empty'   => 0,
		'hierarchical' => 1,
		'taxonomy'     => 'product_cat',
		'pad_counts'   => 1
	) ) );
	if ( ! apply_filters( 'woocommerce_product_subcategories_hide_empty', false ) ) {
		$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
	}
	if ( $product_categories ) {
		echo $before;
		foreach ( $product_categories as $category ) {
			do_action('nudie_before_loop_product');
			wc_get_template( 'content-product_cat.php', array(
				'category' => $category
			) );
			do_action('nudie_after_loop_product');
		}
		// If we are hiding products disable the loop and pagination
		if ( is_product_category() ) {
			$display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );
			switch ( $display_type ) {
				case 'subcategories' :
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
				break;
				case '' :
					if ( 'subcategories' === get_option( 'woocommerce_category_archive_display' ) ) {
						$wp_query->post_count    = 0;
						$wp_query->max_num_pages = 0;
					}
				break;
			}
		}
		if ( is_shop() && 'subcategories' === get_option( 'woocommerce_shop_page_display' ) ) {
			$wp_query->post_count    = 0;
			$wp_query->max_num_pages = 0;
		}
		echo $after;
		return true;
	}
}


function bootstrap_woocommerce_account_menu_item_classes( $classes, $endpoint ) {

	global $wp;

	// Set current item class.
	$current = isset( $wp->query_vars[ $endpoint ] );

	if ( 'dashboard' === $endpoint && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
		$current = true; // Dashboard is not an endpoint, so needs a custom check.
	}

	if ( $current ) {
		$classes[] = 'active';
	}

	return $classes;
}

add_filter( 'woocommerce_account_menu_item_classes', 'bootstrap_woocommerce_account_menu_item_classes', 10, 2 );
