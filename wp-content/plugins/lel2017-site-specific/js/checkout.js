/*
 * Phil Whitehurst
 *
 * Custom functionalility in checkout front end
 */

jQuery(function () {

    jQuery('#billing_country').on('change', function () {

        if (this.value === 'GB') {

            jQuery('#billing_state_field').hide();


        }
        else {
            jQuery('#billing_state_field').show();

        }

    });


}
);




