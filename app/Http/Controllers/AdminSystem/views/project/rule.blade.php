@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/bootstrap_multiselect.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_multiselect.js') !!}
@stop

@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span
                        class="text-semibold">Quản lý dự án, công trình</span></h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="javascript:void(0)">Hệ thống</a></li>
            <li class=""><a href="{{admin_link('project')}}">Quản lý dự án, công trình</a></li>
            <li class=""><a href="{{admin_link('project/rule')}}">Phân quyền</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    @if(!$project)
        <div class="alert alert-danger alert-styled-left alert-bordered">
            @if(!app('request')->input('id'))
                Bạn vui lòng chọn dự án trước khi phân quyền
            @else
                Không tìm thấy dự án!
            @endif

        </div>
    @endif
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách thành viên thuộc dự án</strong></h3>
                    (Tìm thấy : {{$listObj->total()}} bản ghi)
                    <div class="heading-elements">
                        <form class="" method="GET">
                            <div class="form-inline">
                                <div class="input-group content-group">
                                    <div class="has-feedback has-feedback-left">
                                        <input name="q" value="{{app('request')->input('q')}}" type="text"
                                               class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                                        <div class="form-control-feedback">
                                            <i class="icon-search4 text-muted text-size-base"></i>
                                        </div>
                                    </div>

                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Tìm kiếm
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" id="memberTbl" style="min-height: 400px">
                    <table class="table table-striped  table-io">
                        <thead>
                        <tr>
                            <th>Họ và tên</th>
                            <th>Tài khoản</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Phòng ban - Chức vụ</th>
                            <th>Nhóm chức năng</th>
                            <th width="120" class="text-right">Chức năng</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listObj as $key=>$obj)
                            <tr id="itemRow_{{$obj->id}}">
                                <td>
                                    <div class="text-bold text-primary-800">{{$obj->name}}</div>
                                </td>
                                <td> {{$obj->account}}</td>
                                <td> {{$obj->email}}</td>
                                <td> {{$obj->phone}}</td>
                                <td>
                                    @if(count($obj->departments)>1)
                                        <div class="btn-group" style="top:-2px">
                                            <a href="#" class="badge bg-indigo-400 dropdown-toggle"
                                               data-toggle="dropdown">{{count($obj->departments)}} Phòng ban<span
                                                        class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @foreach($obj->departments as $key=> $item)
                                                    @if(isset($item['id']))
                                                        <li><a href="#">
                                                                @if(isset($allDepartment[$item['id']]))
                                                                    {{$allDepartment[$item['id']]['name']}}
                                                                    @if(isset($obj->positions[$key]['name']))
                                                                        -
                                                                        <span class="badge badge-info">{{$obj->positions[$key]['name']}}</span>
                                                                    @endif
                                                                    @if(isset($obj->departments[$key]['main']) && $obj->departments[$key]['main']==1)
                                                                        - <i title="Phòng ban chính"
                                                                             class="icon-checkmark-circle2 text-success-800"></i>
                                                                    @endif
                                                                @else
                                                                    <i class="text-danger">Không xác định</i>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                </td>
                                <td>

                                    @if(isset($accountPermission[$obj['_id']]['role_group']))
                                        @php
                                            $temp =  $accountPermission[$obj['_id']]['role_group'];
                                        @endphp
                                        @if(isset($roleGroup[$temp]))
                                            {{$roleGroup[$temp]['name']}}
                                        @else
                                            <i class="text-grey">Chưa phân quyền...</i>
                                        @endif
                                    @else
                                        <i class="text-grey">Chưa phân quyền...</i>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <ul class="icons-list">

                                        {{--<li class="text-primary-600">--}}
                                        {{--<a href="{{admin_link('staff/input?id='.$obj['_id'].'')}}" title="Xem/Sửa">--}}
                                        {{--<i class="icon-pencil7"></i>--}}
                                        {{--</a>--}}
                                        {{--</li>--}}
                                        @if(isset($accountPermission[$obj['_id']]['role_group']))
                                        <li class="text-danger-600">
                                            <a href="javascript:void(0)" title="Xóa quyền"
                                               onclick="return MNG_POST.deleteItem('{{admin_link('project/rule-action?action=remove&staff='.$obj['_id'].'&project_id='.$project['_id'].'&token='.\App\Elibs\Helper::buildTokenString($project['_id'].$obj['_id']).'')}}')">
                                                <i class="icon-trash"></i>
                                            </a>
                                        </li>
                                        @endif
                                        <li class="text-primary-600">
                                            <a
                                                    @if(isset($project['_id']))
                                                    onclick="_SHOW_FORM_REMOTE('{{admin_link('project/rule-popup?account_id='.$obj['_id'].'&project_id='.$project['_id'])}}')"
                                                    @endif
                                                    title="Xem/Sửa">
                                                <i class="icon-pencil7"></i>
                                            </a>
                                        </li>

                                    </ul>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


                @if(!$listObj->count())
                    <div class="table-responsive p-3">
                        <div>
                            Không tìm thấy nhân viên nào trong danh sách này.
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Phòng ban liên quan</strong></h3>
                    Các nhân viên của phòng ban sẽ được thêm vào danh sách phân quyền, thêm chức năng
                </div>
                <form name="postInputForm"
                      onsubmit="return MNG_POST.update('{{admin_link('project/_save')}}','#postInputForm');"
                      id="postInputFormDep" class="form-horizontal" method="post">
                    <div class="panel-body p-4" style="border-top: 1px solid #ccc">


                        {{--thông tin cần có với form meta-data cơ bản--}}
                        <input name="project_id" id="obj-project_id"
                               value="{{isset($project['_id'])?$project['_id']:''}}" type="hidden">

                        <div class="form-group">
                            <label class="control-label col-md-3">Tên dự án </label>
                            <div class="col-md-9">
                                <input readonly
                                       value="{{isset($project['name'])?$project['name']:'Bạn cần chọn dự án'}}"
                                       type="text" class="form-control">
                            </div>
                        </div>
                        @if(isset($project['brief']) && $project['brief'])
                            <div class="form-group">
                                <label class="control-label col-md-3">Mô tả ngắn </label>
                                <div class="col-md-9">
                                    <input readonly value="{{isset($project['brief'])?$project['brief']:''}}"
                                           type="text" class="form-control">
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Phòng ban <i class="text-danger">*</i> </label>
                            <div class="col-md-9">

                                {{--<div class="input-group content-group">--}}
                                {{--<div class="">--}}
                                <select name="obj[departments][id][]"
                                        class="select-search select-md"
                                        onchange="loadMemberToList()"
                                        multiple="true"

                                >

                                    @isset($allDepartment)
                                        @foreach($allDepartment as $val)
                                            <option
                                                    @if(isset($project['departments'])
                                                    && array_first($project['departments'],function($item) use($val){
                                                            return $item['id'] == $val['_id'];}))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}
                                                @if(isset($val['parent_dep']['name']))
                                                    ({{$val['parent_dep']['name']}})
                                                @endif
                                            </option>

                                        @endforeach
                                    @endisset
                                </select>
                                {{--</div>--}}

                                {{--<div class="input-group-btn">--}}
                                {{--<a target="_blank" onclick="return searchStaffByString(this)" type="button" class="btn btn-default btn-sm">Lưu lại</a>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right">
                        <button type="button"
                                onclick="return  MNG_POST.update('{{admin_link('project/rule-action?action=save_dep')}}','#postInputFormDep');"
                                class="btn btn-primary">Phân dự án cho phòng ban
                        </button>
                    </div>
                </form>
            </div>
            {{--<div class="panel panel-flat">--}}
            {{--<div class="panel-heading">--}}
            {{--<h3 class="panel-title"><strong>Thêm nhân viên mới</strong></h3>--}}
            {{--Thêm nhân viên mới vào dự án--}}
            {{--</div>--}}
            {{--<form name="postInputForm"--}}
            {{--onsubmit="return MNG_POST.update('{{admin_link('project/_save')}}','#postInputForm');"--}}
            {{--id="postInputForm" class="form-horizontal" method="post">--}}
            {{--<div class="panel-body p-4" style="border-top: 1px solid #ccc">--}}


            {{--thông tin cần có với form meta-data cơ bản--}}
            {{--<input name="project_id" id="obj-project_id"--}}
            {{--value="{{isset($project['_id'])?$project['_id']:''}}" type="hidden">--}}

            {{--<div class="form-group">--}}
            {{--<label class="control-label col-md-3">Tên dư án </label>--}}
            {{--<div class="col-md-9">--}}
            {{--<input readonly--}}
            {{--value="{{isset($project['name'])?$project['name']:'Bạn cần chọn dự án'}}"--}}
            {{--type="text" class="form-control">--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@if(isset($project['brief']) && $project['brief'])--}}
            {{--<div class="form-group">--}}
            {{--<label class="control-label col-md-3">Mô tả ngắn </label>--}}
            {{--<div class="col-md-9">--}}
            {{--<input readonly value="{{isset($project['brief'])?$project['brief']:''}}"--}}
            {{--type="text" class="form-control">--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}

            {{--<div class="form-group">--}}
            {{--<label class="control-label col-md-3">Nhân viên <i class="text-danger">*</i> </label>--}}
            {{--<div class="col-md-9">--}}

            {{--<div class="input-group content-group">--}}
            {{--<div class="">--}}
            {{--<input name="staff" id="staff" type="text"--}}
            {{--class="form-control input-sm"--}}
            {{--placeholder="Nhập email hoặc tài khoản nhân viên hoặc số phone">--}}
            {{--</div>--}}

            {{--<div class="input-group-btn">--}}
            {{--<a target="_blank" onclick="return searchStaffByString(this)" type="button"--}}
            {{--class="btn btn-default btn-sm">Tìm kiếm</a>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

            {{--</div>--}}
            {{--<div class="panel-footer text-right">--}}
            {{--<button type="button"--}}
            {{--onclick="return  MNG_POST.update('{{admin_link('project/rule-action?action=add')}}','#postInputForm');"--}}
            {{--class="btn btn-primary">Thêm nhân viên vào dự án--}}
            {{--</button>--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--</div>--}}

        </div>
    </div>
    <script type="text/javascript">
        function searchStaffByString(obj) {
            var staff = document.getElementById('staff').value;
            if (staff.trim() == '') {
                alert('Bạn cần nhập từ khóa tìm kiếm!');
                return false
            }
            jQuery(obj).attr('href', '{{admin_link('/staff?q=')}}' + staff + '');
            return true;
        }

        function loadMemberToList() {

        }
    </script>
@stop
