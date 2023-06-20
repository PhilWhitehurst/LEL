<?php
/**
 * File Security Check
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('You do not have sufficient permissions to access this page!');
}

/* If viewing a singular front page, return. */
if (( is_home() && is_front_page() ) || ( is_page() && is_front_page() ))
    return;
?>
<?php ?>

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
        <p class="fixed">&nbsp;</p>
        <?php
        /*
         *  output the language switcher
         */

        // lel2017_show_language_switcher();
        ?>
    </div>
</div>


<div id="lel-header-image" >

    <img src="<?php lel2017_header_image(); ?>" class="img-responsive" width="100%">

</div><!-- .loop-meta -->
