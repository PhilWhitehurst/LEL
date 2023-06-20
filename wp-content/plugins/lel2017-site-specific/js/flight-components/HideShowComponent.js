

var HideShowComponent = flight.component(function () {
    /*
     * Hide or show a dom element in reaction to events passed as attributes
     */
    this.hide = function () {
        jQuery(this.node)
                .addClass('hidden');
    };
    this.show = function () {

        jQuery(this.node)
                .removeClass('hidden');
    };
    this.after('initialize', function () {

        var l = this.attr.hide.length;
        for (var i = 0; i < l; i++) {

            this.on(document, this.attr.hide[i].event, this.hide);
        }
        l = this.attr.show.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.show[i].event, this.show);
        }


    });
});

