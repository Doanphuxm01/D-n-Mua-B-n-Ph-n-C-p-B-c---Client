@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_checkboxes_radios.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}

@stop
@extends($THEME_EXTEND)
@php
    //project
      $mng_obj = \App\Http\Models\Role::mng_project;

      $mng_action = \App\Http\Models\Role::mng_action_update;
      $requireRole = [\App\Http\Models\Role::getRoleKey($mng_obj, $mng_action)];
      $canUpdateProject= \App\Http\Models\Role::haveRole2($requireRole);

      $mng_action = \App\Http\Models\Role::mng_action_role;
      $requireRole = [\App\Http\Models\Role::getRoleKey($mng_obj, $mng_action)];
      $canRoleProject= \App\Http\Models\Role::haveRole2($requireRole);

      $mng_action = \App\Http\Models\Role::mng_action_delete;
      $requireRole = [\App\Http\Models\Role::getRoleKey($mng_obj, $mng_action)];
      $canDeleteProject= \App\Http\Models\Role::haveRole2($requireRole);


@endphp
@section('BREADCRUMB_REGION')

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="javascript:void(0)">Hệ thống</a></li>
            <li class=""><a href="{{admin_link('project')}}">Quản lý dự án, công trình</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            @if($canUpdateProject)
                <li>
                    <a href="javascript:void(0)"
                       onclick="return  _SHOW_FORM_REMOTE('{{admin_link('project/input')}}');">
                        <b><i class="icon-file-plus2"></i></b> Thêm dự án mới
                    </a>
                </li>
                <li>
                    <a href="{{admin_link('project/list?excel=1')}}">
                        <b><i class="icon-file-plus2"></i></b> Xuất excel
                    </a>
                </li>
            @endif
            @if($canRoleProject)
                <li>
                    <a href="{{admin_link('/project/_list_manage?action=assign-dep')}}">
                        <b><i class="icon-file-excel"></i></b> Phân quyền dự án
                    </a>
                </li>
            @endif

        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Danh sách dự án, công trình</strong></h3>
            (Tìm thấy : {{$listObj->total()}} bản ghi)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        {{--<div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    <option @if(isset($q_status) && $q_status==\App\Http\Models\BaseModel::STATUS_ACTIVE) selected="selected" @endif value="{{\App\Http\Models\BaseModel::STATUS_ACTIVE}}">Hoạt động</option>
                                    <option @if(isset($q_status) && $q_status==\App\Http\Models\BaseModel::STATUS_DISABLE) selected="selected" @endif value="{{\App\Http\Models\BaseModel::STATUS_DISABLE}}">Không hoạt động</option>
                                </select>
                            </div>
                        </div>--}}
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_roled"
                                        class="select-search select-xs @if(request('q_roled')) text-danger text-bold @endif"
                                        title="Phân quyền ?" style="width: 230px">
                                    <option value="">--Chọn trạng thái--</option>
                                    <option @if(request('q_roled') == 'da_phan_quyen' ) selected @endif value="da_phan_quyen">
                                        Đã phân quyền
                                    </option>
                                    <option @if(request('q_roled') == 'chua_phan_quyen' ) selected @endif value="chua_phan_quyen">
                                        Chưa phân quyền
                                    </option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_department"
                                        class="select-search select-xs @if(request('q_department')) text-danger text-bold @endif"
                                        title="Thuộc Phòng ban" style="width: 230px">
                                    <option value="">--Chọn phòng ban--</option>
                                    @foreach($dictAllDepartMent as $key=>$val)
                                        <option @if(isset($q_department) && $q_department == $val['_id']) selected="selected"
                                                @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group content-group">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th>Tên dự án</th>
                    <th width='120'>Phòng ban</th>
                    <th width="120">Tạo lúc</th>

                    <th width="120">Cập nhật</th>

                    <th width="120" class="text-right">Chức năng</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$obj)
                    <tr id="itemRow_{{$obj->id}}">
                        <td>
                            <div class="text-bold text-primary-800">{{$obj->name}}</div>
                            @if($obj->full_name)
                                <i>Tên đầy đủ: </i>{{$obj->full_name}}
                            @endif
                            {{$obj->brief}}

                        </td>
                        <td>
                            @if(isset($obj['departments']) &&!empty($obj['departments']) && $obj['departments'] !== null)
                                {{implode(',',array_map( function($item)use($dictAllDepartMent){
                                    if (@$dictAllDepartMent[@$item['id']]){
                                    return @$dictAllDepartMent[@$item['id']]['name'];
                                    }
                                }, $obj['departments']))}}

                            @endif
                        </td>
                        <td>
                            {{\App\Elibs\Helper::showMongoDate($obj['created_at'],'H:i:s d/m/Y')}}
                        </td>
                        <td>
                            {{\App\Elibs\Helper::showMongoDate($obj['updated_at'],'H:i:s d/m/Y')}}
                        </td>

                        <td class="text-right">
                            {{-- <span style="margin-bottom: 5px"
                                   class="label label-{{ \App\Http\Models\BaseModel::getStatus(@$obj['status'])['style'] }}">{{ \App\Http\Models\BaseModel::getStatus(@$obj['status'])['text'] }}
                            </span>--}}
                            <ul class="icons-list">
                                @if(\App\Http\Models\Member::haveRole([\App\Http\Models\Member::mng_project]))
                                    @if($canRoleProject)
                                        <li class="text-primary-600">
                                            <a href="{{admin_link('project/_list_manage?obj[id]='.$obj['_id'].'&action=assign-member')}}"
                                               title="Xem phân quyền">

                                                <i
                                                        @if(isset($obj['departments']) &&!empty($obj['departments']))
                                                        class="icon-user-check text-success"
                                                        @else
                                                        class="icon-users" style="color:black"
                                                        @endif
                                                ></i>
                                            </a>
                                        </li>
                                    @endif

                                    <li class="text-primary-600">
                                        <a href="javascript:void(0)"
                                           onclick="return  _SHOW_FORM_REMOTE('{{admin_link('project/input?id='.$obj['_id'].'')}}');"
                                           title="Xem/Sửa">
                                            <i class="icon-pencil7"></i>
                                        </a>
                                    </li>
                                    @if($canDeleteProject)
                                        <li class="text-danger-600">
                                            <a href="javascript:void(0)" title="Xóa"
                                               onclick="return MNG_POST.deleteItem('{{\App\Http\Models\BaseModel::buildLinkDelete($obj,'project')}}','{{$obj['_id']}}')">
                                                <i class="icon-trash"></i>
                                            </a>
                                        </li>
                                    @endif
                                @endif
                            </ul>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="panel-body">
            @if(!$listObj->count())
                <div class="alert alert-danger alert-styled-left alert-bordered">
                    Không tìm thấy dữ liệu nào ở trang này. (Hãy kiểm tra lại các điều kiện tìm kiếm hoặc
                    phân trang...)
                </div>
            @endif
            <div class="text-center pagination-rounded-all">{{ $listObj->render() }}</div>
        </div>
    </div>
@stop
