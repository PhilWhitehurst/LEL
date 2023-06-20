var RestComponent = flight.component(function () {
    /*
     * Component to submit form contents to a resturl
     */


    this.attributes({
        trigger_event: '',
        offline: '',
        offline_store: '',
        reset_form: '',
        listen_event: '',
        teardown_event: '',
        method: 'POST',
        resturl: '',
        X_WP_Nonce: '',
        security_token: 'Security token expired. Please refresh the page.',
        tracking_rate: 'Please slow down rate of tracking requests or you will be banned for a period'



    });

    this.store = localforage.createInstance({
        name: "lel_offline"
    });


    this.Submit = function (evt) {

        evt.preventDefault(); // Prevent default form submit
        var data = '';
        var ID = '';
        var rider = '';
        var _this = this;
        if (this.attr.method !== 'DELETE') {
            var data = jQuery(this.node).serialize(); // Grab form data
        } else {
            // Delete needs a slightly different rest url
            ID = jQuery('input[name="ID"]', this.node).val();
        }

        rider = jQuery('input[name="rider"]', this.node).val();
        if (this.attr.reset_form === 'yes') {
            this.node.reset();
        }



        if (rider !== undefined) {
            this.setCookie('tracking-rider', rider, 1);
        }

        // submit to resturl

        jQuery.ajax({
            dataType: "json",
            headers: {
                'X-WP-Nonce': this.attr.X_WP_Nonce
            },
            url: this.attr.resturl + ID,
            type: this.attr.method,
            data: data,
            context: this,
            success: function (response) {

                this.trigger(document, this.attr.trigger_event, response);
                this.Resend();
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

                if (response.readyState === 0 && this.attr.offline === 'Yes') {
                    // Get timestamp
                    var d = new Date();
                    var n = d.getTime();
                    // Now trigger scan meessage
                    var days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                    var day = days[d.getDay()];
                    var day_num = d.getDate();
                    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var mon = months[d.getMonth()];
                    var y = d.getFullYear();

                    var hour = String(d.getHours());
                    if (hour.length === 1) {

                        hour = '0' + hour;
                    }
                    var min = String(d.getMinutes());
                    if (min.length === 1) {
                        min = '0' + min;
                    }


                    var time_stamp = day + ' ' + day_num + ' ' + mon + ' ' + hour + ':' + min;
                    // Store the data to send later
                    // The same code with localForage:
                    var key = String(n);
                    data = data + '&timestamp=' + Math.floor(n / 1000);
                    _this.store.setItem(key, data)
                            .then(function () {
                                var key_pairs = data.split('&');
                                var scan = {
                                    'ID': 'offline',
                                    'first_name': '',
                                    'last_name': '',
                                    'country': '',
                                    'time_in_hand': '',
                                    'direction': ''
                                };
                                for (var i = 0; i < key_pairs.length; i++) {
                                    var variable = key_pairs[i].split('=');
                                    scan[ variable[0]] = variable[1];
                                }

                                scan["rider_id"] = scan["rider"];
                                scan["timestamp"] = time_stamp;
                                var msg = {
                                    status: 'success',
                                    msg: 'No internet connection. Scan stored locally to send later',
                                    result: [scan]
                                };

                                _this.trigger(document, _this.attr.trigger_event, msg);

                                _this.store.length().then(function (numberOfKeys) {
                                    var msg = {
                                        count: numberOfKeys
                                    };
                                    _this.trigger(document, 'offline-count', msg);
                                    return;
                                });


                            });
                }
            }
        });
    };


    this.Resend = function (evt, msg) {
        failed = 0;
        if (msg) {
            failed = msg.failed;
        }


        var _this = this;


        _this.store.length().then(function (numberOfKeys) {
            if (numberOfKeys > 0) {
                _this.store.key(0)
                        .then(function (key) {

                            _this.store.getItem(key)
                                    .then(function (value) {
                                        // Submit to resturl
                                        var promise = jQuery.ajax({
                                            dataType: "json",
                                            headers: {
                                                'X-WP-Nonce': _this.attr.X_WP_Nonce
                                            },
                                            url: _this.attr.resturl,
                                            type: _this.attr.method,
                                            data: value,
                                            context: _this});

                                        // Success from ajax call
                                        promise.done(function (response) {
                                            _this.trigger(document, this.attr.trigger_event, response);
                                            // remove local copy of scan
                                            _this.store.removeItem(key)
                                                    .then(function () {
                                                        _this.store.length().then(function (numberOfKeys) {
                                                            msg = {
                                                                count: numberOfKeys
                                                            };
                                                            _this.trigger(document, 'offline-count', msg);
                                                            // Trigger processing of the next offline scan
                                                            var msg = {
                                                                failed: 0
                                                            }
                                                            _this.trigger(document, 'online-restored');
                                                        });
                                                    });

                                        });
                                        // Ajax call failed, if 5 in a row stop else try again in 3 seconds
                                        promise.fail(function (err) {
                                            if (failed < 5) {
                                                setTimeout(function () {
                                                    var msg = {
                                                        failed: failed
                                                    }
                                                    _this.trigger(document, 'online-restored', msg);
                                                }, 3000);
                                            }
                                        });

                                    });

                        });






            }

        })









    }


    this.setCookie = function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }


    this.after('initialize', function () {
        this.on('submit', this.Submit);
        this.on(document, this.attr.listen_event, this.Submit);
        if (this.attr.teardown_event != '') {
            this.on(this.attr.teardown_event, this.teardown);
        }
        if (this.attr.offline === 'Yes') {
            this.on(document, 'online-restored', this.Resend);
        }





    });
});