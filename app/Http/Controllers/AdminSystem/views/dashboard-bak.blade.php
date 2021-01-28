@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3_tooltip.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {{--{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/dashboard.js') !!}--}}
    <style type="text/css">
        .panel-white > .panel-heading {
            padding: 5px 15px;
        }

        .panel-white > .panel-heading h3 {
            font-size: 18px;
        }
    </style>
@stop


@section('CONTENT_REGION')
    {{-- @if(isset($_MSG))
         {!! $_MSG !!}
     @endif
     --}}
    <div class="row">
        <div class="col-md-6">
            <div class="row">

                <div class="col-md-6">

                    <div class="panel panel-white">
                        <div class="panel-heading" style="border-bottom: 0">
                            <h3 class="panel-title">
                                <a class="" data-toggle="collapse" href="#gSumStatus" aria-expanded="true">
                                    <i class="icon-newspaper"></i>
                                    Thống kê trạng thái văn bản
                                </a>
                            </h3>
                        </div>
                        <div id="gSumStatus" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="panel-body no-padding">
                                <div class="table-responsive">
                                    <table class="table table-lg text-nowrap">
                                        <tbody>
                                        @if(isset($filterSum['status']) && $filterSum['status'])
                                            @foreach($filterSum['status'] as $item)
                                                {{--<tr>
                                                    <td class="">
                                                        <div class="media-left">
                                                            <h5 class="text-semibold no-margin">{{\App\Elibs\Helper::numberFormat($item['count'])}}
                                                                <small class="text-size-base">
                                                                    {{round($item['count']*100/$lsFilterCount['status'],2)}}%
                                                                </small>
                                                            </h5>
                                                            <ul class="list-inline list-inline-condensed no-margin">
                                                                <li>
                                                                    <span class="status-mark border-{{@\App\Http\Models\Document::getStatus($item['value'])['style']}}"></span>
                                                                </li>
                                                                <li>
                                                                    <span class="text-muted">{{@\App\Http\Models\Document::getStatus($item['value'])['text']}}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>--}}
                                                <tr class="rule">
                                                    <td style="padding: 7px">
                                                        <a href=""> <span style="font-weight: normal" class="label label-block label-flat border-{{ \App\Http\Models\Document::getStatus($item['value'])['style'] }} label-rounded text-{{ \App\Http\Models\Document::getStatus($item['value'])['style'] }}">{{@\App\Http\Models\Document::getStatus($item['value'])['text']}}</span></a>
                                                    </td>
                                                    <td>
                                                        {{\App\Elibs\Helper::numberFormat($item['count'])}}
                                                    </td>
                                                    <td>
                                                        <small class="text-size-base text-muted">
                                                            {{round($item['count']*100/$lsFilterCount['status'],2)}}%
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-white">
                        <div class="panel-heading" style="border-bottom: 0">
                            <h3 class="panel-title">
                                <a class="" data-toggle="collapse" href="#gSum" aria-expanded="true">
                                    <i class="icon-newspaper"></i>
                                    Thống kê loại văn bản
                                </a>
                            </h3>
                        </div>
                        <div id="gSum" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="panel-body no-padding">
                                <div class="table-responsive">
                                    <table class="table table-lg text-nowrap">
                                        <tbody>

                                        @if(isset($filterSum['type']) && $filterSum['type'])
                                            @foreach($filterSum['type'] as $item)
                                                <tr>
                                                    <td class="">
                                                        <div class="media-left">
                                                            <div id="{{$item['value']}}-sum" data-value="{{round($item['count']*100/$lsFilterCount['type'],0)}}"></div>
                                                        </div>

                                                        <div class="media-left">
                                                            <h5 class="no-margin">{{\App\Elibs\Helper::numberFormat($item['count'])}}
                                                                <small class="text-size-base text-muted">
                                                                    {{round($item['count']*100/$lsFilterCount['type'],2)}}%
                                                                </small>
                                                            </h5>
                                                            <ul class="list-inline list-inline-condensed no-margin">
                                                                <li>
                                                                    <span class="status-mark border-{{isset(\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['style'])?\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['style']:'success'}}"></span>
                                                                </li>
                                                                <li>
                                                                    <a href="{{isset(\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['label'])?admin_link(\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['alias_link']):'/'}}"> <span class="">{{isset(\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['label'])?\App\Http\Models\Document::DOCUMENT_TYPE[$item['value']]['label']:'Không xác định'}}</span></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--region lịch làm việc--}}
            <div class="panel panel-white">
                <div class="panel-heading" style="border-bottom: 0">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gCalendar" aria-expanded="true">
                            <i class="icon-calendar text-danger"></i>
                            Lịch làm việc
                        </a>
                    </h3>
                </div>
                <div id="gCalendar" class="panel-collapse collapse in" aria-expanded="true">
                    <div class="panel-body no-padding">
                        @if(isset($lsCalendar) && $lsCalendar)
                            <table class="table table-striped table-io">
                                <tbody>
                                @foreach($lsCalendar as $key=>$val)
                                    <tr id="itemRow_{{$val->id}}">
                                        <td>
                                            <a class="text-bold text-default" onclick="_SHOW_FORM_REMOTE('{{admin_link('/calendar/show-detail?id='.$val->_id)}}')">{{$val->title}}</a>
                                            <div>
                                                {{$val->brief}}
                                            </div>
                                        </td>
                                        <td>
                                            Bắt đầu: <i>{{\App\Elibs\Helper::showDate($val['started_at'],'d/m/Y H:i')}}</i>
                                            <div>Kết thúc: <i>{{\App\Elibs\Helper::showDate($val['ended_at'],'d/m/Y H:i')}}</i></div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
            {{--endregion lịch làm việc--}}

            <div class="panel panel-white">
                <div class="panel-heading" style="border-bottom: 0">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gMyProject" aria-expanded="true">
                            <i class="fa fa-university"></i>
                            Dự án của tôi
                        </a>
                    </h3>
                </div>
                <div id="gMyProject" class="panel-collapse collapse in" aria-expanded="true">
                    <div class="panel-body no-padding">
                        @if(!isset($allProjectByMe) || !$allProjectByMe)
                            <h3 class="text-center mt-0">Không tìm thấy dự án nào!</h3>
                            <div class="text-center">
                                <small>Có thể bạn chưa được phân quyền vào bất kỳ dự án nào. Hãy liên hệ với quản trị viên để được hỗ trợ!</small>
                            </div>
                        @else
                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                @foreach($allProjectByMe as $key=>$value)

                                    <tr class="rule">
                                        <td>
                                            <div class="title"><a onclick="return MNG_POST.update('{{admin_link('project/show_switch?popup=static&id='.$value['_id'].'&token='.\App\Elibs\Helper::buildTokenString($value['_id']).'')}}')" title="Quản lý dự án" href="javascript:void(0)">
                                                    {{$value['name']}}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                        @endif
                    </div>
                </div>
            </div>


        </div>
        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading" style="border-bottom: 0">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gMyDocument" aria-expanded="true"><i class="icon-book text-primary"></i> Văn bản mới</a>
                    </h3>
                </div>
                <div id="gMyDocument" class="panel-collapse collapse in" aria-expanded="true">

                    <div class="panel-body no-padding">
                        @if(isset($listDocument) && $listDocument)
                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                <tbody>
                                @foreach($listDocument as $key=>$value)
                                    <tr class="rule">
                                        <td>
                                            <a onclick="_SHOW_FORM_REMOTE('{!! admin_link('/document/quick-view?id='.$value['_id']) !!}');return false;" href="{{\App\Http\Models\Document::buildLinkEdit($value)}}" title="Xem thông tin"> {{$value['name']}}</a>
                                            <div>
                                                @if(isset($allProjectByMe[$value['project_id']]))
                                                    <span class="text-grey-600">{{$allProjectByMe[$value['project_id']]['name']}}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-right" width="160">
                                            <span title="Ngày tạo"> {{\App\Elibs\Helper::showMongoDate($value['created_at'],'H:i d/m/Y')}}</span>
                                            <div style="margin-bottom: 5px;clear: both;float:none;display: block"
                                                 class="label label-flat border-{{ \App\Http\Models\Document::getStatus($value['status'])['style'] }} label-rounded text-{{ \App\Http\Models\Document::getStatus($value['status'])['style'] }}">{{ \App\Http\Models\Document::getStatus($value['status'])['text'] }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center"><i class="fa fa-info"></i> Không tìm thấy tài liệu nào!</div>
                        @endif
                    </div>

                </div>
            </div>

            {{--region tin tức mới--}}
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gNews" aria-expanded="true"><i class="icon-newspaper text-success"></i> Tin tức nội bộ</a>
                    </h3>
                </div>
                <div id="gNews" class="panel-collapse collapse in" aria-expanded="true">

                    <div class="panel-body no-padding">
                        @if(isset($lsNews) && $lsNews)

                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                <tbody>
                                @foreach($lsNews as $key=>$value)
                                    <tr class="rule">
                                        <td>
                                            <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/news/show-detail?id='.$value['_id'])}}')" title="Xem thông tin"> {{$value['name']}}</a>

                                            @if(isset($value['departments']) && is_array($value['departments']) && $value['departments'])
                                                @php
                                                    $ls = array_column($value['departments'],'name');
                                                @endphp
                                                <div class="text-muted">
                                                    {{implode(', ',$ls)}}
                                                </div>
                                            @endif

                                        </td>
                                        <td class="text-right" width="160">
                                            <span title="Ngày tạo"> {{\App\Elibs\Helper::showMongoDate($value['created_at'],'H:i d/m/Y')}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center"><i class="fa fa-info"></i> Chưa có tin tức mới</div>
                        @endif
                    </div>

                </div>
            </div>
            <script type="text/javascript">
                jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
                    if (jqxhr.status == 200) {
                        $(document).unbind('click.fb-start');
                        $('[data-popup="lightbox"]').fancybox({
                            padding: 3
                        });
                    }
                });
            </script>
            {{--endregion tin tức mới--}}


        </div>
    </div>
@stop
@section('JS_BOTTOM_REGION')
    <script type="text/javascript">
        campaignDonut("#van-ban-den-sum", 42, [
            {
                "value": jQuery('#van-ban-den-sum').attr('data-value'),
                "color": "#66BB6A"
            },
            {
                "value": jQuery('#van-ban-di-sum').attr('data-value'),
                "color": "#bbb"
            },
            {
                "value": jQuery('#van-ban-noi-bo-sum').attr('data-value'),
                "color": "#bbb"
            },
        ]);
        campaignDonut("#van-ban-di-sum", 42, [
            {
                "value": jQuery('#van-ban-den-sum').attr('data-value'),
                "color": "#bbb"
            },
            {
                "value": jQuery('#van-ban-di-sum').attr('data-value'),
                "color": "#f44336"
            },
            {
                "value": jQuery('#van-ban-noi-bo-sum').attr('data-value'),
                "color": "#bbb"
            },
        ]);
        campaignDonut("#van-ban-noi-bo-sum", 42, [
            {
                "value": jQuery('#van-ban-den-sum').attr('data-value'),
                "color": "#bbb"
            },
            {
                "value": jQuery('#van-ban-di-sum').attr('data-value'),
                "color": "#bbb"
            },
            {
                "value": jQuery('#van-ban-noi-bo-sum').attr('data-value'),
                "color": "#00bcd4"
            },
        ]);


        // Chart setup
        function campaignDonut(element, size, data) {


            // Basic setup
            // ------------------------------

            // Add data set
            var data = data;

            // Main variables
            var d3Container = d3.select(element),
                distance = 2, // reserve 2px space for mouseover arc moving
                radius = (size / 2) - distance,
                sum = d3.sum(data, function (d) {
                    return d.value;
                })


            // Create chart
            // ------------------------------

            // Add svg element
            var container = d3Container.append("svg");

            // Add SVG group
            var svg = container
                .attr("width", size)
                .attr("height", size)
                .append("g")
                .attr("transform", "translate(" + (size / 2) + "," + (size / 2) + ")");


            // Construct chart layout
            // ------------------------------

            // Pie
            var pie = d3.layout.pie()
                .sort(null)
                .startAngle(Math.PI)
                .endAngle(3 * Math.PI)
                .value(function (d) {
                    return d.value;
                });

            // Arc
            var arc = d3.svg.arc()
                .outerRadius(radius)
                .innerRadius(radius / 2);


            //
            // Append chart elements
            //

            // Group chart elements
            var arcGroup = svg.selectAll(".d3-arc")
                .data(pie(data))
                .enter()
                .append("g")
                .attr("class", "d3-arc")
                .style('stroke', '#fff')
                .style('cursor', 'pointer');

            // Append path
            var arcPath = arcGroup
                .append("path")
                .style("fill", function (d) {
                    return d.data.color;
                });


            // Animate chart on load
            arcPath
                .transition()
                .delay(function (d, i) {
                    return i * 500;
                })
                .duration(500)
                .attrTween("d", function (d) {
                    var interpolate = d3.interpolate(d.startAngle, d.endAngle);
                    return function (t) {
                        d.endAngle = interpolate(t);
                        return arc(d);
                    };
                });
        }
    </script>
@stop