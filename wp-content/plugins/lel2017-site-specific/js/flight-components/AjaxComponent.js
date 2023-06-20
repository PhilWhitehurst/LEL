var AjaxComponent = flight.component(function () {
    /*
     * Component to submit a form via ajax
     */

    this.attributes({
        trigger_event: 'resultsReturned'

    });
    this.ajaxSubmit = function (evt) {
        evt.preventDefault();
        var $target = jQuery(this.node);
        var action = $target.attr('action');
// Grab form data
        var data = $target.serialize();
        jQuery.ajax({
            type: "POST",
            dataType: 'json',
            url: wp.ajaxurl + '?action=' + action,
            data: data,
            context: this,
            success: function (response) {

                this.trigger(document, this.attr.trigger_event, response);
                if (response.event) {
                    this.trigger(document, response.event);
                }
            }

        });
    };
    this.after('initialize', function () {
        this.on('submit', this.ajaxSubmit);
    });
});


