/*
 * If rider id found in tracking cookie then auto submit request for Chart
 */

var AutoTrackComponent = flight.component(function () {
    /*
     * Component to request chart from a resturl
     */


    this.attributes({
        trigger_event: '',
        method: 'GET',
        resturl: '',
        X_WP_Nonce: '',
        security_token: 'Security token expired. Please refresh the page.',
        tracking_rate: 'Please slow down rate of tracking requests or you will be banned for a period'


    });

    this.requestChart = function () {

        var rider;
        rider = this.getCookie('tracking-rider');

        if (rider !== '') {

            jQuery.ajax({
                dataType: "json",
                headers: {
                    'X-WP-Nonce': this.attr.X_WP_Nonce
                },
                url: this.attr.resturl,
                type: this.attr.method,
                data: {rider: rider},
                context: this,
                success: function (response) {

                    this.trigger(document, this.attr.trigger_event, response);

                },
                error: function (response) {
                    if (response.status === 503) {
                        var msg = {
                            status: 'error',
                            msg: this.attr.tracking_rate};
                        this.trigger(document, this.attr.trigger_event, msg);

                    }

                    if (response.status === 403) {
                        var msg = {
                            status: 'error',
                            msg: this.attr.security_token};
                        this.trigger(document, this.attr.trigger_event, msg);

                    }

                }
            });
        }

    }
    this.getCookie = function (cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }



    this.after('initialize', function () {
        this.requestChart();

    });

});


