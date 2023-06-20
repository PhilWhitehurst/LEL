/*
 * Phil Whitehurst
 * September 2016
 * Handle the bar code scan logic
 */



ToggleComponent.attachTo('form#bar-code button',
        {
            on: 'Change to Manual Entry',
            off: 'Change to Scan Entry',
            onEvent: 'ScanEntry',
            offEvent: 'ManualEntry'
        });

RestComponent.attachTo('form#bar-code',
        {
            trigger_event: wp_var.trigger_event,
            offline: 'Yes',
            offline_store: 'Tracking',
            reset_form: 'yes',
            listen_event: 'AutoScan',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'tracking/',
            method: wp_var.method
        });

AutoScanComponent.attachTo('form#bar-code input[type="text"]',
        {
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'tracking/',
            method: wp_var.method,
        });









