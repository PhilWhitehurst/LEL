var AjaxSearchComponent = flight.component(function () {
    /*
     * Component to submit a rider search via ajax
     */

    /*
     * Default attributes
     * Should only need to override if multiple search requirements on
     * same page
     */
    this.attributes({
        resultsPerPage: 30,
        trigger_event: 'SearchResults',
        listen_event: 'Search',
        lastSearch_event: 'LastSearch',
        resultsPerPage_event: 'ResultsPerPage'

    });
    /*
     * Submit the search via ajax
     */

    this.ajaxSubmit = function (evt, params) {
        evt.preventDefault();

        var $target = jQuery(this.node);
        var action = $target.attr("action");
        /*
         * If not triggered by form submit and not lastsearch then return
         */
        if (evt.type === this.attr.listen_event) {

            if (this.attr.lastSearch !== action)
            {
                return;
            }
        }

        // Grab form data
        var data = $target.serialize();
        var resultsOffset = (evt.type === this.attr.listen_event ? params.resultsOffset : 0);
        data += '&resultsPerPage=' + this.attr.resultsPerPage + '&resultsOffset=' + resultsOffset;
        // Get the action

        jQuery.ajax({
            type: "POST",
            dataType: 'json',
            url: wp.ajaxurl + '?action=' + action,
            data: data,
            context: this,
            success: function (response) {
                this.trigger(document, this.attr.trigger_event, response);
            }

        });
        var response = {
            lastSearch: action
        };
        this.trigger(document, this.attr.lastSearch_event, response);
    };
    /*
     * Keep track of last search performed so we trigger the right search
     * in response to a riderSearch event.
     *
     */
    this.lastSearch = function (evt, data) {
        this.attr.lastSearch = data.lastSearch;
    };
    /*
     * Keep track of the page offset so results paging handled correctly
     */

    this.updateOffset = function (evt, data) {
        this.attr.resultsOffset = data.resultsOffset;
    };
    this.updateResultsPerPage = function (evt, data) {

        this.attr.resultsPerPage = data.resultsPerPage;
    };
    this.after('initialize', function () {
        this.on('submit', this.ajaxSubmit);
        this.on(document, this.attr.listen_event, this.ajaxSubmit);
        this.on(document, this.attr.lastSearch_event, this.lastSearch);
        this.on(document, this.attr.resultsOffset_event, this.updateOffset);
        this.on(document, this.attr.resultsPerPage_event, this.updateResultsPerPage);
    });
});