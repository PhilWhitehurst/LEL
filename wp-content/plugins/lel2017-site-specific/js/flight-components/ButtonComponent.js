/*
 * Adds a button
 * Phil Whitehurst
 */

var ButtonComponent = flight.component(function () {


    this.init = function () {
        button = '<button class="btn '
                + this.attr.classes
                + '">'
                + this.attr.text
                + '</button>';

        if (this.attr.append) {
            jQuery(this.node).append(button);
        }
        else {
            jQuery(this.node).prepend(button);
        }



    };



    this.after('initialize', function () {

        this.init();
    });

});





