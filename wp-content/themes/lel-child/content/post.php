<?php
/**
 * File Security Check
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('You do not have sufficient permissions to access this page!');
}

if (is_singular('post')) {
    ?>

    <article <?php hybrid_attr('post'); ?>>

        <header class="entry-header page-header">
            <h2 <?php hybrid_attr('entry-title'); ?>><?php single_post_title(); ?></h2>
        </header><!-- .entry-header -->

        <div class="entry-byline">
            <?php _e('Published on', 'LEL2017'); ?> <time <?php hybrid_attr('entry-published'); ?>><?php echo get_the_date(); ?></time> <?php edit_post_link('Edit This', '| '); ?>
        </div>

        <div <?php hybrid_attr('entry-content'); ?>>
            <?php the_content(); ?>
            <?php
            if (function_exists('wp_link_pages_extended')) {
                wp_link_pages_extended(array('before' => '<div class="pagination-wrapper pagination-centered"><ul class="pagination">', 'after' => '</ul></div>', 'before_page' => '<li>', 'before_current_page' => '<li class="active">', 'after_page' => '</li>'));
            } else {
                wp_link_pages();
            }

            $sUrl = lel2017_get_short_url(get_the_ID());
            ?>
        </div><!-- .entry-content -->

        <footer class="entry-footer">
            <div class="lel-social-share">
                <p class="clearfix">&nbsp;</p>
                <a href="https://twitter.com/intent/tweet?text=<?php the_title(); ?>&url=<?php echo $sUrl; ?>&hashtags=LEL2017&via=LEL2017" target="_blank">
                    <i class="fa fa-twitter"> <?php _e('Tweet', 'LEL2017'); ?> </i>
                </a>

                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $sUrl; ?>"  target="_blank">
                    <i class="fa fa-facebook"> <?php _e('Share', 'LEL2017'); ?></i>
                </a>



            </div>

            <div class="entry-meta">
                <?php hybrid_post_terms(array('taxonomy' => 'category', 'text' => __('Posted in %s', 'LEL2017'))); ?> <?php hybrid_post_terms(array('taxonomy' => 'post_tag', 'text' => __('Tagged %s', hybrid_get_parent_textdomain()), 'before' => ' | ')); ?>
                <time class="updated" style="display:none;" datetime="<?php echo get_the_modified_time('Y-m-d\TH:i:sP'); ?>" title="<?php echo get_the_modified_time(esc_attr__('l, F jS, Y, g:i a', hybrid_get_parent_textdomain())); ?>"><?php echo get_the_modified_time(get_option('date_format')) ?></time>
            </div>
        </footer><!-- .entry-footer -->

    </article><!-- .hentry -->

<?php } else { ?>

    <article <?php hybrid_attr('post'); ?>>

        <header class="entry-header">
            <?php the_title('<h2 ' . hybrid_get_attr('entry-title') . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2>'); ?>
            <div class="entry-byline">
                Published on <time <?php hybrid_attr('entry-published'); ?>><?php echo get_the_date(); ?></time>  <?php edit_post_link('Edit This', '| '); ?>
            </div>
        </header><!-- .entry-header -->

        <div <?php hybrid_attr('entry-summary'); ?>>
            <?php if (current_theme_supports('get-the-image')) get_the_image(); ?>
            <?php the_excerpt(); ?>
            <?php
            if (function_exists('wp_link_pages_extended')) {
                wp_link_pages_extended(array('before' => '<div class="pagination-wrapper"><ul class="pagination pagination-sm">', 'after' => '</ul></div>', 'before_page' => '<li>', 'before_current_page' => '<li class="active">', 'after_page' => '</li>'));
            } else {
                wp_link_pages();
            }
            ?>
        </div><!-- .entry-summary -->

        <footer class="entry-footer">
            <div class="entry-meta">
                <?php hybrid_post_terms(array('taxonomy' => 'category', 'text' => __('Posted in', 'LEL2017') . ' %s')); ?> <?php hybrid_post_terms(array('taxonomy' => 'post_tag', 'text' => __('Tagged %s', hybrid_get_parent_textdomain()), 'before' => ' | ')); ?>
                <time class="updated" style="display:none;" datetime="<?php echo get_the_modified_time('Y-m-d\TH:i:sP'); ?>" title="<?php echo get_the_modified_time(esc_attr__('l, F jS, Y, g:i a', hybrid_get_parent_textdomain())); ?>"><?php echo get_the_modified_time(get_option('date_format')) ?></time>
            </div>
        </footer><!-- .entry-footer -->

    </article><!-- .hentry -->

<?php } ?>