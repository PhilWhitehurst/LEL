var OnlineOfflineComponent = flight.component(function () {
    /*
     * Component to indicate if online or offline
     */



    this.online = function (event) {
        if (navigator.onLine) {
            // online
            jQuery(this.node)
                    .addClass('alert-success')
                    .removeClass('alert-danger')
                    .html('Connected to Internet');
            // Let other components know that the Internet connection is back
            var msg = {
                status: 'online',
                msg: 'Internet connection restored'
            };
            this.trigger(document, 'online-restored', msg);


        } else {
            //offline
            jQuery(this.node)
                    .addClass('alert-danger')
                    .removeClass('alert-success')
                    .html('Not connected to Internet');

        }

    };



    this.after('initialize', function () {
        this.on(window, 'online', this.online);
        this.on(window, 'offline', this.online);
        this.online();

    });
});


