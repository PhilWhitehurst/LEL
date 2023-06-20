
var OffsetComponent = flight.component(function () {
    /*
     * Update the offset when search results are returned
     */
    this.attributes({
        listen_event: 'SearchResults'
    })

    this.update = function (evt, data) {

        jQuery(this.node).val(data.offset);
    };

    this.after('initialize', function () {

        this.on(document, this.attr.listen_event, this.update);

    });

});


