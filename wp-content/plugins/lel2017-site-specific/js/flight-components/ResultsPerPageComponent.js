
var ResultsPerPageComponent = flight.component(function () {
    /*
     * Trigger message if selected results per page value changes
     */
    this.attributes({
        trigger_event: 'ResultsPerPage'

    });
    this.notify = function () {

        this.trigger(document, this.attr.trigger_event, {
            resultsPerPage: jQuery(this.node).val()
        });
    };
    this.after('initialize', function () {

        this.on('change', this.notify);
        this.notify();
    });
});



