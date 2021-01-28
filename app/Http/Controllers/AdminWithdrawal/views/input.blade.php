@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3_tooltip.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}


@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Yêu cầu rút tiền </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('rut-tien')}}">Yêu cầu rút tiền</a></li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <form name="postInputFormViHoaHong" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputFormViHoaHong');" id="postInputFormViHoaHong" class="w-100 col-md-4 d-flex form-horizontal " method="post">
            <input type="hidden" name="type_vi" id="id" value="vihoahong"/>
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Ví hoa hồng</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col-md-6">
                                <!-- Today's revenue -->
                                <div class="panel bg-success-400">
                                    <div class="panel-body">


                                        <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vihoahong['total_money']) }}</h3>
                                        Ví hoa hồng
                                        <div class="text-muted text-size-small">
                                            @if(isset($vihoahong['so_diem_treo_gio']) && $vihoahong['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$vihoahong['so_diem_treo_gio']) }}
                                            @else
                                                ---
                                            @endif
                                        </div>
                                    </div>
                                    <div id="vi-hoahong"></div>
                                </div>
                                <!-- /today's revenue -->
                            </div>
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số tiền bạn muốn rút','key'=>'so_tien_muon_rut', 'class' => 'so_tien_muon_rut input-type-number'],
                                    'note'=>['label'=>'* Số tiền tối thiểu yêu cầu rút là 50.000 MPG ~ 50.000 vnđ','class'=>'text-danger']])
                                <small class="text-danger" style="font-size: 11px"></small>

                                @isset($obj['tk_ngan_hang'])
                                    <div class="form-group">
                                        <label for="">Chọn ngân hàng nhận tiền</label>
                                        <select name="obj[tk_ngan_hang]" class="select-search select-md">
                                            @foreach(@$obj['tk_ngan_hang'] as $key=>$value)
                                                <option value="{{@$value['so']}}-xlxx-{{@$value['id']}}">Số TK: {{\App\Elibs\Helper::showContent(@$value['so'])}} - Tên NH: {{\App\Elibs\Helper::showContent(@$value['name'])}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                @endisset
                            </div>



                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($obj,'customer')}}','{{$obj['_id']}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>

                            @endif
                            <button type="button" id="btnInputFormViHoaHong" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Gửi yêu cầu
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <form name="postInputFormViTichLuy" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputFormViTichLuy');" id="postInputFormViTichLuy" class="w-100 col-md-4 d-flex form-horizontal " method="post">
            <input type="hidden" name="type_vi" value="vitichluy"/>
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Ví tích lũy</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col-md-6">
                                <!-- Today's revenue -->
                                <div class="panel bg-blue-400">
                                    <div class="panel-body">

                                        <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vitichluy['total_money']) }}</h3>
                                        Ví tích luỹ
                                        <div class="text-muted text-size-small">
                                            @if(isset($vitichluy['so_diem_treo_gio']) && $vitichluy['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$vitichluy['so_diem_treo_gio']) }}
                                            @else
                                                ---
                                            @endif
                                        </div>
                                    </div>
                                    <div id="vi-tichluy"></div>
                                </div>
                                <!-- /today's revenue -->
                            </div>
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số tiền bạn muốn rút','key'=>'so_tien_muon_rut', 'class' => 'so_tien_muon_rut input-type-number'],
                                    'note'=>['label'=>'* Số tiền tối thiểu yêu cầu rút là 50.000 MPG ~ 50.000 vnđ','class'=>'text-danger']])
                                @isset($obj['tk_ngan_hang'])
                                    <div class="form-group">
                                        <label for="">Chọn ngân hàng nhận tiền</label>
                                        <select name="obj[tk_ngan_hang]" class="select-search select-md">
                                            @foreach(@$obj['tk_ngan_hang'] as $key=>$value)
                                                <option value="{{@$value['so']}}-xlxx-{{@$value['id']}}">Số TK: {{\App\Elibs\Helper::showContent(@$value['so'])}} - Tên NH: {{\App\Elibs\Helper::showContent(@$value['name'])}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                @endisset
                            </div>



                        </div>
                        <div class="panel-footer p-3">
                            <button type="button" id="btnInputFormViTichLuy" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Gửi yêu cầu
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script type="text/javascript">
        MNG_POST.URL_ACTION = '/admin/rut-tien/';
        $(function () {
            $('#btnInputFormViHoaHong').click(function () {
                return MNG_POST.save('#postInputFormViHoaHong');
            })
            $('#btnInputFormViTichLuy').click(function () {
                return MNG_POST.save('#postInputFormViTichLuy');
            })
        })
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });
    </script>
@stop
@section('JS_BOTTOM_REGION')

    <script type="text/javascript">
        $(['#vi-tichluy','#vi-hoahong']).each(function (i, e) {
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