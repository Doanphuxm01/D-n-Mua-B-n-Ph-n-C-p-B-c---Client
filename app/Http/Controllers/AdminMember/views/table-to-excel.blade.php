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
            <li class=""><a href="{{admin_link('staff')}}">Danh sách nhân viên</a></li>
            <li>Tải file excel</li>
        </ul>
        <ul class="breadcrumb-elements">

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <i class="icon-download10 position-left"></i>
                    Xuất file
                    <span class="caret"></span>
                </a>
                <div class="dropdown-backdrop"></div>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{admin_link('staff/export_excel?output=basic')}}">Thông tin cơ bản</a></li>
                    <li><a href="{{admin_link('staff/export_excel?output=work')}}">Thông tin công việc</a></li>
                    <li><a href="{{admin_link('staff/export_excel?output=family')}}">Thông tin gia đình</a></li>
                    <li><a href="{{admin_link('staff/export_excel?output=edu')}}">Thông tin Đào tạo</a></li>

                </ul>
            </li>

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
                <h4 class="panel-title">Xem trước danh sách nhân viên</h4>

            </div>

            <div class="panel-body" style="max-height: 800px;overflow-y: scroll">
                <table id="table-excel" class="table table-bordered overflow-auto">
                    @php
                        $output = app('request')->input('output', '0')
                    @endphp
                    @if($output =='basic')
                        @include("views.table.table-thead-basic")
                        @include("views.table.table-tbody-basic")
                    @elseif($output =='family')
                        @include("views.table.table-thead-family")
                        @include("views.table.table-tbody-family")
                    @elseif($output =='work-contract')
                        @include("views.table.table-thead-work-contract")
                        @include("views.table.table-tbody-work-contract")
                    @elseif($output =='work-process')
                        @include("views.table.table-thead-work-process")
                        @include("views.table.table-tbody-work-process")
                    @elseif($output =='edu-chung-chi')
                        @include("views.table.table-thead-edu-chung-chi")
                        @include("views.table.table-tbody-edu-chung-chi")
                    @elseif($output =='edu-bang-cap')
                        @include("views.table.table-thead-edu-bang-cap")
                        @include("views.table.table-tbody-edu-bang-cap")
                    @endif
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
            fileName: "{{$output}}_output"
        });

    </script>
@endpush
