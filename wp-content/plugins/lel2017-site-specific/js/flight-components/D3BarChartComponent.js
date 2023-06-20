/*
 * Draw vertical bar chart, using d3
 * Phil Whitehurst
 */


var D3BarChartComponent = flight.component(function () {
    var self;
    var chart = "hello";
    this.initChart = function () {
        self = this;
        this.trigger('initDone');
    };
    this.getData = function (evt) {


        d3
                .json(self.attr.resturl)
                .header('X-WP-Nonce', self.attr.wp_rest)
                .get(function (error, json) {

                    if (error)
                        throw error;
                    data = {
                        columns: json[0].columns,
                        groups: json[0].groups,
                        legend: json[0].legend
                    };
                    self.trigger('newData', data);
                });

    };
    this.drawChartC3 = function (evt, data) {

        if (chart === "hello") {
            chart = c3.generate({
                bindto: this.node,
                color: {
                    pattern: [
                        '#e42127',
                        '#333e48'

                    ]
                },
                data: {
                    x: 'x',
                    columns: data.columns,
                    groups: data.groups,
                    type: 'bar',
                    order: 'asc'
                },
                legend: data.legend,
                axis: {
                    x: {
                        type: 'category',
                        height: 60
                    }

                }

            });
        }
        else {
            chart.load({
                columns: data.columns,
                groups: data.groups,
                type: 'bar'
            });
        }

    };
    this.after('initialize', function () {


        this.on('initDone', this.getData);
        this.on('newData', this.drawChartC3);
        var l = this.attr.listen_events.length;
        for (var i = 0; i < l; i++) {
            this.on(document, this.attr.listen_events[i], this.getData);
        }

        this.initChart();
    });
});