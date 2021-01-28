@extends($THEME_EXTEND)
@section('JS_REGION')

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/switch.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_checkboxes_radios.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/autocomplete.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}

@stop



@section('BREADCRUMB_REGION')

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="javascript:void(0)">Hệ thống</a></li>
            <li class=""><a href="{{admin_link('project')}}">Quản lý dự án, công trình</a></li>
            <li class=""><a href="{{admin_link('project/_list_manage')}}">Phân quyền</a></li>
            @if(isset($obj['name']))
                <li>
                    <b>{{$obj['name']}}</b>
                </li>
            @endif
            @if(isset($obj))
                <li class=""><a href="{{admin_link('project/_list_manage?obj[id]='.$obj['id'].'&action=assign-dep')}}">Phân
                        phòng ban cho dự án</a></li>
                @if(isset($action) &&$action!='assign-dep')
                    <li class=""><a
                                href="{{admin_link('project/_list_manage?obj[id]='.$obj['id'].'&action=assign-member')}}">Phân
                            nhân viên cho dự án</a></li>
                @endif
            @endif
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a
                        href="{{admin_link('project/_list_manage?'.'action=assign-dep')}}"
                >
                    <b><i class="icon-address-book"></i></b> Phân quyền cho phòng ban
                </a>
            </li>
            <li>
                @if(isset($obj['id']) &&  $obj['id'])
                    <a

                            href="{{admin_link('project/_list_manage?obj[id]='.$obj['id'].'&action=assign-member')}}"

                    >
                        <b><i class="icon-user"></i></b> Phần quyền thành viên
                        <b>@if(isset($obj['name'])) {{$obj['name']}} @endif</b>
                    </a>@endif
            </li>


        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="row">
        <form method="get" id="form1">
            @if(isset($obj['id']) &&  $obj['id'] )
                <input type="hidden" name="obj[id]" value="{{$obj['id'] }}">
            @endif
            {{--Lựa chọn dự án và phòng ban--}}
            <div class="col-md-4">
                <div class="panel panel-white" style="min-height: 150px">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Lựa chọn dự án </h4>
                        <div class="heading-elements">
                            <input type="text" oninput="findProject(event)" placeholder="Tìm theo tên"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="panel-body" style="height: calc(100vh - 240px) ; overflow-y: scroll">
                        @foreach($listProjects as $key=> $item)
                            <div class="form-group">

                                <label
                                        @if(isset($obj['id']) &&  $obj['id'] ==$item['_id'])
                                        class="text-bold" id="selected-project"
                                        @else
                                        class="js-label"
                                        @endif
                                >
                                    <input name="obj[id]" id="obj-select-project" onclick="selectProject()"
                                           type="radio"
                                           @if(isset($obj['id']) &&  $obj['id'] ==$item['_id'])
                                           checked
                                           @endif
                                           {{--@if($action!='assign-dep') disabled @endif--}}
                                           value="{{$item['_id']}}"
                                           class="styled">
                                    {{$key +1}}.
                                    {{$item['name']}}
                                    @if(isset($item['departments']) &&!empty($item['departments']))
                                        <b class="text-success-800" data-toggle="popover"
                                           title="Đã được gán phòng ban: {{collect($item['departments'])->pluck('name')->implode(', ')}}">
                                            <i class="icon-checkmark-circle2"></i></b>
                                    @endif
                                </label>

                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
            @if($action!=='assign-member')

                <div class="col-md-4">
                    <div class="panel panel-white" style="min-height: 150px">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Phân dự án cho phòng
                            </h4>

                        </div>
                        <div class="panel-body">
                            @if(isset($obj['id']))
                                @foreach($listDepartments as $item)
                                    <div class="form-group">
                                        <label>
                                            <input name="obj[departments][]" id="obj-select-project"
                                                   type="checkbox"
                                                   @if(isset($obj['departments']) &&  collect($obj['departments'])->pluck('id')->contains($item['_id']) )
                                                   checked
                                                   @endif
                                                   {{--@if($action!='assign-dep') disabled @endif--}}
                                                   value="{{$item['_id']}}"
                                                   class="styled"> {{$item['name']}}
                                        </label>

                                    </div>
                                @endforeach

                            @else
                                <p class="text-grey">Bạn cần chọn dự án trước</p>

                            @endif

                        </div>
                        @if($action=='assign-dep')
                            @if(isset($obj['id']) &&  $obj['id'])
                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-info"
                                            style="position: fixed; right: 15px; bottom: 10px;"
                                            onclick="return MNG_POST.update('{{admin_link('project/assign_project_dep')}}','#form1');"
                                    >Gán dự án cho phòng
                                    </button>
                                </div>
                            @endif
                        @elseif($action=='assign-member')
                        @else
                            @if(isset($obj['id']) &&  $obj['id'])
                                <div class="panel-footer">
                                    <div class="form-group text-right">
                                        <a
                                                @if(isset($obj['id']) &&  $obj['id'])
                                                href="{{admin_link('project/_list_manage?obj[id]='.$obj['id'].'&action=assign-member')}}"
                                                @endif
                                        >
                                            <button type="button" class="btn btn-info">Phần quyền cho nhân viên
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            @endif

                        @endif

                    </div>

                </div>
            @endif

            @if($action=='assign-member')
                <div class="col-md-4 ">
                    <div class="panel panel-white"
                         style="min-height: 150px"
                    >
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Danh sách nhân viên
                                <div class="heading-elements">
                                    <input type="text" oninput="findMember(event)" placeholder="Tìm theo tên"
                                           class="form-control">
                                </div>
                            </h4>
                        </div>

                        <div class="panel-body" style="height: calc(100vh - 240px); overflow-y: scroll">
                            @if(isset($obj['departments']) && isset($listAccount) && !empty($obj['departments']))
                                @foreach($obj['departments'] as $dep)
                                    <p class="text-bold">{{$dep['name']}}</p>

                                    @php $tempList = collect($listAccount)
                                                    ->filter(function ($item)use($dep){ return isset($item['department']['id']) && $item['department']['id'] ==$dep['id'];})->toArray()
                                    @endphp
                                    @if(!empty($tempList))
                                        @foreach($tempList as $item)
                                            @if($item['department']['id'] == $dep['id'])
                                                <div class="form-group">
                                                    <label class="js-label-member">
                                                        <input name="obj[account][]" id="obj-select-account"
                                                               type="checkbox"
                                                               @if(isset($listAccountAssign) &&  collect($listAccountAssign)->pluck('account_id')->contains(strval($item['_id'])))
                                                               checked
                                                               @endif
                                                               value="{{$item['_id']}}"
                                                               class="styled"> {{$item['name']}}
                                                        @if(isset($item['chuc_vu_hien_tai']['name']))
                                                            - {{$item['chuc_vu_hien_tai']['name']}}@endif {{'('.@$item['code'].')'}}
                                                    </label>

                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="pl-2 text-grey text-italic">Chưa có nhân viên</p>
                                    @endif

                                @endforeach

                            @else
                                <p class="text-grey">Bạn cần phòng cập nhật phòng ban trước</p>
                                <p>
                                    @if(isset($obj))
                                        <a href="{{admin_link('project/_list_manage?obj[id]='.$obj['id'].'&action=assign-dep')}}">
                                            Nhấn vào đây để cập nhật phòng ban cho dự án @if(isset($obj['name']))
                                                <b>{{$obj['name']}}</b>
                                            @endif
                                        </a>
                                    @endif
                                </p>

                            @endif

                        </div>


                        <button
                                style="position: fixed; right: 15px; bottom: 10px;"
                                onclick="return MNG_POST.update('{{admin_link('project/assign_project_member'.'?obj[id]='.$obj['id'])}}','#form1');"
                                id="save-button" type="button" class="btn btn-info">Gán dự án cho nhân viên
                        </button>

                    </div>

                </div>
            @endif
        </form>

    </div>

    <script>
        try {
            document.getElementById('selected-project').scrollIntoView('top');
            window.scrollBy(0, -150); // Adjust scrolling with a negative value here

        } catch (e) {
            //chưa selected
        }

        function findProject(event) {
            let value = event.target.value.trim().toLowerCase();
            if (value.length > 0) {
                $('.js-label').each(function (i, obj) {


                    if ($(obj).text().toLowerCase().includes(value)) {
                        $(obj).parent().show()

                    } else {
                        $(obj).parent().hide()
                    }
                })
            } else {
                $('.js-label').parent().show()
            }

        }

        function findMember(event) {
            let value = event.target.value.trim().toLowerCase();
            if (value.length > 0) {
                $('.js-label-member').each(function (i, obj) {


                    if ($(obj).text().toLowerCase().includes(value)) {
                        $(obj).parent().show()

                    } else {
                        $(obj).parent().hide()
                    }
                })
            } else {
                $('.js-label-member').parent().show()
            }

        }

        function selectProject() {
            let link = "{{admin_link('project/_list_manage?obj[id]=')}}"
            @if(!isset($action) ||empty($action))
                    @php
                        $action= 'assign-dep'
                    @endphp
                    @endif
                window.location.href = `${link}${event.target.value}&action={{$action}}`
        }
    </script>
@stop
