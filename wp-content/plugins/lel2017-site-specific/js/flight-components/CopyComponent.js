
var CopyComponent = flight.component(function () {
    /*
     * Copy the value of a form field and trigger a copy done event
     */


    this.copy = function () {

        var value = jQuery(this.attr.selector, this.node).val();
        var data = {value: value};

        this.trigger(document, this.attr.trigger_event, data);
    };
    this.after('initialize', function () {
        this.on(jQuery('btn', this.node), 'click', this.copy);
    });
});


