/*
 * Request chart
 */

AutoTrackComponent.attachTo('div#auto-track',
        {
            trigger_event: 'Tracking',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + wp_var.rest_path,
            method: wp_var.rest_method,
            security_token: wp_var.security_token,
            tracking_rate: wp_var.tracking_rate

        });

