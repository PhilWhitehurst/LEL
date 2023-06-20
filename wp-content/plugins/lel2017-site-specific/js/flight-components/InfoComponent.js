
var InfoComponent = flight.component(function () {
    /*
     * Outputs the info message returned from an Ajax submit
     *
     */
    this.output = function (evt, data) {

        var status = data.status;
        var info = data.info;
        if (status === 'success') {
            jQuery(this.node)
                    .removeClass('alert')
                    .removeClass('alert-info')
                    .removeClass('alert-danger')
                    .addClass('alert')
                    .addClass('alert-info')
                    .html(info);
            var node = this.node;

        }

    };
    this.after('initialize', function () {
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.output);
        }

    });
});


