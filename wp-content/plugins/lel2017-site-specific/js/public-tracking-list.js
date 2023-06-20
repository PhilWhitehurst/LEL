/*
 * Handles the tracking list
 */

var table_html =
        [
            '<table class="table table-striped table-responsive">'
                    , '<thead>'
                    , '<tr>'
                    , '<th>'
                    , wp_var.Timestamp
                    , '</th>'
                    , '<th>'
                    , wp_var.Event
                    , '</th>'
                    , '<th>'
                    , wp_var.Control
                    , '</th>'
                    , '<th>'
                    , wp_var.Distance
                    , '</th>'
                    , '<th>'
                    , wp_var.TimeinHand
                    , '</th>'
                    , '</tr>'
                    , '</thead>'
                    , '<tbody class="list"></tbody>'

        ].join('\n');


RiderListComponent.attachTo('#' + wp_var.id,
        {
            template: 'publicTrackingList',
            table_html: table_html,
            limit: wp_var.limit,
            listen_events: ['Tracking'],
            clear_event: '',
            delete_event: '',
            teardown: '',
            trigger_event: '',
            X_WP_Nonce: '',
            resturl: '',
            method: '',
            clear: wp_var.clear
        });


