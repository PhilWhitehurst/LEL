/*
 * Phil Whitehurst
 *
 * Bubble Chart
 */

function lel_bubble(selector, action) {

    var div = d3.select("body")
            .append("div")  // declare the tooltip div
            .attr("class", "tooltip")              // apply the 'tooltip' class
            .style("opacity", 0);                  // set the opacity to nil

    var diameter = $(selector).width(),
            format = d3.format(",d"),
            color = d3.scale.category20c();

    var bubble = d3.layout.pack()
            .sort(null)
            .size([diameter, diameter * 2 / 3])
            .padding(0);


    var svg = d3.select(selector).append("svg")
            .attr("width", diameter)
            .attr("height", diameter * 2 / 3)
            .attr("class", "bubble")


    d3.json(wp.ajaxurl + action, function (error, root) {
        if (error)
            throw error;

        var node = svg.selectAll(".node")
                .data(bubble.nodes({children: root})
                        .filter(function (d) {
                            return !d.children;
                        }))
                .enter().append("g")
                .attr("class", "node")
                .attr("transform", function (d) {
                    return "translate(" + d.x + "," + d.y + ")";
                });


        node.append("circle")
                .attr("r", function (d) {
                    return d.r;
                })
                .attr("class", "circle");


        node.append("text")
                .attr("dy", ".3em")
                .style("text-anchor", "middle")
                .text(function (d) {

                    return d.title.substring(0, d.r / 3);
                });
        node.on("click", function (d) {
            showTooltip(d, div);

        });
        node.on("mouseover", function (d) {
            showTooltip(d, div);

        });
        svg.on("mouseleave", function (d) {
            hideTooltip(d, div);

        });

    });


    d3.select(self.frameElement).style("height", diameter + "px");

    function showTooltip(d, div) {
        div.transition()
                .duration(500)
                .style("opacity", 0);
        div.transition()
                .duration(200)
                .style("opacity", .9);

        div.html(d.full_title + ": " + format(d.value)
                )
                .style("left", (d3.event.pageX) + "px")
                .style("top", (d3.event.pageY - 28) + "px");


    }
    function hideTooltip(d, div) {
        div.transition()
                .duration(500)
                .style("opacity", 0);


    }

}
