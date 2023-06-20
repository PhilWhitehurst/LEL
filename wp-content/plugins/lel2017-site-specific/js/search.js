/*
 * Phil Whitehurst
 * September 2016
 * Handle rider search logic
 */



RestComponent.attachTo('form#riderid-search',
        {
            trigger_event: 'SearchResult',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + (wp_var.public === 'yes' ? 'public/search/' : 'search/'),
            method: wp_var.method,
            security_token: wp_var.security_token,
            tracking_rate: wp_var.tracking_rate
        });

MessageComponent.attachTo('#lel-search-message',
        {
            listen_events: ['SearchResult']
        });



RestComponent.attachTo('form#lastname-search',
        {
            trigger_event: 'SearchResult',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + (wp_var.public === 'yes' ? 'public/search/' : 'search/'),
            method: wp_var.method,
            security_token: wp_var.security_token,
            tracking_rate: wp_var.tracking_rate
        });


RiderListComponent.attachTo('div#search-results',
        {
            template: 'riderSearchList',
            listen_events: ['SearchResult'],
            clear_event: 'Tracking',
            teardown: 'Tracking',
            trigger_event: 'Tracking',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + wp_var.rest_path,
            method: wp_var.rest_method,
            security_token: wp_var.security_token
        });

/*
 * Hide the search form.
 */
HideShowComponent.attachTo('div#rider-search', {
    hide: [{event: 'ScanEntry'}],
    show: [{event: 'ManualEntry'}]
});









