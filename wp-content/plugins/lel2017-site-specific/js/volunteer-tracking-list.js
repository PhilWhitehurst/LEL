/*
 * Handles the tracking list
 */

var table_html =
        [
            '<table class="table table-striped table-responsive">'
                    , '<thead>'
                    , '<tr>'
                    , '<th>Timestamp</th>'
                    , '<th>Event</th>'
                    , '<th>ID</th>'
                    , '<th>Name</th>'
                    , '<th>Country</th>'
                    , '<th>Time in Hand</th>'
                    , '<th>Direction</th>'
                    , '<th></th>'
                    , '</tr>'
                    , '</thead>'
                    , '<tbody class="list"></tbody>'

        ].join('\n');


RiderListComponent.attachTo('#' + wp_var.id,
        {
            template: 'volunteerTrackingList',
            table_html: table_html,
            limit: wp_var.limit,
            listen_events: ['Tracking'],
            clear_event: '',
            delete_event: 'DeleteResult',
            teardown: '',
            trigger_event: 'DeleteResult',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'tracking/',
            method: 'DELETE',
            clear: wp_var.clear
        });


