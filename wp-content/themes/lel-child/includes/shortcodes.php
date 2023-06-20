<?php
/*
 * Phil Whitehurst
 * All shortcode functionality for the lel2017 child theme
 *  */

/*
 * Replace shortcode [lel2017-main-carousel] with carousel (on front page)
 */




add_shortcode('lel2017-main-carousel', 'lel2017_main_carousel');

function lel2017_main_carousel() {

    $output = lel2017_main_carousel_html();
    return $output;
}

/*
 * Return the volunteer sign up form html
 */

function lel2017_main_carousel_html() {
    ob_start();
    ?>
    <div id="myCarousel" class="carousel fade" data-ride="carousel" data-interval="5000" >

        <ol class="carousel-indicators" >
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1" ></li>
            <li data-target="#myCarousel" data-slide-to="2" ></li>
            <li data-target="#myCarousel" data-slide-to="3" ></li>
        </ol>

        <div class="lel-carousel-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logos/svg/LEL2017_carousel_logo.svg" />
        </div>

        <div class="carousel-inner" role="listbox">

            <div class="item active">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/carousel/LEL-2017_carousel_02.jpg" alt="LEL-2017_carousel_02" >
                <div class="carousel-caption">
                    <h1 class="text-center" ><?php _e('1500 RIDERS', 'LEL2017'); ?></h1>
                </div>
            </div>

            <div class="item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/carousel/LEL-2017_carousel_03.jpg" alt="LEL-2017_carousel_03" >
                <div class="carousel-caption">
                    <h1 class="text-center"><?php _e('1400 KILOMETRES', 'LEL2017'); ?></h1>
                </div>
            </div>

            <div class="item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/carousel/LEL-2017_carousel_04.jpg" alt="LEL-2017_carousel_04" >
                <div class="carousel-caption">
                    <h1 class="text-center"><?php _e('FIVE DAYS IN SUMMER', 'LEL2017'); ?></h1>
                </div>
            </div>

            <div class="item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/carousel/LEL-2017_carousel_01.jpg" alt="LEL-2017_carousel_01" >
                <div class="carousel-caption">
                    <h1 class="text-center"><?php _e('ONE AMAZING ADVENTURE', 'LEL2017'); ?></h1>
                </div>
            </div>

            <a class="carousel-control left" href="#myCarousel" role="button" data-slide="prev"  >
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>

            <a class="carousel-control right" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>

            </a>
        </div>
    </div> <!-- Carousel --!>

    <?php
    return \ob_get_clean();
}

/*
 * Latest Posts, magazine style
 */

add_shortcode('lel2017_latest', 'lel2017_latest_posts');

function lel2017_latest_posts($atts) {
    extract(shortcode_atts(array(
        'posts' => 3,
        'category' => 'News',
        'title' => 'Yes',
                    ), $atts));

    /*
     * Header colour in article extract
     */



// Query arguments
    $args = [
        'posts_per_page' => $posts,
        'category_name' => $category
    ];

// The Query
    $the_query = new WP_Query($args);

// The Loop
    if ($the_query->have_posts()) {
        ob_start();
        ?>
                                                                                                                                                                                                                <div class="panel panel-default">
        <?php if ($title === 'Yes') { ?>          <div class="panel-heading">
                                                                                                                                                                                                                    <h3 class="panel-title"> <?php _e('NEWS', 'LEL2017'); ?></h3>
                                                                                                                                                                                                                    </div><?php } ?>
                                                                                                                                                                                                                <div class="panel-body">

        <?php
        while ($the_query->have_posts()) {

            $the_query->the_post();
            ?>
                                                                                                                                                                                                                    <header class="entry-header">
            <?php the_title('<h2 ' . hybrid_get_attr('entry-title') . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2>'); ?>
                                                                                                                                                                                                                    <div class="entry-byline">
            <?php _e('Published on', 'LEL2017'); ?> <time <?php hybrid_attr('entry-published'); ?>><?php echo get_the_date(); ?></time>  <?php edit_post_link('Edit This', '| '); ?>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                    </header><!-- .entry-header -->

            <div <?php hybrid_attr('entry-summary'); ?>>
                <?php
                $default_img = get_stylesheet_directory_uri() . '/img/logos/svg/LEL2017_carousel_logo.svg';
                $defaults = array(
                    'default' => $default_img,
                    'width' => '150px',
                );

                if (current_theme_supports('get-the-image')) {
                    get_the_image($defaults);
                }
                $sUrl = lel2017_get_short_url(get_the_ID());
                ?>
                <?php the_excerpt(); ?>

                <div class="lel-social-share">
                    <p class="clearfix">&nbsp;</p>
                    <a href="https://twitter.com/intent/tweet?text=<?php the_title(); ?>&url=<?php echo $sUrl ?>&hashtags=LEL2017&via=LEL2017" target="_blank">
                        <i class="fa fa-twitter"> <?php _e('Tweet', 'LEL2017'); ?> </i>
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $sUrl; ?>"  target="_blank">
                        <i class="fa fa-facebook"> <?php _e('Share', 'LEL2017'); ?></i>
                    </a>

                </div>

            </div><!-- .entry-summary -->


            </article><!-- .hentry -->


            <?php
        }
        /* Restore original Post Data */
        wp_reset_postdata();
        ?>
        </div> <!-- panel body end -->
        <?php
        if (is_home() or is_front_page()) {
            echo '<p><a class="btn btn-primary" href="/news/">' . __('...more', 'LEL2017') . ' ' . __('NEWS', 'LEL2017') . '</a></p>';
        }
        ?>
        </div> <!-- panel end -->
        <?php
    }

    return \ob_get_clean();
}

add_shortcode('lel2017_menu', 'lel2017_menu');

function lel2017_menu($atts) {
    extract(shortcode_atts(array(
        'menu' => 'Gallery',)
                    , $atts));


    $defaults = array(
        'menu' => $menu,
        'container' => 'div',
        'container_class' => 'lel-menu-container ',
        'container_id' => '',
        'menu_class' => 'lel-menu',
        'menu_id' => '',
        'echo' => false,
        'before' => '',
        'after' => '',
        'link_before' => '',
        'link_after' => '',
        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth' => 0,
        'walker' => ''
    );

    $output = '
<div class="col-md-11 lel-widget-pad">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">'
            . __($menu, 'LEL2017')
            . '</h3>
  </div>
  <div class="panel-body">';

    $output .= wp_nav_menu($defaults);
    $output .= '</div></div></div>';

    return $output;
}

add_shortcode('lel2017_image_gallery', 'lel2017_image_gallery');

function lel2017_image_gallery($atts) {
    extract($atts);
    $media_categories = array($terms);

    /* Styles required
     * 
     */

    wp_enqueue_style('blueimp-gallery-css');
    wp_enqueue_style('bootstrap-image-css');

    /*
     * scripts required
     */
    wp_enqueue_script('blueimp-gallery-script');
    wp_enqueue_script('bootstrap-image-gallery-script');

    ob_start();
    ?>

    <!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery">
        <!-- The container for the modal slides -->
        <div class="slides"></div>
        <!-- Controls for the borderless lightbox -->
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
        <!-- The modal dialog, which will be used to wrap the lightbox content -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body next"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left prev">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary next">
                            Next
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="image-links">

        <?php
        /*
         * Switch to the live site
         */
        switch_to_blog(1);
        /*
         * Search for images with chosen taxonomy terms
         */
        $args = array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'inherit',
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $media_categories,
                ),
            ),
        );
        $query = new WP_Query($args);

// The Loop

        while ($query->have_posts()) {
            $query->the_post();
            $ID = get_the_ID();
            if (wp_attachment_is_image()) {

                $thumbnail = wp_get_attachment_image_src($ID, 'thumbnail');
                $link = wp_get_attachment_image_src($ID, 'large');
                ?>
                <a href="<?php echo $link[0]; ?>" data-gallery><img src="<?php echo $thumbnail[0]; ?>" /></a>

                <?php
            }
        }

// Restore original Post Data
        wp_reset_postdata();
        restore_current_blog();
        ?>

    </div>
    <?php
    return \ob_get_clean();
}

/*
 * Return latest posts in same menu style as gallery
 */

function lel2017_latest_news_menu($posts = 5) {

    $args = array(
        'posts_per_page' => '-1',
        'orderby' => 'post_date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish',
        'suppress_filters' => true);

    $recent_posts = wp_get_recent_posts($args, ARRAY_A);
    ob_start();
    ?>

    <div class="col-md-11 lel-widget-pad">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"> <?php _e('NEWS', 'LEL2017'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="lel-menu-container">
                    <ul class="lel-menu">
                        <?php
                        foreach ($recent_posts as $recent) {
                            $permalink = get_permalink($recent["ID"]);
                            $title = $recent["post_title"];
                            $slug = the_slug($recent["ID"]);
                            $class = ($slug === the_slug() ? 'current-menu-item' : '');
                            ?>
                            <li class="<?php echo $class ?>"><a href="<?php echo $permalink ?>"> <?php echo $title ?> </a></li>

                            <?php
                        }
                        ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <?php
    return \ob_get_clean();
}

add_shortcode('lel2017_sidebar_menu', 'lel2017_sidebar_menu');
/*
 * Choose menu based on page
 */

function lel2017_sidebar_menu($posts = -1, $menu = NULL) {
    $slug = the_slug();
// Gallery menu
    $galleryPages = [
        'prologue-photos',
        'finishers-bw-photos',
        'finishers-colour-photos',
        'edinburgh-photos'
    ];
    $newsPages = ['news'];
    if (is_single() or in_array($slug, $newsPages)) {
        $output = lel2017_latest_news_menu($posts);
    }
    if (in_array($slug, $galleryPages)) {
        $output = lel2017_menu($menu);
    }
//Rider Menu
    $riderPages = [
        'rider-my-details',
        'rider-start-time',
        'rider-bag-drop',
        'rider-merchandise'
    ];
    if (in_array($slug, $riderPages)) {
        $output = lel2017_menu(['menu' => 'Rider']);
    }
//Volunteer Menu
    $volunteerPages = [
        'volunteer-my-details',
        'volunteer-merchandise',
        'volunteer-my-control',
    ];

    if (in_array($slug, $volunteerPages)) {

        if (current_user_can('access_volunteer_area')) {
            $output = lel2017_menu(['menu' => 'Volunteer']);
        }
    }

    // Tracking pages

    $trackingPages = [

        'volunteer-arrival-departure',
        'volunteer-sleep',
        'volunteer-dnf',
        'volunteer-bag-drop',
        'volunteer-rider-dashboard',
        'volunteer-control-dashboard',
        'volunteer-event-dashboard'
    ];


    if (in_array($slug, $trackingPages)) {

        if (current_user_can('access_tracking_area')) {
            $output = lel2017_menu(['menu' => 'Tracking']);
        }
    }

// Registration pages

    $registrationPages = [
        'volunteer-registration',
    ];


    if (in_array($slug, $registrationPages)) {

        if (current_user_can('access_registration_area')) {
            $output = lel2017_menu(['menu' => 'Registration']);
        }
    }




//Public Tracking Menu
    $publicTrackingPages = [
        'tracking-rider',
        'tracking-chart-time-in-hand',
        'tracking-chart-distance-time',
        'tracking-chart-speed-time',
        'tracking-chart-speed-distance',
        'tracking-chart-elapsed-distance',
        'tracking-chart-event'
    ];
    if (in_array($slug, $publicTrackingPages)) {
        $output = lel2017_menu(['menu' => 'Public Tracking']);
    }

    __('Public Tracking', 'LEL2017');




// Rider Admin Menu
    $riderAdminPages = [
        'admin-rider-start-waves',
        'admin-set-designated-start',
        'admin-rider-dashboard'
    ];
    if (in_array($slug, $riderAdminPages)) {
        $output = lel2017_menu(['menu' => 'Rider Admin']);
    }
// Route Menu
    $routePages = [
        'route',
        'loughton-stives',
        'stives-spalding',
        'spalding-louth',
        'louth-pocklington',
        'pocklington-thirsk',
        'thirsk-barnardcastle',
        'barnardcastle-brampton',
        'brampton-moffat',
        'moffat-edinburgh',
        'edinburgh-brampton',
        'brampton-barnardcastle',
        'barnardcastle-thirsk',
        'thirsk-pocklington',
        'pocklington-louth',
        'louth-spalding',
        'spalding-stives',
        'stives-greateaston',
        'greateaston-loughton'
    ];


    if (in_array($slug, $routePages)) {
        $output = lel2017_menu(['menu' => 'Northbound']);
        $output .= lel2017_menu(['menu' => 'Southbound']);
    }
    __('Northbound', 'LEL2017');
    __('Southbound', 'LEL2017');

    return $output;
}

/*
 * Test microbloggers url shortner
 */

add_shortcode('lel2017_url_shorten', 'lel2017_url_shorten');

function lel2017_url_shorten($atts) {
    extract(shortcode_atts(array(
        'url' => 'http://londonedinburghlondon.com',)
                    , $atts));

    $output = lel2017_url_shortener($url, false);

    return $output;
}

add_shortcode('lel2017_resend_order_emails', 'lel2017_resend_order_emails');

function lel2017_resend_order_emails() {
    $args = array(
        'post_type' => 'shop_order',
        'post_status' => 'wc-on-hold',
        'meta_key' => '_customer_user',
        'posts_per_page' => '-1',
        'orderby' => 'ID'
    );


    $my_query = new WP_Query($args);

    $customer_orders = $my_query->posts;


    $wc_customer_order = new WC_Email_Customer_Processing_Order();
    $wc_new_order = new WC_Email_New_Order();
    $wc_bacs = new WC_Gateway_BACS();

// $wc_customer_order->trigger(3291);
    $wc_customer_order->trigger(3557);

    $output = '';
    /*
      foreach ($customer_orders as $customer_order) {

      $order_id = $customer_order->ID;

      $output .= $order_id . '<br>';
      $wc_customer_order->trigger($order_id);
      // $wc_new_order->trigger($order_id);
      }
     */
    return $output;
}
