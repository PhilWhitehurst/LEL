<?php

/**
 * Pagination for pages of replies (when viewing a topic)
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_pagination_loop' ); ?>

<div class="pagination-centered">

	<ul class="pagination">
		<?php bbp_topic_pagination_links(); ?>
	</ul>
	
</div>

<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
