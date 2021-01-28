@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/exporttable/FileSaver.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/exporttable/Blob.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/exporttable/xls.core.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/exporttable/tableexport.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setPreLoadCssLink('backend-ui/assets/js/exporttable/tableexport.css') !!}
@stop
@section('BREADCRUMB_REGION')

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('logs')}}">Quản lý log, lịch sử thao tác</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <style>

        #table-excel td, th {
            width: 100px;
            overflow: hidden;
            white-space: nowrap;
        }
    </style>


    <div class="" style="padding: 0px 12px">

        <div class="panel panel-white">
            <div class="panel-heading">
                <h4 class="panel-title">Xem trước danh sách log khi xuất excel <small class="text-danger">Lưu ý: mỗi lần xuất tối đa 5000 bản ghi</small> </h4>

            </div>

            <div class="panel-body" style="max-height: 800px;overflow-y: scroll">
                <table id="table-excel" class="table table-bordered overflow-auto">
                    <thead>
                    <th>STT</th>
                    <th>Account</th>
                    <th>Phòng ban liên quan nhân viên</th>
                    <th>Phòng ban liên quan dữ liệu</th>
                    <th>Dự án</th>
                    <th>Đối tượng liên quan</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                    <th>Ghi chú</th>
                    </thead>
                    <tbody>
                    @if(isset($listObj))
                        @foreach($listObj as $key=>$obj)

                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @isset($obj['created_by']['name']){{$obj['created_by']['name']}} @endisset
                                </td>
                                <td>
                                    @isset($obj['created_by']['department_of_staff']){{$obj['created_by']['department_of_staff']}} @endisset
                                </td>
                                <td>
                                    @isset($obj['department_name']){{$obj['department_name']}} @endisset
                                </td>
                                <td>
                                    @isset($obj['project_name']){{$obj['project_name']}}
                                    @else
                                        @if(isset($obj['data_object']['project']['name']))
                                            {{$obj['data_object']['project']['name']}}
                                            @elseif($obj['collection_name']===\App\Http\Models\Logs::OBJECT_PROFILE)
                                                {{--vá tạm việc không lưu tên dự án vào log =>móc ngược lại bảng dự án--}}

                                                <?php

                                                    if(isset($obj['data_object']['project']) && $obj['data_object']['project'] && is_array($obj['data_object']['project']))    {
                                                        $projectName = [];
                                                        foreach (@$obj['data_object']['project'] as $p=>$v){
                                                            $project = \App\Http\Models\Project::select('name')->where('_id',$v)->first();
                                                            if($project) {
                                                                $projectName[] = $project['name'];
                                                            }
                                                        }
                                                        echo implode(' | ',$projectName);
                                                    }
                                                ?>
                                        @endif
                                    @endisset
                                </td>
                                <td>
                                    @if(isset($lsObject[$obj->collection_name]))
                                        {{$lsObject[$obj->collection_name]}}
                                    @endif
                                </td>
                                <td>
                                    {{\App\Elibs\Helper::showMongoDate($obj->created_at,'H:i d/m/Y')}}
                                </td>
                                <td>
                                    {{$obj['type']}}
                                </td>
                                <td>
                                    {{$obj['note']}}
                                </td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                </table>

            </div>


        </div>

    </div>
@stop

@push('JS_BOTTOM_REGION')
    <script type="text/javascript">
        $("#table-excel").tableExport({
            position: 'top',
            formats: ["xlsx", "xls", "csv", "txt"],
            trimWhitespace: true,
            fileName: "log_access_output_"
        });

    </script>
@endpush
