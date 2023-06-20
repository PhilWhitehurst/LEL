
var RiderListComponent = flight.component(function () {
    this.attributes({
        results_returned_trigger_event: 'ResultsReturned',
        no_results_returned_trigger_event: 'NoResultsReturned'
    });
    this.updateRiderList = function (evt, data) {

        var status = data.status;
        var riders = data.riders;
        if (status === 'success') {
            jQuery(this.node).html('');
            var designated_starts = jQuery('#searchDesignatedStart').html();
            _.each(riders, function (rider) {

                rider["designated_starts"] = designated_starts;

                jQuery(this.node).append(Templates.riderList(rider));

            }, this);


            var l = riders.length;
            for (i = 0; i < l; i++) {
                if (riders[i].designated_start) {
                    jQuery(' #designated' + riders[i].id)
                            .val(riders[i].designated_start);
                }
            }
            if (l > 0) {
                this.trigger(document, this.attr.results_returned_trigger_event);
            } else {
                this.trigger(document, this.attr.no_results_returned_trigger_event);
            }

        }
    };
    this.after('initialize', function () {
        this.on(document, 'SearchResults', this.updateRiderList);
    });
});

