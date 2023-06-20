var AutoScanComponent = flight.component(function () {
    /*
     * Component to keep focus and auto submit form contents
     * on new scan data appearing 
     */
    var _this;

    this.attributes({
        teardown_event: '',
        method: 'POST'

    });

    this.init = function () {
        this.focus_on();
        _this = this;

    };

    this.focus_on = function () {
        jQuery(this.node)
                .blur(jQuery.proxy(this.focus, this))

        this.focus();
    }

    this.focus_off = function () {
        jQuery(this.node).off('blur');

    }


    this.focus = function () {

        jQuery(this.node).focus();
    }



    this.scanmode = function (e) {
        if (e === 'ManualEntry') {
            this.focus_off();
        }
        else {
            this.focus_on();
        }
    }


    this.after('initialize', function () {

        this.on(document, 'ManualEntry', this.focus_off);
        this.on(document, 'ScanEntry', this.focus_on);

        this.init();
    });

});