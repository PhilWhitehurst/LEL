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
                    , '<th>Control</th>'
                    , '<th>Distance (km)</th>'
                    , '<th>Time in Hand</th>'
                    , '<th>Direction</th>'
                    , '<th></th>'
                    , '</tr>'
                    , '</thead>'
                    , '<tbody class="list"></tbody>'

        ].join('\n');


RiderListComponent.attachTo('#' + wp_var.id,
        {
            template: 'volunteerTrackingNotes',
            notetemplate: 'volunteerRiderNotes',
            table_html: table_html,
            limit: wp_var.limit,
            listen_events: ['Tracking', 'TrackingUpdate'],
            clear_event: '',
            delete_event: 'TrackingUpdate',
            teardown: '',
            trigger_event: 'TrackingUpdate',
            X_WP_Nonce: wp_var.X_WP_Nonce,
            resturl: wp_var.resturl + 'tracking/',
            noteresturl: wp_var.resturl + 'tracking/notes/',
            method: 'DELETE',
            clear: wp_var.clear
        });