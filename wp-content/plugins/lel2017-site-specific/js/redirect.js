/*
 * Redirect on successful tracking
 */

RedirectComponent.attachTo('div#redirect',
        {
            listen_events: ['Tracking'],
            url: wp_var.url,
        });

