<?php

/**
 * The Woo Commerce functionality  of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    lel-2017
 * @subpackage lel-2017/includes
 * @author     Phil Whitehurst
 */
class LEL_WooCommerce {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->current_gateway_title = '';
        $this->current_gateway_extra_charges = '';
        $this->common = new LEL_Common($this->plugin_name, $this->version);
    }

    public function auth_redirect() {
        $wooPages = [
            'rider',
        ];


        $slug = the_slug();
        if (in_array($slug, $wooPages) && !current_user_can('access_rider_area')) {
            wp_logout();
            auth_redirect();
        }

        $wooPages = [
            'volunteer',
        ];


        $slug = the_slug();
        if (in_array($slug, $wooPages) && !current_user_can('access_volunteer_area')) {
            wp_logout();
            auth_redirect();
        }

        $wooPages = [
            'basket',
            'checkout',
        ];


        $slug = the_slug();
        if (in_array($slug, $wooPages) && !current_user_can('access_rider_area') && !current_user_can('access_volunteer_area')) {
            wp_logout();
            auth_redirect();
        }
    }

    /**
     * Add custom fields to woocommerce product inventory admin tab
     *
     * @since     1.0.0
     * @return    null
     */
    public function woo_add_custom_inventory_fields() {

// global $woocommerce, $post;

        echo '<div class="options_group">';

        woocommerce_wp_checkbox(
                array(
                    'id' => '_lel_lifetime_checkbox',
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Lifetime', 'woocommerce'),
                    'description' => __('If <b>Sold Individually</b> is also checked then only one of this item may be bought across the lifetime of a customer account (identified by email)', 'woocommerce')
                )
        );

// lifetime group
        woocommerce_wp_text_input(
                array(
                    'id' => '_lel_lifetime_group',
                    'label' => __('Lifetime Group', 'woocommerce'),
                    'placeholder' => 'Lifetime Group',
                    'desc_tip' => 'true',
                    'description' => __('A group from which only one of the products belonging to it can be bought during the lifetime of the customer account', 'woocommerce')
                )
        );

        echo '</div>';
    }

    /**
     * Add custom fields to woocommerce product general admin tab
     *
     * @since     1.0.0
     * @return    null
     */
    public function woo_add_custom_general_fields() {

// global $woocommerce, $post;

        echo '<div class="options_group">';

        woocommerce_wp_checkbox(
                array(
                    'id' => '_lel_early_entry_checkbox',
                    'wrapper_class' => 'show_if_simple',
                    'label' => __('Early Entry', 'woocommerce'),
                    'description' => __('Tick box if only available to qualified early entrants', 'woocommerce')
                )
        );

        echo '</div>';
    }

    /**
     * Save custom fields as meta on woocommerce product save
     *
     * @since     1.0.0
     * @return    null
     */
    public function woo_add_custom_inventory_fields_save($post_id) {
// Checkbox
        $woocommerce_lifetime_checkbox = isset($_POST['_lel_lifetime_checkbox']) ? 'yes' : 'no';
        update_post_meta($post_id, '_lel_lifetime_checkbox', $woocommerce_lifetime_checkbox);

        $woocommerce_lifetime_group = $_POST['_lel_lifetime_group'];
        if (!empty($woocommerce_lifetime_group)) {
            update_post_meta($post_id, '_lel_lifetime_group', $woocommerce_lifetime_group);
        }
    }

    /**
     * Save custom fields as meta on woocommerce product save
     *
     * @since     1.0.0
     * @return    null
     */
    public function woo_add_custom_general_fields_save($post_id) {
// Checkbox
        $woocommerce_checkbox = isset($_POST['_lel_early_entry_checkbox']) ? 'yes' : 'no';
        update_post_meta($post_id, '_lel_early_entry_checkbox', $woocommerce_checkbox);
    }

    /*
     * ReCaptcha settings section for Woocommerce
     */

    public function woo_add_recaptcha_section($settings_tabs) {
        $settings_tabs['recaptcha'] = __('Recaptcha', 'woocommerce-settings-recaptcha');
        return $settings_tabs;
    }

    public function woo_recaptcha_add_settings_to_tab() {

        woocommerce_admin_fields($this->get_woo_recaptcha_settings());
    }

    public function get_woo_recaptcha_settings() {
        $settings = array(
            'section_title' => array(
                'name' => __('ReCAPTCHA Keys', 'lel-woocommerce'),
                'type' => 'title',
                'desc' => 'Enter your site and secret keys and where to show reCAPTCHA',
                'id' => 'wc_settings_recaptcha_api_title'
            ),
            'site' => array(
                'name' => __('Site Key', 'lel-woocommerce'),
                'type' => 'text',
                'desc' => __('Please supply reCAPTCHA site key', 'lel-woocommerce'),
                'id' => 'wc_settings_recaptcha_site_key'
            ),
            'secret' => array(
                'name' => __('Secret Key', 'lel-woocommerce'),
                'type' => 'text',
                'desc' => __('Please supply reCAPTCHA secret key.', 'lel-woocommerce'),
                'id' => 'wc_settings_recaptcha_secret_key'
            ),
            'checkout' => array(
                'name' => __('Show in checkout page', 'lel-woocommerce'),
                'type' => 'checkbox',
                'desc' => __('If checked, then reCAPTCHA will be shown on checkout page', 'lel-woocommerce'),
                'id' => 'wc_settings_recaptcha_checkout'
            ),
        );
        return apply_filters('wc_settings_recaptcha', $settings);
    }

    public function update_recaptcha_settings() {
        woocommerce_update_options($this->get_woo_recaptcha_settings());
    }

    public function add_export_tab_fields($settings) {
        $lel_settings = array(
            'gender' => array(
                'name' => __('Gender', 'woocommerce-simply-order-export'),
                'type' => 'checkbox',
                'desc' => __('Gender', 'woocommerce-simply-order-export'),
                'id' => 'wc_settings_tab_gender'
            ),
            'country' => array(
                'name' => __('Country', 'woocommerce-simply-order-export'),
                'type' => 'checkbox',
                'desc' => __('Country', 'woocommerce-simply-order-export'),
                'id' => 'wc_settings_tab_country'
            )
        );
        return array_merge($settings, $lel_settings);
    }

    public function add_export_columns($cols) {
        $cols['wc_settings_tab_gender'] = __('Gender', $this->plugin_name);
        $cols['wc_settings_tab_country'] = __('Country', $this->plugin_name);
        return $cols;
    }

    public function add_values_to_csv(&$csv_values, $order_details, $key, $fields, $item_id, $current_item) {
        $order_id = get_the_ID();

        switch ($key) {
            /**
             * Check if we need gender
             */
            case 'wc_settings_tab_gender':
                $user_id = get_post_meta($order_id, '_customer_user', true);
                $gender = get_user_meta($user_id, 'gender', true);
                array_push($csv_values, $gender);
                break;
            case 'wc_settings_tab_country':
                $country = get_post_meta($order_id, '_billing_country', true);
                array_push($csv_values, $country);
                break;
        }
    }

    /**
     * Return qty of 1 if product already in cart
     * Should trigger a cannot add to cart message
     *
     * @since    1.0.0
     * @param    numeric     $quantity        Current cart qty
     * @param    numeric     $product_id      id of product being added to cart
     * @param    numeric     $variation_id    Of product being added to cart
     * @param    array       $cart item data  Array of products in cart
     * @returns     array    $product_ids array of product ids matching sku
     */
    public function is_one_item_in_cart($q, $quantity, $product_id, $variation_id, $cart_item_data) {

        $this->remove_from_basket();
        $prod_ids = $this->getProdbySku($product_id);
        $lifetime_group = get_post_meta($product_id, '_lel_lifetime_group', true);



        global $woocommerce;

        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {

            $cart_product_id = $cart_item['product_id'];

            if (in_array($cart_product_id, $prod_ids)) {

                throw new Exception(sprintf('<a href="%s" class="button wc-forward">%s</a> %s', $woocommerce->cart->get_cart_url(), __('View Cart', 'woocommerce'), sprintf(__('You cannot add another &quot;%s&quot; to your cart.', 'woocommerce'), get_the_title($product_id))));
            }

            $cart_lifetime_group = get_post_meta($cart_product_id, '_lel_lifetime_group', true);


            if (!empty($lifetime_group)) {

                if ($lifetime_group === $lifetime_group) {


                    throw new Exception(sprintf('<a href="%s" class="button wc-forward">%s</a> %s', $woocommerce->cart->get_cart_url(), __('View Cart', 'woocommerce'), sprintf(__('You cannot add &quot;%s&quot; to your cart.', 'woocommerce'), get_the_title($product_id))));
                }
            }
        }

        return $q;
    }

    /*
     * Remove items from cart if out of stock
     */

    public function remove_from_basket() {

        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {

            $_product = $values['data'];
            $prod_id = $_product->get_id();
            $product_data = wc_get_product($prod_id);
            if (!$product_data->is_in_stock()) {
                $woocommerce->cart->remove_cart_item($cart_item_key);
            }
        }
    }

    /**
     * Checks for lifetime single purchase items to see if customer has
     * already purchased
     *
     * @since    1.0.0
     * @param      array   $availability  Is product available?
     * @param      object  $product       Reference to the woo commerce product
     * @returns    array   $availability  is product available?
     */
    public function is_purchasable($purchasable, $product) {


        $Path = $_SERVER['REQUEST_URI'];

        if (strpos($Path, '/order-received/') !== false) {
            return $purchasable;
        }


        /*
         * If not logged in, then assume item purchasable for now
         * as we don't know who they are
         */
        if (is_user_logged_in()) {

            $prod_id = ($product->get_id() ? $product->get_id() : get_the_ID() );
            $prod_ids = $this->getProdBySku($prod_id);

            /*
             * Get the product single purchase and lifetime values
             */
            $lifetime = get_post_meta($prod_id, '_lel_lifetime_checkbox', true);
            $lifetime_group = get_post_meta($prod_id, '_lel_lifetime_group', true);

            $sold_individually = get_post_meta($prod_id, '_sold_individually', true);

            /*
             * Get current user
             */
            $current_user = wp_get_current_user();
            $current_user_id = $current_user->ID;
            $current_user_email = $current_user->user_email;


            /*
             * If current user has already purchased product then set purchasable to false
             */
            if ($lifetime === 'yes' && $sold_individually === 'yes') {

                if ($this->customer_bought_product($current_user_email, $current_user_id, $lifetime_group) > 0) {
                    $purchasable = false;
                    $product->purchased = array(
                        'purchased' => __('Already purchased', $this->plugin_name),
                        'class' => 'item_purchased');
                    $product->purchased_class = 'item_purchased';
                }
            }
        }
        return $purchasable;
    }

    /**
     * Checks if a user (by email) has bought an item. This is a copy of
     * the woocommerce wc_customer_bought_product function but includes onhold
     * (wc-on-hold) orders in the query
     *
     * @access public
     * @param string $customer_email
     * @param int $user_id
     * @param int $product_id
     * @return bool
     */
    private function customer_bought_product($customer_email, $user_id, $lifetime_group) {
        global $wpdb;

        $emails = array();

        if ($user_id) {
            $user = get_user_by('id', $user_id);

            if (isset($user->user_email)) {
                $emails[] = $user->user_email;
            }
        }

        if (is_email($customer_email)) {
            $emails[] = $customer_email;
        }

        if (sizeof($emails) == 0) {
            return false;
        }

        return $wpdb->get_var(
                        $wpdb->prepare("
			SELECT COUNT( DISTINCT order_items.order_item_id )
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
			LEFT JOIN {$wpdb->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE
				posts.post_status IN ( 'wc-on-hold' , 'wc-completed', 'wc-processing' ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    = '_lel_lifetime_group'  AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode("','", array_map('esc_sql', array_unique($emails))) . "' ) OR
					(
						postmeta.meta_value = %s AND
						postmeta.meta_value > 0
					)
				)
			", $lifetime_group, $user_id
                        )
        );
    }

    /**
     * Add the recaptcha field to the checkout
     */
    public function recaptcha_checkout_field($checkout) {
        wp_register_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
        wp_enqueue_script('recaptcha');

        echo '<div id="woo-recaptcha"><h3>ReCAPTCHA <abbr class="required" title="required">*</abbr></h3>';
        echo '<div class="g-recaptcha" data-sitekey="' . get_option('wc_settings_recaptcha_site_key') . '"></div>';
        echo '</div>';
    }

    /**
     * Add the fields to the checkout
     */
    public function checkout_fields($checkout) {
        $user_id = get_current_user_id();
        $gender = get_user_meta($user_id, 'gender', true);
        $auk_membership_number = get_user_meta($user_id, 'auk_membership_number', true);
        ?>
        <div class = "radio">
            <p><strong><?php _e('Gender');
        ?></strong> <abbr class="required" title="required">*</abbr></p>
            <label>
                <input type="radio" name="gender" id="genderOptions1" value="male" <?php echo ($gender === 'male' ? 'checked' : ''); ?>>
                <?php _e('Male'); ?>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="gender" id="genderOptions2" value="female" <?php echo ($gender === 'female' ? 'checked' : ''); ?>>
                <?php _e('Female'); ?>
            </label>
        </div>
        <div class="form-group">
            <p><strong><?php _e('Audax UK');
                ?></strong></p>

            <input type="text" class="form-control input-text" name="auk_membership_number" id="auk_membership_number" value="<?php echo ($auk_membership_number !== undefined ? $auk_membership_number : ''); ?>" placeholder="Please provide membership number if a current member of Audax UK"><br>


        </div>
        <?php
    }

    public function recaptcha_checkout_field_validate() {

        /*
         * Check Google reCAPTCHA test has been passed
         */

        if (count(wc_get_notices()) === 0) { /* only if no errors already */




            $captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $g_response = wp_remote_post($captcha_url, array(
                'body' => array('secret' => get_option('wc_settings_recaptcha_secret_key'),
                    'response' => $_POST['g-recaptcha-response'],
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                )
                    )
            );

            $g_body = json_decode($g_response["body"], TRUE);

            if (!$g_body["success"]) {
                /*
                 * Failed the reCAPTCHA test
                 */
                wc_add_notice(__('You must complete the reCAPTCHA test (it may have timed out)'), 'error');
            }
        }
    }

    public function checkout_fields_validate() {

// Check if set, if its not set add an error.
        if (!isset($_POST['gender'])) {
            wc_add_notice(__('You must choose a gender of male or female'), 'error');
        }

// If any products in the cart are early entry then perform
// early entry validation
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $early_entry = false;
        foreach ($items as $item => $values) {
            $_product = $values['data']->post;
            if (get_post_meta($values['product_id'], '_lel_early_entry_checkbox', true) === 'yes') {
                $early_entry = true;
            }
        }

        if ($early_entry) {
            $this->early_entry_validation();
        }
    }

    private function early_entry_validation() {

// If early entry products then check they are on auk list or guaranteed
        if (isset($_POST['auk_membership_number']) && !empty($_POST['auk_membership_number'])) {
// Get auk lookup list
            $auk_membership_number = $_POST['auk_membership_number'];
// strip out any non numeric bits for validation
            $auk_membership_number = preg_replace("/[^0-9,.]/", "", $auk_membership_number);
// Check they don't already have an entry
            if ($this->check_early_entry($auk_membership_number)) {
                $msg = 'There is already an entry against the Audax UK membership number, a second entry is not permitted';
                wc_add_notice(_($msg), 'error');
            }


            $auk = $this->get_early_entry('auk');

// First check if membership number supplied is in auk early entry list


            if (!$auk[$auk_membership_number]) {
// not in auk early entry list so check if in guaranteed list
                $this->check_guaranteed_entry($auk_membership_number);
            } else {

// auk membership number is in early entry list now check
// details supplied are a match
// Check supplied first name is correct
                if (isset($_POST['billing_first_name']) && !empty($_POST['billing_first_name'])) {
                    $first_name = strtolower($_POST['billing_first_name']);
                    if ($auk[$auk_membership_number]["first_name"] !== $first_name) {
                        $msg = 'Supplied first name does not match details held in Audax UK early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }
// Check supplied last name is correct
                if (isset($_POST['billing_last_name']) && !empty($_POST['billing_last_name'])) {

                    $_POST['billing_last_name'] = str_replace("\\", "", $_POST['billing_last_name']);
                    $last_name = strtolower($_POST['billing_last_name']);


                    if ($auk[$auk_membership_number]["last_name"] !== $last_name) {
                        $msg = 'Supplied last name does not match details held details held in Audax UK early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }

// Check supplied postcode is correct
                if (isset($_POST['billing_postcode'])) {

                    $post_code = (string) strtolower($_POST['billing_postcode']);


                    if ((string) strtolower($auk[$auk_membership_number]["post_code"]) !== $post_code) {
                        $msg = 'Supplied post code does not match details held details held in Audax UK early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }
            }
        } else {
// auk membership number not entered check guaranteed list
            $this->check_guaranteed_entry(0);
        }
    }

    private function check_guaranteed_entry($auk_no) {
// Check guaranteed entry if audax uk number not entered
        if (isset($_POST['billing_email']) && !empty($_POST['billing_email'])) {
            $guaranteed = $this->get_early_entry('guaranteed');

//Check email in guaranteed list
            $email = $_POST['billing_email'];

            if (!$guaranteed[$email]) {
                $msg = 'Email address is not in the early entry list';
                wc_add_notice(_($msg), 'error');
            } else {
// Check name details match supplied email
// Check supplied first name is correct
                if (isset($_POST['billing_first_name']) && !empty($_POST['billing_first_name'])) {

                    $first_name = strtolower($_POST['billing_first_name']);
                    if ($guaranteed[$email]["first_name"] !== $first_name) {
                        $msg = 'First name does not match details held in early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }
// Check supplied last name is correct
                if (isset($_POST['billing_last_name']) && !empty($_POST['billing_last_name'])) {

                    $_POST['billing_last_name'] = str_replace("\\", "", $_POST['billing_last_name']);
                    $last_name = strtolower($_POST['billing_last_name']);
                    if ($guaranteed[$email]["last_name"] !== $last_name) {
                        $msg = 'Last name does not match details held in early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }

// If Audax UK membership number held in list, check not already
// registered via auk list

                $auk_membership_number = $guaranteed[$email]['auk_membership_number'];

//if auk number provided then check it matches the one we hold in
//   guaranteed list

                if ($auk_no !== 0) {
                    if ($auk_no !== $auk_membership_number) {
                        $msg = 'Audax UK membership number does not match details held in early entry list';
                        wc_add_notice(_($msg), 'error');
                    }
                }


                if ($auk_membership_number !== 0) {
                    $_POST['auk_membership_number'] = $auk_membership_number;
                    if ($this->check_early_entry($auk_membership_number)) {
                        $msg = 'There is already an entry against the Audax UK membership number, a second entry is not permitted';
                        wc_add_notice(_($msg), 'error');
                    }
                }
            }
        }
    }

    public function checkout_accept_fields() {
        $user_id = get_current_user_id();
        $privacy = get_user_meta($user_id, 'privacy', true);
        $age18 = get_user_meta($user_id, 'age18', true);
        ?>
        <p class="form-row terms">
            <input type="checkbox" class="input-checkbox" name="privacy"  id="privacy" <?php
            if ($privacy === 'on') {
                echo checked;
            }
            ?>/>

            <label for="privacy" class="checkbox">I have read and accept the <a href="/privacy/" target="_blank">privacy policy *</a></label>

        </p>
        <?php if (!is_user_logged_in()) { ?>
            <p class="form-row terms">
                <input type="checkbox" class="input-checkbox" name="age18"  id="age18" <?php
                if ($age18 === 'on') {
                    echo checked;
                }
                ?>/>
                <label for="age18" class="checkbox">I will be aged 18 or over on 30 July 2017 </label>

            </p>

            <?php
        }
    }

    /*
     * Check the the accept checkboxes have been ticked
     */

    public function checkout_accept_fields_validate() {

// Check if set, if its not set add an error.
        if (!isset($_POST['privacy'])) {
            wc_add_notice(__('You must accept our privacy policy.'), 'error');
        }
        if (!is_user_logged_in()) {

            if (!isset($_POST['age18'])) {
                wc_add_notice(__('You must be aged 18 or over on 30 July 2017.'), 'error');
            }
        }
    }

    /**
     * Update the order meta with field value
     */
    public function checkout_field_update_user_meta($user_id) {
        if (!empty($_POST['gender'])) {
            update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
        }

        if (!empty($_POST['auk_membership_number'])) {
            update_user_meta($user_id, 'auk_membership_number', preg_replace("/[^0-9,.]/", "", sanitize_text_field($_POST['auk_membership_number'])));
        }

        if (!empty($_POST['privacy'])) {
            update_user_meta($user_id, 'privacy', sanitize_text_field($_POST['privacy']));
        }
        if (!empty($_POST['age18'])) {
            update_user_meta($user_id, 'age18', sanitize_text_field($_POST['age18']));
        }
    }

    /*
     * Remove checkout fields from billing
     */

    public function checkout_remove_fields($fields) {
        unset($fields['billing']['billing_company']);
        return $fields;
    }

    public function product_is_visible($visible, $product_id) {
        if (!empty($visible)) {

            $categories = wp_get_post_terms($product_id, 'product_cat', array("fields" => "names"));

            if (in_array('Rider', $categories) && current_user_can('access_rider_area')) {
                return true;
            }

            if (in_array('Volunteer', $categories) && current_user_can('access_volunteer_area')) {
                return true;
            }
// If in either Rider or Volunteer category and they don't
// have permission then don't show product
            if (in_array('Rider', $categories) or in_array('Volunteer', $categories)) {
                return false;
            }
        }

        return $visible;
    }

    /*
     * Prevent direct access to rider or volunteer products if user
     * does not have access.
     */

    public function merchandise_access() {

        if (is_single()) {

            global $post;

            $product_id = $post->ID;

            $categories = wp_get_post_terms($product_id, 'product_cat', array("fields" => "names"));

            if (in_array('Rider', $categories) && current_user_can('access_rider_area')) {
                return;
            }

            if (in_array('Volunteer', $categories) && current_user_can('access_volunteer_area')) {
                return;
            }
// If in either Rider or Volunteer category and they don't
// have permission then don't show product
            if (in_array('Rider', $categories) or in_array('Volunteer', $categories)) {
                auth_redirect();
            }
        }
    }

    public function enqueueAssets() {

        if (function_exists('is_checkout')) {
            if (is_checkout()) {
                wp_register_script('woocheckout', plugins_url('../js/checkout.js', __FILE__), array('jquery'), '', true);
                wp_enqueue_script('woocheckout');
            }
        }
    }

    /*
     * return array of product ids for a given SKU
     */

    private function getProdBySku($prod_id) {
        $sku = get_post_meta($prod_id, '_sku', true);

        if ($sku != '') {

            $args = array(
                'post_type' => 'product',
                'post_status' => 'any',
                'meta_key' => '_sku',
                'meta_value' => $sku,
                'fields' => 'ids'
            );
            $query = new WP_query($args);
            $prod_ids = $query->get_posts();
        } else {
            $prod_ids[] = $prod_id;
        }
        return $prod_ids;
    }

    private function get_early_entry($id) {
// Look in cache first else execute db query

        if (!$lookup = wp_cache_get($id, "early")) {
// Not in cache so execite sql , and store in cache
            global $wpdb;
            $table_name = $wpdb->prefix . 'early_entry';
            $sql = 'SELECT lookup FROM '
                    . $table_name
                    . ' WHERE id = "'
                    . $id
                    . '";';

            $data = $wpdb->get_row($sql);
            if ($data !== null) {
                $lookup = json_decode($data->lookup, true);
                wp_cache_set($id, $lookup, "early", 86400);
            } else {
                $lookup = [];
            }
        }

        return $lookup;
    }

// check if already entered, only called during early entry
    private function check_early_entry($id) {
// Look in cache first else execute db query

        if (!$result = wp_cache_get($id, "early")) {
// Not in cache so execute sql , and store in cache
            global $wpdb;
            $table_name = $wpdb->prefix . 'usermeta';
            $sql = 'SELECT meta_value FROM '
                    . $table_name
                    . ' WHERE meta_key = "auk_membership_number"'
                    . ' AND meta_value = "'
                    . $id
                    . '";';

            $data = $wpdb->get_row($sql);

            if ($data === null) {
                $result = false;
            } else {
                $result = true;
            }

            wp_cache_set($id, $result, "early", 100);
        }

        return $result;
    }

    /*
     * Set paid Paypal orders to complete if only virtual products
     */

    function autocomplete_paid_virtual_orders($order_status, $order_id) {
        $order = new WC_Order($order_id);
        if ('processing' == $order_status && ('on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status)) {

            $virtual_order = null;
            if (count($order->get_items()) > 0) {
                foreach ($order->get_items() as $item) {
                    if ('line_item' == $item['type']) {
                        $_product = $order->get_product_from_item($item);
                        if (!$_product->is_virtual()) {
                            $virtual_order = false;
                            break;
                        } else {
                            $virtual_order = true;
                        }
                    }
                }
            }

            if ($virtual_order) {
                return 'completed';
            }
        }
        return $order_status;
    }

    public function Payment_handling_fees() {
        global $woocommerce;
// Get the payment gateway
        $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
        $current_gateway = '';
        if (!empty($available_gateways)) {
// Chosen Method
            if (isset($woocommerce->session->chosen_payment_method) && isset($available_gateways[$woocommerce->session->chosen_payment_method])) {
                $current_gateway = $available_gateways[$woocommerce->session->chosen_payment_method];
            } elseif (isset($available_gateways[get_option('woocommerce_default_gateway')])) {
                $current_gateway = $available_gateways[get_option('woocommerce_default_gateway')];
            } else {
                $current_gateway = current($available_gateways);
            }
        }


        if ($current_gateway != '') {
            $current_gateway_id = $current_gateway->id;
            $extra_charges_id = 'woocommerce_' . $current_gateway_id . '_extra_charges';
            $extra_charges_type = $extra_charges_id . '_type';
            $extra_charges = (float) get_option($extra_charges_id);
            $extra_charges_type_value = get_option($extra_charges_type);
            if ($extra_charges) {
                $fee_title = $current_gateway->method_title . ' Extra Fees';
                if ($extra_charges_type_value == 'percentage') {
                    $fee = round(($woocommerce->cart->get_displayed_subtotal() * $extra_charges) / 100, 2);
                } else {
                    $fee = $extra_charges;
                }
                $woocommerce->cart->add_fee($fee_title, $fee, true, 'standard');
            }
        }
    }

    public function recalc_totals_script() {
        wp_enqueue_script('recalc-totals', plugins_url('../js/recalc-totals.js', __FILE__), array('wc-checkout'), false, true);
    }

    public function extra_fee_form_fields() {
        global $woocommerce;
// Get current tab/section
        $current_tab = ( empty($_GET['tab']) ) ? '' : sanitize_text_field(urldecode($_GET['tab']));
        $current_section = ( empty($_REQUEST['section']) ) ? '' : sanitize_text_field(urldecode($_REQUEST['section']));

        if ($current_tab == 'checkout' && $current_section != '' && ($current_section == 'bacs' || $current_section == 'cod' || $current_section == 'cheque' || $current_section == 'paypal')) {
            $gateways = $woocommerce->payment_gateways->payment_gateways();
            foreach ($gateways as $gateway) {
                if ((strtolower(get_class($gateway)) == 'wc_gateway_bacs' || strtolower(get_class($gateway)) == 'wc_gateway_cheque' || strtolower(get_class($gateway)) == 'wc_gateway_cod' || strtolower(get_class($gateway)) == 'wc_gateway_paypal') && strtolower(get_class($gateway)) == 'wc_gateway_' . $current_section) {
                    $current_gateway = $gateway->id;
                    $extra_charges_id = 'woocommerce_' . $current_gateway . '_extra_charges';
                    $extra_charges_type = $extra_charges_id . '_type';
                    if (isset($_REQUEST['save'])) {
                        update_option($extra_charges_id, $_REQUEST[$extra_charges_id]);
                        update_option($extra_charges_type, $_REQUEST[$extra_charges_type]);
                    }
                    $extra_charges = get_option($extra_charges_id);
                    $extra_charges_type_value = get_option($extra_charges_type);
                }
            }
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    $data = '<h4>Add Extra Charges</h4><table class="form-table">';
                    $data += '<tr valign="top">';
                    $data += '<th scope="row" class="titledesc">Extra Charges</th>';
                    $data += '<td class="forminp">';
                    $data += '<fieldset>';
                    $data += '<input style="" name="<?php echo $extra_charges_id ?>" id="<?php echo $extra_charges_id ?>" type="text" value="<?php echo $extra_charges ?>"/>';
                    $data += '<br /></fieldset></td></tr>';
                    $data += '<tr valign="top">';
                    $data += '<th scope="row" class="titledesc">Extra Charges Type</th>';
                    $data += '<td class="forminp">';
                    $data += '<fieldset>';
                    $data += '<select name="<?php echo $extra_charges_type ?>"><option <?php if ($extra_charges_type_value == "add") echo "selected=selected" ?> value="add">Total Add</option>';
                    $data += '<option <?php if ($extra_charges_type_value == "percentage") echo "selected=selected" ?> value="percentage">Total % Add</option>';
                    $data += '<br /></fieldset></td></tr></table>';
                    $('.form-table:last').after($data);

                });
            </script>

            <?php
        }
    }

    function remove_password_strength() {
        if (wp_script_is('wc-password-strength-meter', 'enqueued')) {
            wp_dequeue_script('wc-password-strength-meter');
        }
    }

    function paypal_disable($available_gateways) {
        global $woocommerce;
        if (isset($available_gateways['paypal']) && !current_user_can('access_paypal')) {
            unset($available_gateways['paypal']);
        }
        if (isset($available_gateways['bacs']) && current_user_can('access_paypal')) {
            unset($available_gateways['bacs']);
        }



        return $available_gateways;
    }

    /*
     * Add the data of birth field to insurance products
     */

    public function add_date_of_birth_field() {
        global $post;

        if (has_term(['Insurance'], 'product_cat', $post)) {
            $output = '<div class="variations">'
                    . '<div class="form-group">'
                    . '<label class="col-sm-3 control-label" for="dob">'
                    . 'Date of Birth'
                    . '</label>'
                    . '<div class="col-sm-9">'
                    . '<input type="text" name="dob" placeholder="yyyy-mm-dd">'
                    . '</div>'
                    . '</div>'
                    . '</div>';


            echo $output;
        }
    }

    public function date_of_birth_validation($true, $product_id, $product_qty) {
        $post = get_post($product_id);
        if (has_term(['Insurance'], 'product_cat', $post)) {

            if (empty($_REQUEST['dob'])) {
                wc_add_notice(__('Please provide your date of birth', 'LEL2017Plugin'), 'error');
                return false;
            }

            $dob = $_REQUEST['dob'];

            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dob)) {
                wc_add_notice(__('Please provide your date of birth in yyyy-mm-dd format', 'LEL2017Plugin'), 'error');
                return false;
            }
            /*
             * Validate they don't live in the UK
             */
            $user_id = get_current_user_id();
            if ($user_id === 0) {
                wc_add_notice(__('You must be logged in to pruchase this product', 'LEL2017Plugin'), 'error');
                return false;
            };
            $billing_country = get_user_meta($user_id, 'billing_country', true);
            if ($billing_country === 'GB') {
                wc_add_notice(__('Riders resident in the UK may not purchase this product', 'LEL2017Plugin'), 'error');
                return false;
            }
        }
        /*
         * 18-65 Insurance
         */
        if ($product_id === 8917) {
            $dob = strtotime($_REQUEST['dob']);
            $last_date = strtotime('2017-08-10');
            $EighteenYearsAgo = strtotime("-18 years", $last_date);
            $SixtySixYearsAgo = strtotime("-66 years", $last_date);

            if ($dob > $EighteenYearsAgo | $dob <= $SixtySixYearsAgo) {
                wc_add_notice(__('You must be aged 18-65 on 10th August 2017 to purchase this product', 'LEL2017Plugin'), 'error');
                return false;
            }
        }

        /*
         * 66-75 Insurance
         */
        if ($product_id === 8788) {
            $dob = strtotime($_REQUEST['dob']);
            $last_date = strtotime('2017-08-10');
            $SixtySixYearsAgo = strtotime("-66 years", $last_date);
            $SeventySixYearsAgo = strtotime("-76 years", $last_date);

            if ($dob > $SixtySixYearsAgo | $dob <= $SeventySixYearsAgo) {
                wc_add_notice(__('You must be aged 66-75 on 10th August 2017 to purchase this product', 'LEL2017Plugin'), 'error');
                return false;
            }
        }




        /*
         * Valid date of birth has been provided or not an insurance product
         */
        return $true;
    }

    /*
     * Save the data of birth
     */

    public function save_date_of_birth_field($cart_item_data, $product_id) {
        if (isset($_REQUEST['dob'])) {
            $dob_timestamp = strtotime($_REQUEST['dob']);
            $dob_formatted = date('D d F Y', $dob_timestamp);
            $cart_item_data['date_of_birth'] = $dob_formatted;
            $cart_item_data['unique_key'] = md5(microtime() . rand());
        }
        return $cart_item_data;
    }

    /*
     * Render date of birth in cart meta data
     */

    public function render_meta_on_cart_and_checkout($cart_data, $cart_item = null) {
        $custom_items = [];
        if (!empty($cart_data)) {
            $custom_items = $cart_data;
        }
        if (isset($cart_item['date_of_birth'])) {
            $custom_items[] = ['name' => 'Date of Birth', 'value' => $cart_item['date_of_birth']];
        }
        return $custom_items;
    }

    /*
     * Render date of birth in order email and checkout
     */

    public function date_of_birth_order_meta_handler($item_id, $values, $cart_item_key) {
        if (isset($values['date_of_birth'])) {
            wc_add_order_item_meta($item_id, 'Date of Birth', $values['date_of_birth']);
        }
    }

    /*
     * Add custom cart processing fees
     */

    public function add_custom_cart_fee() {
        global $woocommerce;

        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        $extra_fee = 0;
        foreach ($woocommerce->cart->cart_contents as $key => $values) {
            $product_id = $values['product_id'];

            $fee_description = get_post_meta($product_id, '_fee_description', true);
            $fee_amount = (int) get_post_meta($product_id, '_fee_amount', true);

            if ($fee_description && $fee_amount && $fee_amount > 0) {
                $extra_fee = $extra_fee + $fee_amount;
            }
        }
        if ($extra_fee > 0) {
            $woocommerce->cart->add_fee($fee_description, $extra_fee, $taxavle = false, $tax_class = '');
        }
    }

    public function add_fees_product_tab($product_data_tabs) {
        $product_data_tabs['fees-tab'] = array(
            'label' => __('Fees', 'LEL2017Plugin'),
            'target' => 'fees_product_data',
        );
        return $product_data_tabs;
    }

    public function add_custom_fees() {
        global $woocommerce, $post;

        echo '<div id="fees_product_data" class="panel woocommerce_options_panel">';

// custom fee description
        woocommerce_wp_text_input(
                array(
                    'id' => '_fee_description',
                    'label' => __('Fee Description', 'woocommerce'),
                    'placeholder' => 'Fee description',
                    'desc_tip' => 'true',
                    'description' => __('Enter description of the addtional fee here.', 'woocommerce')
                )
        );

// Fee amount (in integer amounts of chosen currency)
        woocommerce_wp_text_input(
                array(
                    'id' => '_fee_amount',
                    'label' => __('Fee Amount', 'woocommerce'),
                    'placeholder' => 'Fee amount',
                    'desc_tip' => 'true',
                    'description' => __('Enter the additional fee amount here.', 'woocommerce'),
                    'type' => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min' => '0'
                    )
                )
        );

        echo '</div>';
    }

    /*
     * Save custom fee fields
     */

    public function add_custom_fees_save($post_id) {
// Text Field
        $woocommerce_fee_description = $_POST['_fee_description'];
        if (!empty($woocommerce_fee_description))
            update_post_meta($post_id, '_fee_description', esc_attr($woocommerce_fee_description));

// Number Field
        $woocommerce_fee_amount = $_POST['_fee_amount'];
        if (!empty($woocommerce_fee_amount))
            update_post_meta($post_id, '_fee_amount', esc_attr($woocommerce_fee_amount));
    }

    /*
     * Save the lifetime group
     */

    public function save_lifetime_group($cart_item_data, $product_id) {
        $lifetime_group = get_post_meta($product_id, '_lel_lifetime_group', true);


        if (!empty($lifetime_group)) {

            $cart_item_data['_lel_lifetime_group'] = $lifetime_group;
        }
        return $cart_item_data;
    }

    public function lifetime_group_order_meta_handler($item_id, $values, $cart_item_key) {
        if (isset($values['_lel_lifetime_group'])) {
            wc_add_order_item_meta($item_id, '_lel_lifetime_group', $values['_lel_lifetime_group']);
        }
    }

    /*
     * Export insurance orders
     */

    function csv_export_insurance() {


        global $wpdb;
        global $woocommerce;


        $filename = 'lel-insurance-orders-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'First Name',
            'Last Name',
            'Date of Birth',
            'Email',
            'Address Line 1',
            'Address Line 2',
            'City',
            'State',
            'Postcode',
            'Country',
            'Product',
            'Issue Date',
            'Period of Cover',
            'Price'
        ];


        $fh = @fopen('php://output', 'w');


        fputcsv($fh, $header_row);

// Get insurance product ids
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'insurance',
                ],
        ]];
        $products = new WP_Query($args);

        $product_ids = [];
        while ($products->have_posts()) {
            $products->the_post();
            $product_ids[] = get_the_id();
        }

// Get a list of order ids we are interested in
        $product_ids_sql = array_map(function($p) {
            return "'" . esc_sql($p) . "'";
        }, $product_ids);
        $product_ids_sql = implode(',', $product_ids_sql);


        $sql = "SELECT order_id FROM "
                . $wpdb->prefix . "woocommerce_order_items"
                . " WHERE "
                . "order_item_id IN ("
                . "SELECT order_item_id FROM "
                . $wpdb->prefix . "woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND meta_value IN "
                . "( "
                . $product_ids_sql
                . " )"
                . " )";


        $order_ids = $wpdb->get_col($sql);



        $data_rows = [];
        foreach ($order_ids as $order_id) {

            $order = new WC_Order($order_id);

            if ($order->get_status() == 'completed' or $order->get_status() == 'processing') {

                $date_of_order = $order->get_date_paid()->date('D d F Y');


                $order_item = $order->get_items();

                foreach ($order_item as $item_id => $item) {


                    $product_id = wc_get_order_item_meta($item_id, '_product_id');



                    if (in_array($product_id, $product_ids)) {

                        $date_of_birth = wc_get_order_item_meta($item_id, 'Date of Birth');
                        $line_subtotal = wc_get_order_item_meta($item_id, '_line_subtotal');
                        $period_of_cover = wc_get_order_item_meta($item_id, 'period-of-cover');
                        $product_name = get_the_title($product_id);
                        // Customer details
                        $order_meta = get_post_meta($order_id);
                        // time of order


                        $row = [
                            $order_meta['_billing_first_name'][0],
                            $order_meta['_billing_last_name'][0],
                            $date_of_birth,
                            $order_meta['_billing_email'][0],
                            $order_meta['_billing_address_1'][0],
                            $order_meta['_billing_address_2'][0],
                            $order_meta['_billing_city'][0],
                            $order_meta['_billing_state'][0],
                            $order_meta['_billing_postcode'][0],
                            $this->common->get_country($order_meta['_billing_country'][0]),
                            $product_name,
                            $date_of_order,
                            $period_of_cover,
                            $line_subtotal
                        ];
                        $data_rows[] = $row;
                    }
                }
            }
        }
        // Now output formatted order data
        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    /*
     * Export orders
     */

    function csv_export_orders() {


        global $wpdb;
        global $woocommerce;


        $filename = 'lel-orders-' . date('Y-m-d H:i:s') . '.csv';
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

// Create file and output header row


        $header_row = [
            'First Name',
            'Last Name',
            'Email',
            'Address Line 1',
            'Address Line 2',
            'City',
            'State',
            'Postcode',
            'Country',
            'Product',
            'Date of Order',
            'Price'
        ];


        $fh = @fopen('php://output', 'w');


        fputcsv($fh, $header_row);


        // Get all the orders id
        // status of processing or completed
        $args = [
            'posts_per_page' => -1,
            'post_type' => 'shop_order',
            'post_status' => ['wc-completed', 'wc-processing']
        ];
        $orders = new WP_Query($args);

        $order_ids = [];
        while ($orders->have_posts()) {
            $orders->the_post();
            $order_ids[] = get_the_id();
        }

        unset($orders); // clear the variable


        $data_rows = [];
        foreach ($order_ids as $order_id) {
            $order = new WC_Order($order_id);
            if ($order->get_status() == 'completed' or $order->get_status() == 'processing') {

                $date_of_order = $order->order_date;

                $order_item = $order->get_items();

                foreach ($order_item as $item_id => $item) {

                    $product_id = wc_get_order_item_meta($item_id, '_product_id');
                    $line_subtotal = wc_get_order_item_meta($item_id, '_line_subtotal');
                    $product_name = get_the_title($product_id);

                    $mens_or_womens = $item['mens-or-womens'] ?? '';
                    $size = $item['size'] ?? '';

                    if ($size !== '') {
                        $product_name .= ' '
                                . $mens_or_womens
                                . ' '
                                . $size
                                . ' ( x'
                                . $item['qty']
                                . ')';
                    }

                    $product_name = str_replace("&#8217;", "'", $product_name);
                    // Customer details
                    $order_meta = get_post_meta($order_id);

                    //build output row
                    $row = [
                        $order_meta['_billing_first_name'][0],
                        $order_meta['_billing_last_name'][0],
                        $order_meta['_billing_email'][0],
                        $order_meta['_billing_address_1'][0],
                        $order_meta['_billing_address_2'][0],
                        $order_meta['_billing_city'][0],
                        $order_meta['_billing_state'][0],
                        $order_meta['_billing_postcode'][0],
                        $this->common->get_country($order_meta['_billing_country'][0]),
                        $product_name,
                        $date_of_order,
                        $line_subtotal
                    ];
                    $data_rows[] = $row;
                }
            }
        }
        // Now output formatted order data
        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);
        die();
    }

    public function rest_api_init() {
// Insurance CSV export
        register_rest_route('lel/v1', '/csv/ridersinsurance', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_insurance'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );

        // Orders CSV export
        register_rest_route('lel/v1', '/csv/orders', [
            'methods' => 'GET',
            'callback' => [$this, 'csv_export_orders'],
            'permission_callback' => function () {
        return current_user_can('manage_options');
    }
                ]
        );
    }

}
