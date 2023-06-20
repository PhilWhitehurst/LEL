/*
 * Phil Whitehurst
 * September 2016
 * Handle Set My Control page logic
 */

RestComponent.attachTo('#set-my-control',
        {
            trigger_event: 'myControlUpdated',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'mycontrol/',
            method: wp_var.method
        });

MessageComponent.attachTo('form div.lel-message',
        {
            listen_events: ['myControlUpdated'],
        });
InfoComponent.attachTo('#my-control-info',
        {
            listen_events: ['myControlUpdated'],
        });






