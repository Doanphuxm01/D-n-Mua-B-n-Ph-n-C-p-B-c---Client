@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/bootstrap_multiselect.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_multiselect.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/time-pciker-addons/time-addons.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/js/plugins/pickers/time-pciker-addons/i.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/js.cookie.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}


@stop
@section('BREADCRUMB_REGION')

    @php
        $canView = \Role::isAllowTo(\Role::$ACTION_LIST. \Role::$KAYN_MEMBER);

        $canAdd = \Role::isAllowTo(\Role::$ACTION_EDIT. \Role::$KAYN_MEMBER) || \Role::isAllowTo(\Role::$ACTION_EDIT_OF_NOT_ME. \Role::$KAYN_MEMBER) || \Role::isAllowTo(\Role::$ACTION_EDIT_OF_ME. \Role::$KAYN_MEMBER);

        $canDelete = \Role::isAllowTo(\Role::$ACTION_DELETE. \Role::$KAYN_MEMBER) || \Role::isAllowTo(\Role::$ACTION_DELETE_OF_NOT_ME. \Role::$KAYN_MEMBER) || \Role::isAllowTo(\Role::$ACTION_DELETE_OF_ME. \Role::$KAYN_MEMBER);

        $canEdit = $canAdd;

        $canRole = \Role::isAllowTo(\Role::$ACTION_ROLE. \Role::$KAYN_MEMBER);

        $canViewAll = \Role::isAllowTo(\Role::$ACTION_LIST. \Role::$KAYN_MEMBER);



    @endphp
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('staff')}}">Danh sách nhân viên</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            @if($canViewAll)
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="icon-download10 position-left"></i>
                        Xuất toàn bộ dữ liệu
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-backdrop"></div>

                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{admin_link('staff/export_excel?output=basic')}}">Thông tin cơ bản</a></li>
                        <li><a href="{{admin_link('staff/export_excel?output=work-contract')}}">Thông tin công việc(hợp
                                đồng)</a></li>
                        <li><a href="{{admin_link('staff/export_excel?output=work-process')}}">Thông tin công việc(quá
                                trình
                                công tác)</a></li>
                        <li><a href="{{admin_link('staff/export_excel?output=family')}}">Thông tin gia đình</a></li>
                        <li><a href="{{admin_link('staff/export_excel?output=edu-bang-cap')}}">Thông tin Đào tạo(bằng
                                cấp)</a></li>
                        <li><a href="{{admin_link('staff/export_excel?output=edu-chung-chi')}}">Thông tin Đào tạo(chứng
                                chỉ)</a></li>

                    </ul>
                </li>
            @endif
            @if($canAdd)
                <li>
                    <a href="{{admin_link('staff/input')}}">
                        <b><i class="icon-file-plus2"></i></b> Thêm nhân viên mới
                    </a>
                </li>
            @endif

        </ul>
    </div>
@stop
@section('CONTENT_REGION')

    @include("views.components.sidebar", ["allDepartments"=>$allDepartment,])

    <div class="content-wrapper">
        <div class="" style="padding: 0px 12px">

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Quản lý danh sách tài khoản nhân viên</strong></h3>
                    (Tìm thấy : {{$listObj->total()}} tài khoản)
                    <div class="heading-elements">
                        <form class="" method="GET">
                            <div class="form-inline dep-container">
                                <div class="input-group content-group">
                                    <select class="form-control" name="q-job-status" id="">
                                        <option @if(request('q-job-status')=='') selected @endif value="">
                                            Tất cả
                                        </option>
                                        <option @if(request('q-job-status')=='active') selected @endif value="active">
                                            Đang công tác
                                        </option>
                                        <option @if(request('q-job-status')=='deactive' ) selected
                                                @endif value="deactive">Đã nghỉ việc
                                        </option>
                                        <option @if(request('q-job-status')=='temp-deactive' )selected
                                                @endif value="temp-deactive">
                                            Tạm nghỉ
                                        </option>
                                    </select>
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
                                        <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Tìm kiếm
                                        </button>
                                        <button
                                                type="button"
                                                onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/export_popup')}}')"
                                                title="Xuất toàn bộ kết quả tìm được"
                                                class="btn btn btn-primary bg-info-800 btn-sm ml-1">
                                            Xuất excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" id="memberTbl" style="overflow-x: visible">
                    <table class="table table-striped table-bordered table-io">
                        <thead>
                        <tr style="border-top: 1px solid #ccc">
                            <th>Stt</th>
                            <th>Mã Nv</th>
                            <th>Họ tên</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh</th>
                            <th>Chuyên môn</th>
                            <th>Điện thoại</th>
                            <th>Phòng ban/Chức vụ</th>
                            <th>Nhóm quyền</th>
                            {{--<th>Chức vụ</th>--}}
                            <th>Ngày tiếp nhận</th>
                            {{--<th>Cập nhật</th>--}}
                            <th width="68"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $temp = $listObj->toArray();
                        $currentIndex = $temp['per_page']* ($temp['current_page'] -1);
                        @endphp

                        @foreach($listObj as $key=>$obj)
                            <tr id="itemRow_{{$obj->id}}">
                                <td>{{$currentIndex +  $key +1}}</td>
                                <td>
                                    <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/preview?id='.$obj->_id)}}')">
                                        {{@$obj->code}}</a>
                                </td>
                                <td>
                                    {{@$obj->name}}
                                    @if($obj->tinh_trang_cong_viec == "Đã nghỉ việc")
                                        <span class="pull-right " title="Đã nghỉ việc">
                                            <i class="icon-blocked text-orange-800" ></i>
                                        </span>
                                    @elseif($obj->tinh_trang_cong_viec == "Tạm nghỉ")
                                        <span class="pull-right" title="Tạm nghỉ">
                                            <i class="icon-notification2 text-orange-300" ></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{@['male'=>'Nam' , 'female'=>'Nữ'][@$obj->gender]}}
                                </td>
                                <td>
                                    {{\App\Elibs\Helper::showMongoDate(@$obj->date_of_birth)}}
                                </td>
                                <td>
                                    @foreach( collect(@$obj['bang_cap'])->slice(0,2) as $item)
                                        <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/preview?id='.$obj->_id).'&tab=tab-edu'}}')"
                                                class="cursor-pointer">{{@$item['chuyen_mon']['name']}}</a> <br>
                                    @endforeach
                                    @if(collect(@$obj['bang_cap'])->count() > 2)
                                        <span
                                                onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/preview?id='.$obj->_id).'&tab=tab-edu'}}')"
                                                class="label bg-indigo cursor-pointer">...</span>
                                    @endif
                                </td>
                                <td>{{collect($obj->phones)->pluck('value')->implode(',')}}</td>
                                <td>
                                    @if(isset($obj['department']['id']) && isset($allDepartment[$obj['department']['id']]))
                                        {{@$allDepartment[@$obj['department']['id']]['name']}}
                                    @endif
                                    @if(isset($obj['position']['name']))
                                        - {{@$allPosition[@$obj['position']['id']]['name']}}
                                    @endif
                                </td>
                                <td>
                                    {{ (\App\Http\Models\Member::isRoot(@$obj['account'])) ? 'Ăn mày đất khách' : '' }}
                                </td>
                                <td>
                                    @foreach(collect(@$obj['thong_tin_hop_dong_lao_dong'])->reverse()->slice(0,2) as $item)
                                        {{\App\Elibs\Helper::showMongoDate(@$item['ngay_bat_dau'])}} -
                                        <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/preview?id='.$obj->_id).'&tab=tab-work'}}')">
                                            {{@$item['loai_hop_dong']}}</a>
                                    @endforeach
                                </td>

                                <td>
                                    <ul class="list list-inline no-margin">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle text-default" data-toggle="dropdown">
                                                <i class="icon-cog7 position-left"></i>
                                                <span class="caret"></span>
                                            </a>

                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a class="text-primary-700"
                                                       href="{{admin_link('staff/input?id='.$obj['_id'])}}" title="Sửa"><i
                                                                class="icon-pencil7"></i> Thông tin chi tiết</a>
                                                </li>
                                                @if($canRole)
                                                    <li>
                                                        <a class="text-primary-700"
                                                           href="{{admin_link('staff/input?id='.$obj['_id'].'&tab=role')}}"
                                                           title="phân quyền"><i
                                                                    class="icon-key"></i> Phân quyền</a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="{{admin_link('logs?created_by='.$obj['_id'],true)}}"
                                                       title="Xem lịch sử hoạt động"><i class="icon-list"></i> Lịch sử
                                                        hoạt động</a>
                                                </li>
                                                @if($canDelete)
                                                    <li>
                                                        <a href="javascript:void(0);" class="text-danger-800"
                                                           title="Xóa"
                                                           onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Member::buildLinkDelete($obj,'staff')}}','{{$obj['_id']}}')"><i
                                                                    class="icon-trash"></i> Xóa </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
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
        </div>
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
    </script>
@endpush
