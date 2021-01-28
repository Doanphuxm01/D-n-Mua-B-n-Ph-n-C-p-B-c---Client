@extends('baotri')
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/js/plugins/ui/fullcalendar.min.css') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/fullcalendar.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/fullcalendar/lang/vi.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3_tooltip.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}
    <style type="text/css">
        .panel-white > .panel-heading {
            padding: 9px 15px;
        }

        .panel-white > .panel-heading h3 {
            font-size: 18px;
        }

    </style>
@stop

@section('CONTENT_REGION')
<div class="page-container login-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content">

                <!-- Error wrapper -->
                <div class="container-fluid text-center">
                    <h1 class="error-title offline-title">Xin lỗi</h1>
                    <h6 class="text-semibold content-group">Website đang bảo trì</h6>
                    <h6 class="text-semibold content-group">Mời bạn quay lại vào ngày mai</h6>

                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3">
                            <a href="javascript: history.go(-1)" class="btn btn-primary btn-block content-group"><i class="icon-circle-left2 position-left"></i> Go to dashboard</a>
                        </div>
                    </div>
                </div>
                <!-- /error wrapper -->


                <!-- Footer -->
                <div class="footer text-muted">
                    &copy; 2020. <a href="https://minhphucgroup.com.vn/">minhphucgroup.com.vn</a>
                </div>
                <!-- /footer -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
@stop

@section('JS_BOTTOM_REGION')
    <script type="text/javascript">
        $(['#vi-chietkhau', '#vi-tichluy','#vi-congno', '#vi-hoahong', '#vi-tieudung', '#vi-khodiem']).each(function (i, e) {
            dailyRevenue(e, 50); // initialize chart
        })


        // Chart setup
        function dailyRevenue(element, height) {


            // Basic setup
            // ------------------------------

            // Add data set
            var dataset = [
                {
                    "date": "04/13/14",
                    "alpha": "60"
                }, {
                    "date": "04/14/14",
                    "alpha": "35"
                }, {
                    "date": "04/15/14",
                    "alpha": "65"
                }, {
                    "date": "04/16/14",
                    "alpha": "50"
                }, {
                    "date": "04/17/14",
                    "alpha": "65"
                }, {
                    "date": "04/18/14",
                    "alpha": "20"
                }, {
                    "date": "04/19/14",
                    "alpha": "60"
                }
            ];

            // Main variables
            var d3Container = d3.select(element),
                margin = {top: 0, right: 0, bottom: 0, left: 0},
                width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right,
                height = height - margin.top - margin.bottom,
                padding = 20;

            // Format date
            var parseDate = d3.time.format("%m/%d/%y").parse,
                formatDate = d3.time.format("%a, %B %e");



            // Add tooltip
            // ------------------------------

            var tooltip = d3.tip()
                .attr('class', 'd3-tip')
                .html(function (d) {
                    return "<ul class='list-unstyled mb-5'>" +
                        "<li>" + "<div class='text-size-base mt-5 mb-5'><i class='icon-check2 position-left'></i>" + formatDate(d.date) + "</div>" + "</li>" +
                        "<li>" + "Sales: &nbsp;" + "<span class='text-semibold pull-right'>" + d.alpha + "</span>" + "</li>" +
                        "<li>" + "Revenue: &nbsp; " + "<span class='text-semibold pull-right'>" + "$" + (d.alpha * 25).toFixed(2) + "</span>" + "</li>" +
                        "</ul>";
                });



            // Create chart
            // ------------------------------

            // Add svg element
            var container = d3Container.append('svg');

            // Add SVG group
            var svg = container
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
                .call(tooltip);



            // Load data
            // ------------------------------

            dataset.forEach(function (d) {
                d.date = parseDate(d.date);
                d.alpha = +d.alpha;
            });



            // Construct scales
            // ------------------------------

            // Horizontal
            var x = d3.time.scale()
                .range([padding, width - padding]);

            // Vertical
            var y = d3.scale.linear()
                .range([height, 5]);



            // Set input domains
            // ------------------------------

            // Horizontal
            x.domain(d3.extent(dataset, function (d) {
                return d.date;
            }));

            // Vertical
            y.domain([0, d3.max(dataset, function (d) {
                return Math.max(d.alpha);
            })]);



            // Construct chart layout
            // ------------------------------

            // Line
            var line = d3.svg.line()
                .x(function(d) {
                    return x(d.date);
                })
                .y(function(d) {
                    return y(d.alpha)
                });



            //
            // Append chart elements
            //

            // Add mask for animation
            // ------------------------------

            // Add clip path
            var clip = svg.append("defs")
                .append("clipPath")
                .attr("id", "clip-line-small");

            // Add clip shape
            var clipRect = clip.append("rect")
                .attr('class', 'clip')
                .attr("width", 0)
                .attr("height", height);

            // Animate mask
            clipRect
                .transition()
                .duration(1000)
                .ease('linear')
                .attr("width", width);



            // Line
            // ------------------------------

            // Path
            var path = svg.append('path')
                .attr({
                    'd': line(dataset),
                    "clip-path": "url(#clip-line-small)",
                    'class': 'd3-line d3-line-medium'
                })
                .style('stroke', '#fff');

            // Animate path
            svg.select('.line-tickets')
                .transition()
                .duration(1000)
                .ease('linear');



            // Add vertical guide lines
            // ------------------------------

            // Bind data
            var guide = svg.append('g')
                .selectAll('.d3-line-guides-group')
                .data(dataset);

            // Append lines
            guide
                .enter()
                .append('line')
                .attr('class', 'd3-line-guides')
                .attr('x1', function (d, i) {
                    return x(d.date);
                })
                .attr('y1', function (d, i) {
                    return height;
                })
                .attr('x2', function (d, i) {
                    return x(d.date);
                })
                .attr('y2', function (d, i) {
                    return height;
                })
                .style('stroke', 'rgba(255,255,255,0.3)')
                .style('stroke-dasharray', '4,2')
                .style('shape-rendering', 'crispEdges');

            // Animate guide lines
            guide
                .transition()
                .duration(1000)
                .delay(function(d, i) { return i * 150; })
                .attr('y2', function (d, i) {
                    return y(d.alpha);
                });



            // Alpha app points
            // ------------------------------

            // Add points
            var points = svg.insert('g')
                .selectAll('.d3-line-circle')
                .data(dataset)
                .enter()
                .append('circle')
                .attr('class', 'd3-line-circle d3-line-circle-medium')
                .attr("cx", line.x())
                .attr("cy", line.y())
                .attr("r", 3)
                .style('stroke', '#fff')
                .style('fill', '#29B6F6');



            // Animate points on page load
            points
                .style('opacity', 0)
                .transition()
                .duration(250)
                .ease('linear')
                .delay(1000)
                .style('opacity', 1);


            // Add user interaction
            points
                .on("mouseover", function (d) {
                    tooltip.offset([-10, 0]).show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })

                // Hide tooltip
                .on("mouseout", function (d) {
                    tooltip.hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                });

            // Change tooltip direction of first point
            d3.select(points[0][0])
                .on("mouseover", function (d) {
                    tooltip.offset([0, 10]).direction('e').show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })
                .on("mouseout", function (d) {
                    tooltip.direction('n').hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                });

            // Change tooltip direction of last point
            d3.select(points[0][points.size() - 1])
                .on("mouseover", function (d) {
                    tooltip.offset([0, -10]).direction('w').show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })
                .on("mouseout", function (d) {
                    tooltip.direction('n').hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                })



            // Resize chart
            // ------------------------------

            // Call function on window resize
            $(window).on('resize', revenueResize);

            // Call function on sidebar width change
            $('.sidebar-control').on('click', revenueResize);

            // Resize function
            //
            // Since D3 doesn't support SVG resize by default,
            // we need to manually specify parts of the graph that need to
            // be updated on window resize
            function revenueResize() {

                // Layout variables
                width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right;


                // Layout
                // -------------------------

                // Main svg width
                container.attr("width", width + margin.left + margin.right);

                // Width of appended group
                svg.attr("width", width + margin.left + margin.right);

                // Horizontal range
                x.range([padding, width - padding]);


                // Chart elements
                // -------------------------

                // Mask
                clipRect.attr("width", width);

                // Line path
                svg.selectAll('.d3-line').attr("d", line(dataset));

                // Circles
                svg.selectAll('.d3-line-circle').attr("cx", line.x());

                // Guide lines
                svg.selectAll('.d3-line-guides')
                    .attr('x1', function (d, i) {
                        return x(d.date);
                    })
                    .attr('x2', function (d, i) {
                        return x(d.date);
                    });
            }
        }


    </script>
@stop