<?php
/**
 * The template for displaying product search form.
 *
 * Override this template by copying it to yourtheme/woocommerce/product-searchform.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" id="searchform" class="form-inline" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<div class="form-group">
		<label class="sr-only" for="woocommerce-product-search-field"><?php _e( 'Search for:', 'woocommerce' ); ?></label>
		<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="woocommerce-product-search-field" class="form-control" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'woocommerce' ); ?>"  />
	</div>
	<div class="form-group">
		<input type="submit" id="searchsubmit" class="btn btn-default" value="<?php echo esc_attr_x( 'Search', 'woocommerce' ); ?>" />
		<input type="hidden" name="post_type" value="product" />
	</div>
</form>