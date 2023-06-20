<?php
/**
 * File Security Check
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('You do not have sufficient permissions to access this page!');
}

$headline = (!hybrid_get_setting('banner-headline') ) ? get_bloginfo('name') : hybrid_get_setting('banner-headline');

$subtext = (!hybrid_get_setting('banner-subtext') ) ? get_bloginfo('description') : hybrid_get_setting('banner-subtext');
?>


<div id="lang">
    <div class="container">

        <div class="lel-social">
            <a href="https://twitter.com/lel2017/" target="_blank">
                <i class="fa fa-twitter"></i>
            </a>

            <a href="https://www.facebook.com/groups/392520757598601/" target="_blank">
                <i class="fa fa-facebook"></i>
            </a>


        </div>
        <p class="fixed"><&nbsp;</p>


        <?php // lel2017_show_language_switcher() ?>
    </div>
</div>
<?php
remove_filter('the_content', 'wpautop');
echo do_shortcode('[lel2017-main-carousel]');
add_filter('the_content', 'wpautop');
?>