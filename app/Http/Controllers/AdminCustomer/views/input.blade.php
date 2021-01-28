@extends($THEME_EXTEND)

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}
@stop
@section('BREADCRUMB_REGION')
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

        #upload-avatar {
            display: none;
        }

        #avatar-container:hover #upload-avatar {
            display: block;
            margin-left: auto;
            margin-right: auto;

        }
        @media (max-width: 576px) {
            #obj_tk_ngan_hang tr {
                display: flex;
                flex-direction: column;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
    @if(!isset($allCity))
        @php
            $allCity = collect(\App\Http\Models\Location::getAllCity());
        @endphp
    @endif
    @php($allBankDataList = \App\Http\Models\MetaData::getAllByType(\App\Http\Models\MetaData::STAFF_NGAN_HANG))
    @php($allLienHeDataList = \App\Http\Models\MetaData::getAllByType(\App\Http\Models\MetaData::STAFF_LIEN_HE_KHAC))

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Cập nhật thông tin khách hàng </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('customer')}}">Quản lý khách hàng</a></li>
            @if(isset($obj['_id']) && $obj['_id'])
                <li class="active">Cập nhật thông tin</li>
            @else
                <li class="active">Thêm khách hàng mới</li>
            @endif
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('customer/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm khách hàng mới
                </a>
            </li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <form name="postInputForm" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="w-100 d-flex form-horizontal " method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:0}}"/>
            <div class="col-md-4">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Thông tin khách hàng</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Họ tên','key'=>'name'],
                                    'note'=>['label'=>'*','class'=>'text-danger']])

                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số điện thoại','key'=>'phone']])
                            </div>

                            {{--<div class="form-group">
                                <label class="control-label col-md-3">Phone (dùng để login)</label>
                                <div class="col-md-6">
                                    <input name="obj[phone]" id="obj-phone" value="{{isset($obj['phone'])?$obj['phone']:''}}" type="text" class="form-control" placeholder="Số điện thoại dùng để đăng nhập">
                                </div>
                                <div class="col-md-3">
                                    <select id="obj-verified_phone" name="obj[verified][phone]" class="select">
                                        <option value=false>Chưa xác thực</option>
                                        <option @if(isset($obj->verified['phone']) && $obj->verified['phone']=='true') selected="selected" @endif value=true>Đã xác thực</option>
                                    </select>
                                </div>
                            </div>--}}
                            <div class="d-flex">
                                <div class="col-12">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Email','key'=> 'email']])
                                </div>
                                {{--<div class="col-3">
                                    <div class="form-group">
                                        <label for="">Xác thực email</label>
                                        <select id="obj-verified_email" name="obj[verified][email]" class="select">
                                            <option value=false>Chưa xác thực</option>
                                            <option @if(isset($obj->verified['email']) && $obj->verified['email']=='true') selected="selected" @endif value=true>Đã xác thực</option>
                                        </select>
                                    </div>
                                </div>--}}
                            </div>

                            <div class="d-flex">
                                <div class="col-6">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Địa chỉ','key'=>'addr']])
                                </div>
                                <div class="col-3">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Ngày sinh','key'=>'birthday', 'class' => 'datepicker', 'value' => \App\Elibs\Helper::showMongoDate(@$obj['birthday'],'d/m/Y')]])
                                </div>
                                <div class="col-3">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'CMND/CCCD','key'=>'can_cuoc_cong_dan']])
                                </div>
                            </div>



                            <div class="d-flex">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="control-label">Giới tính</label>
                                        <select id="obj-gender" name="obj[gender]" class="select">
                                            <option value="">Chọn giới tính</option>
                                            <option @if(isset($obj['gender']) && $obj['gender']=='male') selected="selected" @endif value="male">Nam giới</option>
                                            <option @if(isset($obj['gender']) && $obj['gender']=='female') selected="selected" @endif value="male">Nữ giới</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="control-label">Trạng thái</label>
                                        <select id="obj-status" name="obj[status]" class="select">
                                            <option value="">Chưa lựa chọn</option>
                                            <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Customer::STATUS_ACTIVE) selected="selected" @endif value="{{\App\Http\Models\Customer::STATUS_ACTIVE}}">Hoạt động</option>
                                            <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Customer::STATUS_INACTIVE) selected="selected" @endif value="{{\App\Http\Models\Customer::STATUS_INACTIVE}}">Chờ duyệt</option>
                                            <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Customer::STATUS_DISABLE) selected="selected" @endif value="{{\App\Http\Models\Customer::STATUS_DISABLE}}">Khóa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Mã giới thiệu</label>
                                        <div>
                                            <a class="form-control" href="{{ \App\Http\Models\Customer::buildLinkMaGioiThieu(@$obj['ma_gioi_thieu']) }}" target="_blank">{{ @$obj['ma_gioi_thieu']??'Chưa đủ doanh số' }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($obj,'customer')}}','{{$obj['_id']}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>
                            @endif
                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm','add-new');" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Lưu & Thêm mới
                            </button>
                            <button type="button"  onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');" class="btn btn-info bg-danger-800 pull-right mr-15">
                                <i class=" icon-database-check position-left"></i>Lưu lại
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-5">
                <div class="panel panel-white">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-teal-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vichietkhau['total_money']) }}</h3>
                                    Ví chiết khấu
                                </div>
                                <div id="vi-chietkhau"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-blue-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vitichluy['total_money']) }}</h3>
                                    Ví tích luỹ
                                </div>
                                <div id="vi-tichluy"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-pink-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vicongno['total_money']) }}</h3>
                                    Ví công nợ
                                </div>
                                <div id="vi-congno"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-success-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vihoahong['total_money']) }}</h3>
                                    Ví hoa hồng
                                </div>
                                <div id="vi-hoahong"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-info-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">Đang cập nhật</h3>
                                    Kho điểm
                                </div>
                                <div id="vi-khodiem"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-4">
                            <!-- Today's revenue -->
                            <div class="panel bg-blue-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vitieudung['total_money']) }}</h3>
                                    Ví tiêu dùng
                                </div>
                                <div id="vi-tieudung"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>

                    </div>
                </div>

                {{--Tài khoản ngân hàng--}}
                <div class="panel panel-white">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th width="300">Tài khoản ngân hàng</th>
                            <th width="200">Số</th>
                            <th width="1" class="text-right"><a onclick="clone_tk_ngan_hang()"
                                                                class="btn btn-link text-primary">
                                    <i class="icon-add-to-list"></i></a></th>
                        </tr>
                        </thead>
                        <tbody id="obj_tk_ngan_hang">
                        @isset($obj['tk_ngan_hang'])
                            @foreach(@$obj['tk_ngan_hang'] as $key=>$value)
                                <tr>
                                    <td>
                                        <select name="obj[tk_ngan_hang][{{$key}}][id]" class="select-search select-md"
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allBankDataList as $val)
                                                <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-type-number"
                                               name="obj[tk_ngan_hang][{{$key}}][so]"
                                               value="{{@$value['so']}}"
                                               placeholder="Nhập số">

                                    </td>
                                    <td><i class="icon-trash text-danger"
                                           onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                    </td>
                                </tr>

                            @endforeach
                        @endisset

                        </tbody>
                    </table>
                </div>
                {{--Liên hệ khác--}}
                <div class="panel panel-white">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th width="300">Liên hệ khác</th>
                            <th width="200">Thông tin</th>
                            <th width="1" class="text-right"><a onclick="clone_lien_he_khac()"
                                                                class="btn btn-link text-primary">
                                    <i class="icon-add-to-list"></i></a></th>
                        </tr>
                        </thead>
                        <tbody id="obj_lien_he_khac">
                        @isset($obj['lien_he_khac'])
                            @foreach(@$obj['lien_he_khac'] as $key=>$value)
                                <tr>
                                    <td>
                                        <select name="obj[lien_he_khac][{{$key}}][id]" class="select-search select-md"
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allLienHeDataList as $val)
                                                <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                               name="obj[lien_he_khac][{{$key}}][thong_tin]"
                                               value="{{@$value['thong_tin']}}"
                                               placeholder="Nhập thông tin...">

                                    </td>
                                    <td><i class="icon-trash text-danger"
                                           onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                    </td>
                                </tr>

                            @endforeach
                        @endisset

                        </tbody>
                    </table>
                </div>
                {{--files--}}
                <input type="hidden" name="obj[files_thong_tin_co_ban]" value="">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h6 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gFile" aria-expanded="true">File đính kèm</a>
                        </h6>
                    </div>
                    <div id="gFile" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body no-padding-bottom">
                            <div id="documentFileRegion">
                                @if(isset($obj['files_thong_tin_co_ban']) && $obj['files_thong_tin_co_ban'] && is_array($obj['files_thong_tin_co_ban']))
                                    @foreach($obj['files_thong_tin_co_ban'] as $key=> $file)
                                        <div class="form-group js-document-container" id="file_{{$key}}">

                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <div style="display: flex">
                                                        <input type="text" style="z-index: 0" class="form-control "
                                                               name="obj[files_thong_tin_co_ban][name][]"
                                                               @isset($file['name']) value="{{$file['name']}}" @endisset
                                                               placeholder="Tên file">
                                                        <input type="text" style="z-index: 0" readonly=""
                                                               class="form-control js-document-file"
                                                               name="obj[files_thong_tin_co_ban][path][]"
                                                               @isset($file['path']) value="{{$file['path']}}" @endisset

                                                               placeholder="File tài liệu">
                                                    </div>

                                                    <div class="input-group-btn">
                                                        <a target="_blank"
                                                           href="{{\App\Http\Models\Media::getFileLink($file['path'])}}"
                                                           class="btn btn-default js-document-link">Xem file</a>
                                                        <a onclick="_removeFile('#file_{{$key}}')" href="javascript:void(0)"
                                                           class="btn btn-default js-document-del"><i
                                                                    class="icon-trash text-danger"></i> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="col-md-11 p-3  text-right">
                                    <button id="pickfiles" type="button" class="btn bg-primary btn-xs"><i
                                                class="fa fa-plus"></i>
                                        Thêm file
                                    </button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-md-3">
                {{--hình ảnh--}}
                {{--<div class="panel panel-white">
                    <div class="panel-heading">
                        <?php
                        $avatar = "";
                        if (!isset($obj['avatar_url']) || empty($obj['avatar_url'])) {
                            $avatar = '/images/no-avatar.png';
                        } else {
                            $avatar = \App\Http\Models\Media::getFileLink($obj['avatar_url']);
                        }
                        ?>
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gImage" aria-expanded="true">Hình ảnh</a>
                            {!!  isset($avatar) ? ' <a style="float: right;" href="'.$avatar.'" id="xem-lightbox" data-popup="lightbox">[Xem]</a>':''!!}

                        </h3>
                    </div>
                    <div id="gImage" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="d-flex">
                                <div style="height: 200px; width: 100%;">

                                    <div id="avatar-container" class="border-green "
                                         style="border:1px dashed ; height: 100px;height: 100%;background-position: center;background-repeat: no-repeat;background-size: contain ;display: flex;align-items: center ;background-image: url('{{$avatar}}')">
                                        <div class="text-center" style="width: 100%;">
                                            <input type="hidden" value="{{@$obj['avatar_url']}}" id="input-avatar"
                                                   name="obj[avatar_url]">
                                            <button title="Nhấn để thay ảnh"
                                                    id="upload-avatar"
                                                    class="btn btn-sm btn-info">
                                                <i class="icon-image5"></i>
                                            </button>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>--}}
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Tài khoản</a>
                        </h3>
                    </div>
                    <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Tài khoản đăng nhập','value' => @$obj['account'], 'key' => 'o','disabled' => 'disabled']])
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['type' => 'password', 'label'=>'Mật khẩu đăng nhập','key'=>'password', 'value' => '']])
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['type' => 'password', 'label'=>'Nhập lại mật khẩu','key'=>'cfpassword']])
{{--                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Ngày kích hoạt','key'=>'actived_at', 'class' => 'datepicker', 'value' => \App\Elibs\Helper::showMongoDate(@$obj['actived_at'],'d/m/Y')]])--}}
                                {{--@include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Ngày gia hạn','key'=>'end_at', 'class' => 'datepicker', 'value' => \App\Elibs\Helper::showMongoDate(@$obj['end_at'],'d/m/Y')]])--}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <div style="display: none" id="uploadFileItemClone">
        <div class="form-group js-document-container">
            <div class="col-md-12">
                <div class="input-group"><span
                            style="position: absolute;display: none; z-index: 100; right: 200px; top: 8px;"
                            class="js-document-loading"><i
                                class="fa fa-spinner fa-spin"></i> Đang upload vui lòng đợi....</span>
                    <div style="display: flex">
                        <input type="text" style="z-index: 0" class="form-control "
                               name="obj[files_thong_tin_co_ban][name][]" value="" placeholder="Tên file">
                        <input type="text" style="z-index: 0" readonly="" class="form-control js-document-file"
                               name="obj[files_thong_tin_co_ban][path][]" value="" placeholder="File tài liệu">

                    </div>
                    <div class="input-group-btn">
                        <a target="_blank" href="" class="btn btn-default js-document-link">Xem file</a>
                        <a href="javascript:void(0)" class="btn btn-default js-document-del"><i
                                    class="icon-trash text-danger"></i> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });
        function clone_tk_ngan_hang() {
            let index = $('#obj_tk_ngan_hang tr').length
            let temp_select_class = "select-search-" + Number(new Date())
            let tmp = `<tr>
                                <td>
                                   <select name="obj[tk_ngan_hang][${index}][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allBankDataList as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control input-type-number" name="obj[tk_ngan_hang][${index}][so]"

                                           placeholder="Nhập số">

                                </td>
                                <td class='text-right'><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>
`
            $('#obj_tk_ngan_hang').append(tmp)
            DATE_PICKER_INIT()
            INPUT_NUMBER()
            $(`.${temp_select_class}`).select2()
        }

        function clone_lien_he_khac() {
            let index = $('#obj_lien_he_khac tr').length
            let temp_select_class = "select-search-" + Number(new Date())
            let tmp = `<tr>
                                <td>
                                   <select name="obj[lien_he_khac][${index}][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allLienHeDataList as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="obj[lien_he_khac][${index}][thong_tin]"

                                           placeholder="Nhập thông tin">

                                </td>
                                <td class='text-right'><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>
`
            $('#obj_lien_he_khac').append(tmp)
            DATE_PICKER_INIT()
            INPUT_NUMBER()
            $(`.${temp_select_class}`).select2()
        }

        MNG_MEDIA.uploadInit({
            loading_element: '#loading_upload',
            input_element: '#document_file',
            link_element: '#document_file_link'
        });
        MNG_MEDIA.uploadAvatarInit();


        function _removeFile($element) {
            bootbox.confirm("File của bạn sẽ bị xóa.<br/>Bạn có chắc chắn muốn thực hiện hành động này?", function (result) {
                if (result) {
                    $($element).remove();
                }
            });
        }

    </script>
@stop
