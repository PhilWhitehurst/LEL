<?php
/**
 * File Security Check
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('You do not have sufficient permissions to access this page!');
}
?>
</main><!-- #main -->

<?php get_sidebar('subsidiary'); // Loads the sidebar-primary.php template. ?>

<footer <?php hybrid_attr('footer'); ?>>

    <div class="container">

        <?php get_template_part('menu', 'subsidiary'); // Loads the menu-subsidiary.php template. ?>

        <div class="footer-content">
            <div class="pull-left">(C) Copyright 2017</div>
            <!-- Terms and conditions, privacy and contact us were here -->
        </div><!-- .footer-content -->
        <div class="pull-right lel-footer-logo">

            <img src="<?php echo get_stylesheet_directory_uri() . '/img/logos/svg/Rad_Mon.svg' ?>" class="pull-right" />
            <img src="<?php echo get_stylesheet_directory_uri() . '/img/logos/svg/AUK.svg' ?>" class="pull-right" />
            <img src="<?php echo get_stylesheet_directory_uri() . '/img/logos/svg/LEL2017_LogoIcon-EN_GB-RGB.svg' ?>" class="pull-right" />
        </div>

    </div><!-- .container -->

</footer><!-- #footer -->

<?php wp_footer(); // wp_footer ?>

</body>
</html>