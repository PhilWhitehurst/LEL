
var PrevComponent = flight.component(function () {
    /*
     * Updated page offset by subtracing resultsPerPage
     * trigger event passed in attributes
     */

    this.attributes({
        trigger_event: 'Search',
        resultsPerPage: null,
        resultsOffset: null

    });
    this.prev = function () {
        var resultsOffset = parseInt(jQuery(this.attr.resultsOffset).val());
        if (!jQuery.isNumeric(resultsOffset)) {
            resultsOffset = 0;
        }

        var resultsPerPage = parseInt(jQuery(this.attr.resultsPerPage).val());
        resultsOffset = resultsOffset - resultsPerPage;
        resultsOffset = (resultsOffset <= 0 ? 0 : resultsOffset);
        jQuery(this.attr.resultsOffset).val(resultsOffset);
        this.trigger(document, this.attr.trigger_event, {
            resultsOffset: resultsOffset
        });
    };
    this.after('initialize', function () {
        this.on('click', this.prev);
    });
});


