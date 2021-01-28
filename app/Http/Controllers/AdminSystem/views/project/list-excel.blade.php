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
            <li class=""><a href="javascript:void(0)">Hệ thống</a></li>
            <li class=""><a href="{{admin_link('project')}}">Quản lý dự án, công trình</a></li>
            <li class="active">Xuất excel</li>
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
            <table id="table-excel" class="table table-bordered overflow-auto">
                <thead>
                <tr>
                    <th>Tên dự án</th>
                    <th>Mô tả</th>
                    <th>Trung tâm</th>
                    <th>Tạo lúc</th>
                    <th>Cập nhật</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$obj)
                    <tr id="itemRow_{{$obj->id}}">
                        <td>{{$obj->name}}</td>
                        <td>{{$obj->brief}}</td>
                        <td>{{collect(@$obj['departments'])->pluck('name')->implode(',')}}</td>
                        <td>
                            {{\App\Elibs\Helper::showMongoDate($obj['created_at'],'H:i:s d/m/Y')}}
                        </td>
                        <td>
                            {{\App\Elibs\Helper::showMongoDate($obj['updated_at'],'H:i:s d/m/Y')}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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