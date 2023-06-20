/*
 * Phil Whitehurst
 * February 2016
 * Admin update of rider start waves and times
 */

/*
 * Enable Ajax search for riders 
 */
AjaxSearchComponent.attachTo('form[type="search"]');

/*
 * Output messages from SearchResults or Copy and Paste
 */

MessageComponent.attachTo('#lel-rider-return-msg', {
    listen_events: [
        'SearchResults',
        'PasteDone'
    ]
});
/*
 * Output Search results into designated waves update form.
 *
 */
RiderListComponent.attachTo('#lel-rider-wave-content');
/*
 * Enable copy for designated wave and start fields.
 */

CopyComponent.attachTo('#lel-designated-start-copy-form', {
    trigger_event: 'CopyDesignatedStart',
    selector: 'select'
});

/*
 * Enable paste for designated wave and designated start
 */
PasteComponent.attachTo('#js-rider-designated-starts-form', {
    events: [
        {
            listen_event: 'CopyDesignatedStart',
            selector: '.js_designated_start'
        }

    ]
});


/*
 * Convert designated waves update form to AJAX submission
 */
AjaxComponent.attachTo('#js-rider-designated-starts-form',
        {
            trigger_event: 'UpdatedWaves'
        });
/*
 * Output message for designated start update response
 */
MessageComponent.attachTo('#lel-designated-update-msg', {
    listen_events: [
        'UpdatedWaves'
    ]
});
/*
 * Hide or show the designated start update button based on whether
 * any search results returned.
 */
HideShowComponent.attachTo('#lel-designated-submit', {
    hide: [{event: 'NoResultsReturned'}],
    show: [{event: 'ResultsReturned'}]
});
/*
 * Page through the rider search results
 */
PrevComponent.attachTo('#lel-js-prev', {
    resultsOffset: '#lel-rider-page-options #resultsOffset',
    resultsPerPage: '#lel-rider-page-options #resultsPerPage'
});

NextComponent.attachTo('#lel-js-next', {
    resultsOffset: '#lel-rider-page-options #resultsOffset',
    resultsPerPage: '#lel-rider-page-options #resultsPerPage'
});
/*
 * Ensure the results per page and offset values are managed
 */
ResultsPerPageComponent.attachTo('#lel-rider-page-options #resultsPerPage');
OffsetComponent.attachTo('#lel-rider-page-options #resultsOffset');
