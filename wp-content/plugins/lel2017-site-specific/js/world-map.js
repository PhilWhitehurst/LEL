/*
 * Define the drawing window for map          */
var width = 350, height = 350;

var projection = d3.geo.azimuthalEqualArea()
        .clipAngle(180 - 1e-3)
        .scale(100)
        .translate([width / 2, height / 2])
        .precision(.1);

var svg = d3.select("#world-map").append("svg")
        .attr("width", width)
        .attr("height", height);


var path = d3.geo.path()
        .projection(projection);

var g = svg.append("g");
var g1 = svg1.append("g");
var g2 = svg2.append("g");
var g3 = svg3.append("g");
var g4 = svg4.append("g");

// load and display the World
d3.json("/wp-content/plugins/lel-charts/geoJSON/world-topo-min.json", function (error, topology) {

    g.selectAll("path")
            .data(topojson.feature(topology, topology.objects.countries)
                    .features)
            .enter()
            .append("path")
            .attr("d", path);




    g1.selectAll("path")
            .data(topojson.feature(topology, topology.objects.countries)
                    .features)
            .enter()
            .append("path")
            .attr("d", path);

    g2.selectAll("path")
            .data(topojson.feature(topology, topology.objects.countries)
                    .features)
            .enter()
            .append("path")
            .attr("d", path);

    g3.selectAll("path")
            .data(topojson.feature(topology, topology.objects.countries)
                    .features)
            .enter()
            .append("path")
            .attr("d", path);

    var txt1 = g4

            .append("text")
            .attr("class", "small");
    var text = g4

            .append("text")
            .attr("class", "big");


    //Add the text attributes
    var textLabels = txt1
            .attr("x", "50")
            .attr("y", "70")
            .text("Once around the Sun")
    var textLabels = text
            .attr("x", "50")
            .attr("y", "300")
            .text("75,065")






    // add a red line going round
    coords = [];
    coord = projection([0, 51])
    coords.push(coord)
    coord = projection([+300, 51])
    coords.push(coord)
    /*
     * We now have an array of trackpoints x,y coords in projection of map
     */
    var lineFunction = d3.svg.line()
            .x(function (d) {
                return d[0];
            })
            .y(function (d) {
                return d[1];
            })
            .interpolate("linear");
    /*
     * Now add to the map
     */

    /*var lineGraph = g
     .append("path")
     .attr("d", lineFunction(coords))
     .attr("class", "line");
     */



});

