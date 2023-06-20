<?php

/*
 * Phil Whitehurst
 * All woocommerce add on functionality for the lel2017 child theme
 *
 */

//Change the Billing Address label
function lel2017_billing_field_strings($translated_text, $text, $domain) {
    switch ($translated_text) {
        case 'Billing Address' :
            $translated_text = __('Address', 'woocommerce');
            break;
        case 'Billing address' :
            $translated_text = __('Address', 'woocommerce');
            break;
    }
    return $translated_text;
}

add_filter('gettext', 'lel2017_billing_field_strings', 20, 3);



