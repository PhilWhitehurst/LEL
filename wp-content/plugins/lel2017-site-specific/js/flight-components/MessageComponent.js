
var MessageComponent = flight.component(function () {
    /*
     * Outputs the message returned from an Ajax submit
     *
     */
    this.output = function (evt, data) {


        var status = data.status;
        var msg = data.msg;

        if (status === 'success') {
            jQuery(this.node)
                    .removeClass('alert')
                    .removeClass('alert-danger')
                    .addClass('alert')
                    .addClass('alert-success')
                    .html(msg);
            var node = this.node;
            setTimeout(function () {

                jQuery(node)
                        .removeClass('alert')
                        .removeClass('alert-success')
                        .html('');
            }, 1000);
        }
        else {
            jQuery(this.node)
                    .removeClass('alert')
                    .removeClass('alert-success')
                    .addClass('alert')
                    .addClass('alert-danger')
                    .html(msg);
        }
    };
    this.after('initialize', function () {
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.output);
        }

    });
});




