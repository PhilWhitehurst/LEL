<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="page-header">
	<?php the_title( '<h1 itemprop="name" class="product_title entry-title">', '</h1>' ); ?>
</div>


