var RiderListComponent = flight.component(function () {
    /*
     * Component that combines all the other components for
     * managing a rider list
     */


    this.attributes({
        template: '',
        notetemplate: '',
        table_html: '',
        limit: 0,
        clear: 'yes',
        listen_events: ['ListResult'],
        clear_event: '',
        delete_event: '',
        trigger_event: '',
        teardown_event: '',
        method: 'POST',
        resturl: '',
        noteresturl: '',
        msg_pos: 'top',
        X_WP_Nonce: ''

    });

    this.init = function () {
        /*
         * Empty the dom node we are attached to.
         */
        jQuery(this.node).html('');

        var html = '<div class="lel-message"></div>'
                + '<br>';

        /*
         * Position message above rider list (default)
         */
        if (this.attr.msg_pos === 'top') {
            jQuery(this.node).append(html);
        }
        // Rider notes area
        var html = '<div id="rider-notes"></div>';
        jQuery(this.node).append(html);
        this.notes = jQuery('#rider-notes', this.node);

        /*
         * Set default container for rider list
         */
        this.container = this.node;
        /*
         * Add table structure if required
         */
        if (this.attr.table_html !== '') {
            jQuery(this.node).append(this.attr.table_html);
            this.container = jQuery('tbody.list', this.node);
        }
        else {
            jQuery(this.node).append('<div class="list"></div>');
            this.container = jQuery('div.list', this.node);
        }

        /*
         * Alternatively position message after rider list
         */
        if (this.attr.msg_pos !== 'top') {
            jQuery(this.node).append(html);
        }

        var msg_node = jQuery('.lel-message', this.node);

        var listen_events = this.attr.noteresturl === '' ? [this.attr.trigger_event] : [this.attr.trigger_event, 'TrackingUpdate'];

        MessageComponent.attachTo(msg_node,
                {
                    listen_events: listen_events
                });

    }

    this.output = function (evt, data) {



        // Remove any Rest components attached to existing rider list
        // before we remove existing rider list

        if (this.attr.clear === 'yes' && evt.type !== 'TrackingUpdate') {
            jQuery(this.container).html('');
        }

        if (this.attr.clear === 'yes' && data.notes && evt.type === 'TrackingUpdate') {
            jQuery(this.notes).html('');
        }



        // Process notes if found
        if (data.notes) {
            jQuery(this.notes).html(Templates[this.attr.notetemplate](data.notes));
            RestComponent.attachTo('form.rider-notes',
                    {
                        trigger_event: 'TrackingUpdate',
                        teardown_event: this.attr.teardown_event,
                        X_WP_Nonce: this.attr.X_WP_Nonce,
                        resturl: this.attr.noteresturl,
                        method: 'PUT'
                    })

        }

        var riders = data.result;



        _.each(riders, function (rider) {

            jQuery(this.container).prepend(Templates[this.attr.template](rider));
            if (this.attr.limit > 0)
            {
                var children = jQuery(this.container).children();
                if (children.length > this.attr.limit) {
                    children.last().remove();

                }
            }
        }, this);




        RestComponent.attachTo('form.rider-entry',
                {
                    trigger_event: this.attr.trigger_event,
                    teardown_event: this.attr.teardown_event,
                    X_WP_Nonce: this.attr.X_WP_Nonce,
                    resturl: this.attr.resturl,
                    method: this.attr.method
                })

    }

    this.clear = function (evt, data) {
        if (data.status === "success") {
            var data = {};
            data.result = [];
            this.output('', data);
        }
    }

    this.delete = function (evt, data) {
        if (data.status === "success") {
            var id = data.id;
            var element = 'tr[data-id="' + data.id + '"]';
            jQuery(element, this.node).remove();

        }
    }




    this.after('initialize', function () {
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.output);
        }
        this.on(document, this.attr.clear_event, this.clear);
        this.on(document, this.attr.delete_event, this.delete);

        this.init();

    });

});