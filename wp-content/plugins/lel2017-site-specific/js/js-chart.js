/*
 * Attach the bar chart tot he specified divid with specified action
 * and listeners
 */
jsChartComponent.attachTo(wp_chart.divid, {
    resturl: wp_chart.resturl,
    wp_rest: wp_chart.wp_rest,
    listen_events: wp_chart.listen_events,
    auto: wp_chart.auto
});

