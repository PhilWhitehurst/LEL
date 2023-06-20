/*
 * Draw vertical bar chart, using d3
 * Phil Whitehurst
 */


var jsChartComponent = flight.component(function () {
    var self;

    this.initChart = function () {
        self = this;

        this.chart = "hello";
        if (this.attr.auto !== 'no') {

            this.trigger(this.node, 'initDone');
        }

    };
    this.getData = function (evt, data) {

        if (!data) {


            jQuery.ajax({
                dataType: "json",
                headers: {
                    'X-WP-Nonce': self.attr.wp_rest
                },
                url: self.attr.resturl,
                context: self,
                success: function (data) {
                    this.trigger(this.node, 'newData', data);
                }

            });

        }
        else {
            if (data.chart) {
                this.trigger(this.node, 'newData', data.chart);
            } else {
                if (data.status !== 'error') {
                    jQuery.ajax({
                        dataType: "json",
                        headers: {
                            'X-WP-Nonce': self.attr.wp_rest
                        },
                        url: self.attr.resturl,
                        context: self,
                        success: function (data) {
                            this.trigger(this.node, 'newData', data);
                        }

                    });
                }
            }


        }

    };



    this.drawChart = function (evt, data) {

        if (this.chart !== "hello") {
            this.chart.destroy();
        }
        var ctx = this.node;
        this.chart = new Chart(ctx, data);
    };

    this.after('initialize', function () {


        this.on(this.node, 'initDone', this.getData);
        this.on(this.node, 'newData', this.drawChart);
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.getData);

        }

        this.initChart();
    });
});