
var ExtraComponent = flight.component(function () {
    /*
     * Outputs the extra content returned from an Ajax submit
     *
     */
    this.output = function (evt, data) {

        var extra = data.extra;
        if (extra !== null) {
            jQuery(this.node)
                    .html(extra);
        }

    };
    this.after('initialize', function () {
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.output);
        }

    });
});