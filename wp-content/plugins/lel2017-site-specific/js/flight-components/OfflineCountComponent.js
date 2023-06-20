var OfflineCountComponent = flight.component(function () {
    /*
     * Component to indicate if online or offline
     */



    this.count = function (event, msg) {
        if (msg.count > 0) {
            // We have offline scans

            var text = 'Offline Scans ' + msg.count;

            jQuery(this.node)
                    .addClass('alert')
                    .addClass('alert-danger')
                    .html(text);

        } else {
            //No more offline scans
            jQuery(this.node)
                    .removeClass('alert')
                    .removeClass('alert-danger')
                    .html('');

        }

    };



    this.after('initialize', function () {
        this.on(window, 'offline-count', this.count);



    });
});


