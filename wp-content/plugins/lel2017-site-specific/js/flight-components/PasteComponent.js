
var PasteComponent = flight.component(function () {

    this.attributes({
        trigger_event: 'PasteDone',
        events: null

    });


    this.paste = function (evt, data) {
        /*
         * Match the event to the selector
         */
        var matched_elements = 0;
        l = this.attr.events.length;
        for (var i = 0; i < l; i++) {
            if (this.attr.events[i].listen_event === evt.type) {
                matched_elements = jQuery(this.attr.events[i].selector, this.node)
                        .val(data.value)
                        .length;
            }
        }
        ;
        if (matched_elements > 0) {
            var response = {
                status: 'success',
                msg: 'Copy was successful'
            };
            this.trigger(document, this.attr.trigger_event, response);
        }
    };

    this.after('initialize', function () {
        l = this.attr.events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.events[i].listen_event, this.paste);
        }

    });
});


