@extends('backend_gate')
{{-- @section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/nestable/jquery.nestable.min.js') !!}
@stop --}}
@section('CONTENT_REGION')
    @if(!isset($allCity))
        @php
            $allCity = collect(\App\Http\Models\Location::getAllCity());
        @endphp
    @endif
    @php($allBankDataList = \App\Http\Models\MetaData::getAllByType(\App\Http\Models\MetaData::STAFF_NGAN_HANG))
    @php($allLienHeDataList = \App\Http\Models\MetaData::getAllByType(\App\Http\Models\MetaData::STAFF_LIEN_HE_KHAC))
    <div class="login-container" style="position: relative">

        <form method="post" id="xaHoiNayChiCoDangKyThiMoiLamTV" onsubmit="alertChuyenTien()">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            @if(isset($_MSG) && $_MSG)
                {!! $_MSG !!}
            @endif
            <div class="panel panel-body login-form" id="obj_tk_ngan_hang_2">
                <div class="text-center">
                    <div class="icon-object text-slate-300" style="border-width: inherit;">
                        <a href="{{ public_link('/') }}">
                            <img src="{{asset('/images/logo.png')}}" style="height: auto;position: relative;width: 170px;left: 9px;">
                        </a>
                    </div>
                    <h5 class="content-group">Đăng ký tài khoản của bạn
                        <small class="display-block">Nhập tài khoản và mật khẩu vào ô phía dưới</small>
                    </h5>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[fullname]" value="{{isset($obj['fullname'])?$obj['fullname']:null}}" type="text" class="form-control" placeholder="Họ và tên">
                    <div class="form-control-feedback">
                        <i class="glyphicon glyphicon-user text-muted"></i>
                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <div class="popup-main bg-white rounded-8 shadow">
                        <div class="group-khu-vuc">
                            <div class="form-group">
                                <select style="width: 100%;height: 35px" id="location-city"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-district','Chọn quận huyện')"
                                        class="select-search select-md city" name="obj[city]">
                                    <option value="">Chọn tỉnh thành</option>
                                    @foreach($allCity as $key=>$value)
                                        <option @if(isset($obj['city']) && @$obj['city']==$value->slug) selected
                                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="form-group">
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-town','Chọn xã phường', '{{@$obj['town']}}')"
                                        id="location-district" class="district select-search"
                                        name="obj[district]">
                                    <option value="">Chọn quận huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select style="width: 100%;height: 35px"
                                        id="location-town" class="town select-search"
                                        name="obj[town]">
                                    <option value="">Chọn xã phường</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <input class="form-control" type="text" value="{{ @$obj['street'] }}" name="obj[street]" placeholder="Địa chỉ">
                    <div class="form-control-feedback">
                        <i class="glyphicon glyphicon-map-marker text-muted"></i>
                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[phone]" value="{{isset($obj['phone'])?$obj['phone']:null}}" type="text" class="form-control" placeholder="Số điện thoại">
                    <div class="form-control-feedback">
                        <i class="glyphicon glyphicon-earphone text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[email]" value="{{isset($obj['email'])?$obj['email']:null}}" type="text" autocomplete="false" class="form-control" placeholder="Địa chỉ email">
                    <div class="form-control-feedback">
                        <i class="glyphicon glyphicon-list-alt text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[can_cuoc_cong_dan]" value="{{isset($obj['can_cuoc_cong_dan'])?$obj['can_cuoc_cong_dan']:null}}" type="text" class="form-control" placeholder="Căn cước công dân">
                    <div class="form-control-feedback">
                        <i class="icon-credit-card text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[account]" value="{{isset($obj['account'])?$obj['account']:null}}" type="text" class="form-control" placeholder="Tài khoản đăng nhập">
                    <div class="form-control-feedback">
                        <i class="icon-users2 text-muted"></i>
                    </div>
                </div>

                    <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[password]" value="{{isset($obj['password'])?$obj['password']:null}}" type="password" class="form-control" placeholder="Mật khẩu đăng nhập">
                    <div class="form-control-feedback">
                        <i class="icon-key text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[ma_gioi_thieu]" type="text" {{ isset($_GET['ma_gioi_thieu'])?'readonly':'null' }} value="{{isset($obj['ma_gioi_thieu']) || isset($_GET['ma_gioi_thieu'])?$obj['ma_gioi_thieu']??$_GET['ma_gioi_thieu']:null}}" class="form-control" placeholder="Mã giới thiệu">
                    <div class="form-control-feedback">
                        <i class="icon-puzzle3 text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[so_diem_mua]" type="text" class="form-control so_diem_mua input-type-number" min="500000" placeholder="Số điểm MPG muốn mua">
                    <small class="text-danger" style="font-size: 11px">Số điểm tối thiểu mua là 500.000 MPG ~ 500.000 đ</small>
                    <div class="form-control-feedback">
                        <i class="icon-coin-yen text-muted"></i>
                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <div id="obj_tk_ngan_hang">
                            <div class="form-group del">
                                    <div class="form-group">
                                            <select name="obj[tk_ngan_hang][_id]" class="select-search select-md">
                                                    <option value="">Chọn ngân hàng</option>
                                                @foreach($allBankDataList as $n=> $val)
                                                    <option value="{{$val['_id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control input-type-number"
                                               name="obj[tk_ngan_hang][so]"
                                               placeholder="Nhập số tài khoản">
                                    </div>
                            </div>
                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left" id="dan_an_quyt"></div>

                {{--<div class="form-group has-feedback has-feedback-left">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="obj[ae_ho_hang]" id="ae_ho_hang" value="true">
                        <label class="form-check-label" for="ae_ho_hang">
                         Kích hoạt bằng thành viên khác
                        </label>
                    </div>
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <input type="text" readonly name="obj[ma_tai_khoan_nhan_kich_hoat]" id="ma-tai-khoan-nhan-kich-hoat" placeholder="Mã tài khoản nhận tiền" class="form-control">
                    <div class="form-control-feedback">
                        <i class="icon-coin-yen text-muted"></i>
                    </div>
                </div>--}}
                <div class="form-group has-feedback has-feedback-left" id="dan_an_quyt">
                    <div class="form-check">
                        <input class="form-check-input" checked type="checkbox" name="obj[chac_chan_luon]" id="chac_chan_luon" value="true">
                        <label class="form-check-label" for="thich_lam_sieuThi">
                         Tôi đồng ý với các điều khoản của công ty.
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" disabled class="btn btn-primary btn-block" id="dang-ky" >Đăng ký <i class="icon-circle-right2 position-right"></i></button>
                </div>

                <div class="text-center">
                    <a href="{{ admin_link('') }}">Đăng nhập</a>
                </div>
{{--                <div class="panel panel-white">--}}
{{--                    <table class="table table-responsive">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th width="300">Tài khoản ngân hàng</th>--}}
{{--                            <th width="200">Số</th>--}}
{{--                            <th width="1" class="text-right"><a onclick="clone_tk_ngan_hang()"--}}
{{--                                                                class="btn btn-link text-primary">--}}
{{--                                    <i class="icon-add-to-list"></i></a></th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody id="obj_tk_ngan_hang">--}}
{{--                        @isset($obj['tk_ngan_hang'])--}}
{{--                            @foreach(@$obj['tk_ngan_hang'] as $key=>$value)--}}
{{--                                <tr>--}}
{{--                                    <td>--}}
{{--                                        <select name="obj[tk_ngan_hang][{{$key}}][id]" class="select-search select-md"--}}
{{--                                        >--}}
{{--                                            <option value="">Chưa lựa chọn</option>--}}
{{--                                            @foreach($allBankDataList as $val)--}}
{{--                                                <option @if(isset($value['id'])  && $value['id']==$val['_id']))--}}
{{--                                                        selected--}}
{{--                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>--}}

{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <input type="text" class="form-control input-type-number"--}}
{{--                                               name="obj[tk_ngan_hang][{{$key}}][so]"--}}
{{--                                               value="{{@$value['so']}}"--}}
{{--                                               placeholder="Nhập số">--}}

{{--                                    </td>--}}
{{--                                    <td><i class="icon-trash text-danger"--}}
{{--                                           onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}

{{--                            @endforeach--}}
{{--                        @endisset--}}

{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
            </div>
        </form>
        <!-- /simple login form -->
    </div>
    {{--<!-- Footer -->
    <div class="footer text-muted">
        &copy; {{date('Y')}}. <a href="/">Ứng dụng quản lý hệ thống</a> by <a href="{{ admin_link('/') }}">{{ config('app.cms_name') }}</a>
    </div>
    <!-- /footer -->--}}

@stop
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/app.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/interactions.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/loaders/blockui.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/bootstrap.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/loaders/pace.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/bootstrap_select.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_bootstrap_select.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('texo.js') !!}

    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!} --}}
    {{-- {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/nestable/jquery.nestable.min.js') !!} --}}
    <script type="text/javascript">
        var sodiemtoithieu = '{{ $min_mpg??0 }}';
        var is_ctv = '{{ $is_ctv??0 }}';
        var is_daily = '{{ $is_daily??0 }}';
        var is_mpmart = '{{ $is_mpmart??0 }}';
        var sodiemtoithieudaily = '{{ $min_dai_ly??0 }}';
        var sodiemtoithieumpmart = '{{ $min_mpmart??0 }}';
        var everychietkhautichluyctv = '{{ $everychietkhautichluyctv??0 }}';
        var everychietkhautieudungctv = '{{ $everychietkhautieudungctv??0 }}';
        var everychietkhautichluympmart = '{{ $everychietkhautichluympmart??0 }}';
        var everychietkhautieudungmpmart = '{{ $everychietkhautieudungmpmart??0 }}';
        var chuc_danh = '{{ $chuc_danh }}';
        $(function (){
            if($("#ae_ho_hang").is(':checked'))
                $("#txtAge").show();  // checked
            else
                $("#txtAge").hide();  // unchecked
            $('.copiclz').click(function () {
                var copyText = $(this);
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(copyText.html()).select();
                document.execCommand("copy");
                $temp.remove();
                alert("Copied the text: " + copyText.text());
            })
            $('input[name="obj[account]"]').on('keyup change',function () {
                $('#ma-tai-khoan-nhan-kich-hoat').val($(this).val()+Math.floor(Math.random() * 100000) + 1)
            })
            $('.so_diem_mua').on('keyup change',function () {

                var $cocaidaubuoi = $('#dan_an_quyt');
                var so_diem_mua = parseInt($('.so_diem_mua').val());
                $cocaidaubuoi.empty();
                if(so_diem_mua >= sodiemtoithieu) {
                    $('#dang-ky').removeAttr('disabled');
                }else {
                    $('#dang-ky').attr('disabled', true);
                }
                if(chuc_danh == is_ctv) {
                    var html = '';
                    html +=
                        '                    <div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_ctv]" id="del_thic_no" value="'+everychietkhautieudungctv+'" checked>\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            '+formatPercent(everychietkhautieudungctv)+' số dư/ngày từ ví chiết khấu -> tiêu dùng\n' +
                        '                        </label>\n' +
                        '                    </div>\n';
                }
                if(so_diem_mua >= sodiemtoithieudaily || chuc_danh == is_daily) {
                    var html = '';
                    html += '<div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[co_no_hay_khong]" id="del_thic_no" value="no" checked>\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            Không nợ\n' +
                        '                        </label>\n' +
                        '                    </div>\n' +
                        '                    <div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[co_no_hay_khong]" id="no_vailz" value="yes">\n' +
                        '                        <label class="form-check-label" for="no_vailz">\n' +
                        '                            Có nợ\n' +
                        '                        </label>\n' +
                        '                    </div>';
                    $('input[name="obj[co_no_hay_khong]"]').change(function () {
                        if($('#thich_lam_sieuThi').is(':checked') && parseInt($('.so_diem_mua').val()) < sodiemtoithieumpmart) {
                            alert("Bạn cần mua từ 300.000.000 MPG trở lên để sử dụng MP Mart");
                            $('#del_thic_no').prop('checked', true);
                        }
                    })
                }
                if(so_diem_mua >= sodiemtoithieumpmart ||chuc_danh == is_mpmart) {
                    var html = '';
                    html += '<div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_mpmart]" id="no_vailz" value="'+everychietkhautichluympmart+'">\n' +
                        '                        <label class="form-check-label" for="no_vailz">\n' +
                        '                            '+formatPercent(everychietkhautichluympmart)+' số dư/ngày từ ví chiết khấu -> tích luỹ\n' +
                        '                        </label>\n' +
                        '                    </div>\n' +
                        '                    <div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_mpmart]" id="del_thic_no" value="'+everychietkhautieudungctv+'" checked>\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            '+formatPercent(everychietkhautieudungctv)+' số dư/ngày từ ví chiết khấu -> tiêu dùng\n' +
                        '                        </label>\n' +
                        '                    </div>\n';
                }
                $cocaidaubuoi.append(html)

            })
            APPLICATION._changeCity(jQuery('#location-city').val(),'#location-district','Chọn quận huyện', '{{@$obj['district']}}')
        });
        function alertifySoTienCanChuyen() {
            alertify.confirm().set('message', 'This is a new message!').show();
        }
        function clone_tk_ngan_hang() {
            let index = $('#obj_tk_ngan_hang') && $('.del').length;
            let temp_select_class = "select-search-" + Number(new Date());
            
            let tmp2 = `
                        <div class="form-group del">
                                    <div class="form-group ">
                                            <select name="obj[tk_ngan_hang][${index}][id]" class="bootstrap-select">
                                                <option value="">Chọn ngân hàng</option>
                                                @foreach($allBankDataList as $val)
                                                    <option value="{{$val['_id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control input-type-number" name="obj[tk_ngan_hang][${index}][so]"placeholder="Nhập số">
                                    </div>
                                    <div class="col-md-12">
                                        <a title="thêm tài khoản ngân hàng" onclick="clone_tk_ngan_hang()"class="label label-flat border-primary text-primary-600">thêm tài khoản</a>
                                        <a title="thêm tài khoản ngân hàng" onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('.del').remove()"class="label label-flat border-danger text-primary-600">Xóa</a>
                                    </div>
                        </div>
                        `;
                        
            $('#obj_tk_ngan_hang').append(tmp2);
            // DATE_PICKER_INIT();
            // INPUT_NUMBER();
            // $(`.${temp_select_class}`).select2();
        }
        function clone_tk_ngan_hang2() {
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
    </script>
@stop