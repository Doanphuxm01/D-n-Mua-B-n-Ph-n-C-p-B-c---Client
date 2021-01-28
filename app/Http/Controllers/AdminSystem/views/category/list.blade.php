@extends($THEME_EXTEND)
@section('CSS_REGION')

@stop
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.time.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}

@stop
@section('BREADCRUMB_REGION')
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="javascript:void(0)">Hệ thống</a></li>
            <li class=""><a href="{{admin_link('category')}}">Quản lý Hạng mục/ Kiểu/ Loại</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="javascript:void(0)" onclick="return  _SHOW_FORM_REMOTE('{{admin_link('category/input')}}');">
                {{--<a href="javascript:void(0)" onclick="MNG_STAFF.showFormPosition(0)">--}}
                    <b><i class="icon-file-plus2"></i></b> Thêm Hạng mục/ Kiểu/ Loại
                </a>
            </li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    @if(!\App\Http\Models\Member::haveRole(\App\Http\Models\Member::mng_category))
        <div class="alert alert-danger alert-styled-left alert-bordered">
            Bạn không có quyền xem danh sách Hạng mục/ Kiểu/ Loại
        </div>
    @else
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Danh sách Hạng mục/ Kiểu/ Loại</strong></h3>
            (Tổng số : {{$data->total()}} hạng mục)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" class="select-search  select-xs" style="width: 250px">
                                    <option value="0">--Tất cả--</option>
                                    @foreach(collect(App\Http\Models\MetaData::$typeRegister)->sortBy('name') as $val)
                                        @if(($val['key'] != \App\Http\Models\MetaData::POSITION) && ($val['key'] != \App\Http\Models\MetaData::DEPARTMENT)
                                        && ($val['key'] != \App\Http\Models\MetaData::LOCATION_REGION)&& ($val['key'] != \App\Http\Models\MetaData::LOCATION_COUNTRY))
                                        <option @if(isset($q_status) && $val['key'] == $q_status) selected @endif value="{{ $val['key'] }}">{{ $val['name'] }}</option>
                                        @endif
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
        <div class="table-responsive" id="domainTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>
                        Tên Hạng mục/ Kiểu/ Loại
                    </th>
                    <th>
                        Thuộc
                    </th>
                    <th class="text-center">Chức năng</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key=>$val)
                    <tr id="itemRow_{{$val['_id']}}">
                        <td class="text-center">
                            {{$key+1}}
                        </td>
                        <td>
                            <b>{{$val['name']}}</b>
                        </td>
                        <td>
                            @if(isset($val['type']) && $val['type'] == @App\Http\Models\MetaData::$typeRegister[$val['type']]['key'])
                                {{@App\Http\Models\MetaData::$typeRegister[$val['type']]['name']}}
                            @endif
                        </td>
                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="text-primary-600">
                                    <a href="javascript:void(0)" onclick="return  _SHOW_FORM_REMOTE('{{admin_link('category/input?id='.$val['_id'])}}')">
                                        <i class="icon-pencil7"></i>
                                    </a>
                                </li>
                                <li class="text-danger-600">
                                    <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                       onclick="return MNG_POST.deleteItem('{{\App\Http\Models\BaseModel::buildLinkDelete($val,'category')}}','{{$val['_id']}}')">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            </ul>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="panel-body">
            @if(!$data->count())
                <div class="alert alert-danger alert-styled-left alert-bordered">
                    Không tìm thấy dữ liệu nào ở trang này. (Hãy kiểm tra lại các điều kiện tìm kiếm hoặc
                    phân trang...)
                </div>
            @endif
            <div class="text-center pagination-rounded-all">{{ $data->render() }}</div>
        </div>
    </div>
    @endif
@stop
