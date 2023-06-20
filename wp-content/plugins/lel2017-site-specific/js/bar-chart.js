/*
 * Attach the bar chart tot he specified divid with specified action
 * and listeners
 */
D3BarChartComponent.attachTo(wp_bar.divid, {
    resturl: wp_bar.resturl,
    wp_rest: wp_bar.wp_rest,
    names: wp_bar.names,
    listen_events: [
        'UpdatedChosenWave'
    ]
});

