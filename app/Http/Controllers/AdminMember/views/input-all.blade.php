@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}

@stop

@section('BREADCRUMB_REGION')
    @php
        $tab = app('request')->input('tab', 'info');
    @endphp

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('staff')}}">Danh sách nhân viên</a></li>
            <li class=""><a href="{{admin_link('staff')}}">@if(isset($obj['_id'])) Chỉnh sửa thông tin
                    <b>{{$obj['name']}}</b>@else Thêm nhân
                    viên
                    mới @endif</a></li>
        </ul>

    </div>
@stop
@section('CONTENT_REGION')
    <style>
        .panel {
            border: none !important;
            box-shadow: none;
        }

        .select2-chosen {
            max-width: 150px;
        }
        #save-button{
            position: fixed;
            bottom: 0px;
            right: 17px;
        }
        #delete-account-button{
            position: fixed;
            bottom: 0px;
            right: 90px;
        }

    </style>
    @include("views.components.tab-panels")
    <div style="padding-bottom: 100px">
        @if($tab =='info')
            @include('views.components.input-tab-info')
        @elseif ($tab =='family')
            @include('views.components.input-tab-family')
        @elseif ($tab =='work')
            @include('views.components.input-tab-work')
        @elseif ($tab =='edu')
            @include('views.components.input-tab-edu')
        @elseif ($tab =='account')
            @include('views.components.input-tab-account')
        @elseif ($tab =='files')
            @include('views.components.input-tab-files')
        @else
            @include('views.components.input-tab-info')
        @endif


    </div>

@stop

@push('JS_BOTTOM_REGION')
    <script type="text/javascript">
        var allPosition = {!! $allPosition?json_encode($allPosition):json_encode([]) !!};
        var allDepartment = {!! $allDepartment?json_encode($allDepartment):json_encode([]) !!};

        function _getPosByDep(obj) {
            obj = $(obj);
            let posContainer = obj.parents('.dep-container').find('select.js-pos');
            let dep_id = obj.val();
            posContainer.html('<option value="">Chọn chức vụ</option>');
            for (let i in allPosition) {
                let pos = allPosition[i];
                if (typeof pos.department !== 'undefined') {
                    if (typeof pos.department.id !== 'undefined') {
                        if (pos.department.id == dep_id) {
                            let option = '<option value="' + pos._id + '">' + pos.name + '</select>'
                            posContainer.append(option)
                        }
                    }
                }
            }
        }

        DATE_PICKER_INIT()

    </script>


    <script>
        $('.js-document-link').show()
    </script>
@endpush
