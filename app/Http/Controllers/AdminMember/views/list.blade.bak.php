@extends($THEME_EXTEND)

@section('BREADCRUMB_REGION')
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('staff')}}">Danh sách nhân viên</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('staff/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm nhân viên mới
                </a>
            </li>

        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    {{\App\Elibs\Debug::DebugPermission()}}
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Quản lý danh sách tài khoản nhân viên</strong></h3>
            (Tìm thấy : {{$listObj->total()}} tài khoản)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" id="" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    @foreach(App\Http\Models\BaseModel::getListStatus($q_status) as $status)
                                        <option @if(isset($status['checked'])) selected="selected"
                                                @endif value="{{ $status['id'] }}">{{ $status['text'] }}</option>
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
                    <th>Họ tên/Tài khoản</th>
                    <th>Email</th>
                    <th>Cập nhật</th>
                    <th width="120" class="text-right">Chức năng</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$obj)
                    <tr id="itemRow_{{$obj->id}}">
                        <td>
                            <div class="text-bold text-primary-800">
                                <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/preview?id='.$obj->_id)}}')">{{$obj->account}}</a>
                            </div>
                            {{$obj->name}}
                        </td>

                        <td>
                            {{$obj->email}}
                            <div>
                                {{$obj->phone}}
                            </div>
                        </td>
                        <td>
                            {{\App\Elibs\Helper::showMongoDate($obj->created_at)}}
                            <div>
                                {{\App\Elibs\Helper::showMongoDate($obj->updated_at)}}
                            </div>
                        </td>

                        <td class="text-right">
                            <div style="margin-bottom: 5px"
                                 class="label label-{{ \App\Http\Models\Post::getStatus($obj['status'])['style'] }}">{{ \App\Http\Models\Post::getStatus($obj['status'])['text'] }}
                            </div>
                            <ul class="icons-list">
                                @if(\App\Http\Models\Member::haveRole([\App\Http\Models\Member::mng_staff_update]))

                                    <li class="text-primary-600">
                                        <a href="{{admin_link('staff/input?id=')}}{{$obj->id}}" title="Sửa">
                                            <i class="icon-pencil7"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(\App\Http\Models\Member::haveRole([\App\Http\Models\Member::mng_staff_delete]))
                                    <li class="text-danger-600">
                                        <a href="javascript:void(0)" title="Xóa"
                                           onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Member::buildLinkDelete($obj,'member')}}','{{$obj['_id']}}')">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </li>
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
