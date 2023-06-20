var RedirectComponent = flight.component(function () {
    /*
     * Component that combines all the other components for
     * managing a rider list
     */


    this.attributes({
        url: '',
        listen_events: []


    });

    this.init = function () {
    };


    this.redirect = function (evt, data) {

        if (data.status === 'success') {

            window.location.replace(this.attr.url);
        }

    };


    this.after('initialize', function () {
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.redirect);
        }


    });

});