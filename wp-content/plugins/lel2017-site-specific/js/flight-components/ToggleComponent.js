/*
 * A toggle switch component
 * Phil Whitehurst
 */

var ToggleComponent = flight.component(function () {


    this.init = function () {


        btn = jQuery(this.node);
        btn.html(this.attr.on);
        this.trigger(document, this.attr.onEvent);
        self = this;
        btn.click(jQuery.proxy(this.toggle, self));

    }
    this.toggle = function (e) {

        e.preventDefault();
        btn = jQuery(this.node);
        btn.html((btn.html() === this.attr.on ? this.attr.off : this.attr.on));
        this.trigger(document, (btn.html() === this.attr.on ? this.attr.onEvent : this.attr.offEvent));

    }


    this.after('initialize', function () {

        this.init();
    });

});


