/*
 * Phil Whitehurst
 *
 * Handle My Details 
 */


RestComponent.attachTo('form#lel-my-details',
        {
            trigger_event: 'Result',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'mydetails/',
            method: wp_var.method
        });

MessageComponent.attachTo('#lel-update-message',
        {
            listen_events: ['Result']
        });