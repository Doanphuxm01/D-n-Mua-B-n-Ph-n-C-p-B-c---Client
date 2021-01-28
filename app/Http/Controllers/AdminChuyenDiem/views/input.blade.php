@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/wizards/stepy.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/validation/validate.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/wizard_stepy.js') !!}


@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Yêu cầu rút tiền </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('rut-tien')}}">Yêu cầu chuyển điểm</a></li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <!-- Basic setup -->
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">Yêu cầu chuyển điểm các ví</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>

            <form class="stepy-validation" id="postInputForm">
                @csrf
                <fieldset title="1">
                    <legend class="text-semibold">Chọn thông tin</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Today's revenue -->
                            <div class="panel bg-blue-400">
                                <div class="panel-body">

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vitichluy['total_money']) }}</h3>
                                    Ví tích luỹ
                                    <div class="text-muted text-size-small">
                                        ---
                                    </div>
                                </div>
                                <div id="vi-tichluy"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-6">
                            <!-- Today's revenue -->
                            <div class="panel bg-success-400">
                                <div class="panel-body">


                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vihoahong['total_money']) }}</h3>
                                    Ví hoa hồng
                                    <div class="text-muted text-size-small">
                                        ---
                                    </div>
                                </div>
                                <div id="vi-hoahong"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ví thanh toán</label>
                                <select name="obj[type_vi_thanh_toan]" id="vi-thanh-toan" data-placeholder="Chưa lựa chọn" class="select required">
                                    @php($lsVi = \App\Http\Models\BaseModel::getListViChuyenDiem())
                                    @foreach($lsVi as $vi)
                                    <option value="{{ $vi['id'] }}">{{ $vi['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ví nhận điểm</label>
                                <select name="obj[type_vi_nhan_diem]" id="vi-nhan-diem" data-placeholder="Select position" class="select required">
                                    <option selected value="OBJECT_VITIEUDUNG">Ví tiêu dùng</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Chủ tài khoản</label>
                                <input type="text" value="{{ \App\Http\Models\Member::getCurentAccount() }}" disabled class="text-bold form-control" placeholder="{{ \App\Http\Models\Member::getCurentAccount() }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Họ và tên</label>
                                <input type="text" value="{{ \App\Http\Models\Member::getCurrentFullName() }}" disabled class="text-bold form-control" placeholder="{{ \App\Http\Models\Member::getCurrentFullName() }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số điểm giao dịch</label>
                                <input type="text" id="so-diem-giao-dich" class="input-type-number-format form-control" placeholder="Nhập thông tin">
                                <input type="text" style="display: none" min="50000" name="obj[so_diem_giao_dich]" max="{{ @$vitichluy['total_money'] }}" id="so-diem-giao-dich-hidden" class="required input-type-number form-control" placeholder="Nhập thông tin">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset title="2" class="d-none">
                    <legend class="text-semibold">Xác nhận gửi yêu cầu</legend>

                    <div class="row">
                        <div class="col-md-6 m-auto">
                            <div class="row">
                                <div class="col">
                                        <div class="upperCard">
                                            <h1>Thông tin chuyển điểm</h1>
                                            <p>Thời gian đặt chuyển <br /> {{ \App\Elibs\Helper::showMongoDate(\App\Elibs\Helper::getMongoDateTime(), 'd/m/Y H:i:s') }}</p>
                                        </div>
                                        <div class="lowerCard">
                                            <button id="button" type="button" class="mb-2">Thông tin chi tiết đơn chuyển điểm</button>
                                            <ul>
                                                <li class="rHeading"><span>Tài khoản: <a href="javascript:void(0);">{{ \App\Http\Models\Member::getCurentAccount() }}</a></span>  <span></span></li>
                                                <li class="rHeading"><span>Họ tên chủ tài khoản: <a href="javascript:void(0);">{{ \App\Http\Models\Member::getCurrentFullName() }}</a></span>  <span></span></li>
                                                <li class="rHeading"><span>Giao dịch từ : <a href="javascript:void(0);" id="vi-thanh-toan-step2"></a> -> <a href="javascript:void(0);" id="vi-nhan-diem-step2"></a></span>  <span></span></li>
                                                <li class="rDescription">Tổng điểm giao dịch: <b class="text-danger" id="so-diem-giao-dich-step2"></b></li>
                                            </ul>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" id="btnInputForm" class="btn btn-info bg-teal-800 stepy-finish">Xác nhận giao dịch <i class="icon-check position-right"></i></button>
            </form>
        </div>
        <!-- /basic setup -->
    </div>

@stop
@push('JS_BOTTOM_REGION')

    <script type="text/javascript">
        MNG_POST.URL_ACTION = '/admin/chuyen-diem/';
        (function () {
            $('#btnInputForm').click(function () {
                return MNG_POST.save('#postInputForm');
            })
            var vithanhtoan = $('#vi-thanh-toan').val()
            var vinhandiem = $('#vi-nhan-diem').val()
            var sodiemgiaodich = $('#so-diem-giao-dich').val()

            if(vithanhtoan == 'OBJECT_VITICHLUY') {
                $('#vi-thanh-toan-step2').text('Ví tích lũy')
            }else if(vithanhtoan == 'OBJECT_VIHOAHONG') {
                $('#vi-thanh-toan-step2').text('Ví hoa hồng')
            }
            if(vinhandiem == 'OBJECT_VITIEUDUNG') {
                vinhandiem = 'Ví tiêu dùng'
            }else {
                vinhandiem = 'Ví không xác định'
            }
            $('#vi-nhan-diem-step2').text(vinhandiem)
            $('#so-diem-giao-dich-step2').text(sodiemgiaodich)
            $('#vi-thanh-toan').change(function () {
                var vithanhtoan = $(this).val()
                var vinhandiem = $('#vi-nhan-diem').val()
                if(vithanhtoan == 'OBJECT_VITICHLUY') {
                    $('#vi-thanh-toan-step2').text('Ví tích lũy')
                    $('#so-diem-giao-dich-hidden').attr('max', {{@$vitichluy['total_money']}})
                }else if(vithanhtoan == 'OBJECT_VIHOAHONG') {
                    $('#vi-thanh-toan-step2').text('Ví hoa hồng')
                    $('#so-diem-giao-dich-hidden').attr('max', {{@$vihoahong['total_money']}})
                }
                $('#vi-nhan-diem-step2').text(vinhandiem)
            })
            $('#so-diem-giao-dich').change(function () {
                sodiemgiaodich = $(this).val()
                $('#so-diem-giao-dich-hidden').val(sodiemgiaodich.toString().replace(/\D/g, ''))
                sodiemgiaodich = number_format(sodiemgiaodich)
                $('#so-diem-giao-dich-step2').text(sodiemgiaodich)
            })
        })();
    </script>
@endpush
