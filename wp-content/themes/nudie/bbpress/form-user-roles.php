<?php

/**
 * User Roles Profile Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div class="form-group">
	<label for="role" class="col-sm-2" ><?php _e( 'Blog Role', 'bbpress' ) ?></label>
	<div class="col-sm-10" >
		<?php bootstrap_bbpress_edit_user_blog_role(); ?>
	</div>
</div>

<div class="form-group">
	<label for="forum-role" class="col-sm-2" ><?php _e( 'Forum Role', 'bbpress' ) ?></label>
	<div class="col-sm-10" >
		<?php bootstrap_bbpress_edit_user_forums_role(); ?>
	</div>
</div>
