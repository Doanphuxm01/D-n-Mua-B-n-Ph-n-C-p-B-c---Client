@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Mua MPG </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('mua-diem')}}">Mua MPG</a></li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <form name="postInputForm" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="w-100 d-flex form-horizontal " method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:0}}"/>
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Thông tin giao dịch</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số điểm MPG muốn mua','key'=>'so_diem_mua', 'class' => 'so_diem_mua input-type-number'],
                                    'note'=>['label'=>'* Số điểm tối thiểu mua là 50.000 MPG ~ 50.000 vnđ','class'=>'text-danger']])
                                <small class="text-danger" style="font-size: 11px"></small>
                                <div class="form-group has-feedback has-feedback-left" id="dan_an_quyt">

                                </div>

                            </div>



                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($obj,'customer')}}','{{$obj['_id']}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>

                            @endif
                            <button type="button" id="dang-ky" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Mua điểm
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

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
        MNG_POST.URL_ACTION = '/admin/mua-diem/';
        $(function (){
            $('#dang-ky').click(function () {
                var so_diem_mua = parseInt($('.so_diem_mua').val());
                return MNG_POST.save('#postInputForm');
                if(so_diem_mua >= sodiemtoithieu) {
                    return MNG_POST.save('#postInputForm');
                }
                return alert('Số điểm tối thiểu mua là 50.000 MPG ~ 50.000 vnđ');
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
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_ctv]" checked id="del_thic_no" value="'+everychietkhautieudungctv+'">\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            '+formatPercent(everychietkhautieudungctv)+' số dư/ngày từ ví chiết khấu -> tiêu dùng\n' +
                        '                        </label>\n' +
                        '                    </div>\n';
                }
                if(so_diem_mua >= sodiemtoithieudaily || chuc_danh == is_daily) {
                    var html = '';
                    html += '<div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[co_no_hay_khong]" id="no_vailz" value="yes">\n' +
                        '                        <label class="form-check-label" for="no_vailz">\n' +
                        '                            Có nợ\n' +
                        '                        </label>\n' +
                        '                    </div>\n' +
                        '                    <div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[co_no_hay_khong]" id="del_thic_no" value="no" checked>\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            Không nợ\n' +
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
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_mpmart]" id="no_vailz" value="'+everychietkhautichluympmart+'" checked>\n' +
                        '                        <label class="form-check-label" for="no_vailz">\n' +
                        '                            '+formatPercent(everychietkhautichluympmart)+' số dư/ngày từ ví chiết khấu -> tích luỹ\n' +
                        '                        </label>\n' +
                        '                    </div>\n' +
                        '                    <div class="form-check">\n' +
                        '                        <input class="form-check-input styled" type="radio" name="obj[everyday_percent_mpmart]" id="del_thic_no" value="'+everychietkhautieudungctv+'">\n' +
                        '                        <label class="form-check-label" for="del_thic_no">\n' +
                        '                            '+formatPercent(everychietkhautieudungctv)+' số dư/ngày từ ví chiết khấu -> tiêu dùng\n' +
                        '                        </label>\n' +
                        '                    </div>\n';
                }
                $cocaidaubuoi.append(html)

            })

        });
        function alertifySoTienCanChuyen() {
            alertify.confirm().set('message', 'This is a new message!').show();
        }
    </script>
    <script type="text/javascript">
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });
        jQuery.ajaxSetup({
                beforeSend: function(){
                    ajaxindicatorstart('Loading...');
                },
                complete: function(){
                    jQuery('#resultLoading .bg').height('100%');
                    jQuery('#resultLoading').fadeOut(300);
                    jQuery('body').css('cursor', 'default');
                }
            })
            function ajaxindicatorstart(text){
                if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
                    jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="{{ url("images/loading.gif") }}"><div>'+text+'</div></div><div class="bg"></div></div>');
                }
                jQuery('#resultLoading').css({
                    'width':'100%',
                    'height':'100%',
                    'position':'fixed',
                    'z-index':'10000000',
                    'top':'0',
                    'left':'0',
                    'right':'0',
                    'bottom':'0',
                    'margin':'auto'
                });
    
                jQuery('#resultLoading .bg').css({
                    'background':'#000000',
                    'opacity':'0.7',
                    'width':'100%',
                    'height':'100%',
                    'position':'absolute',
                    'top':'0'
                });
    
                jQuery('#resultLoading>div:first').css({
                    'width': '250px',
                    'height':'75px',
                    'text-align': 'center',
                    'position': 'fixed',
                    'top':'0',
                    'left':'0',
                    'right':'0',
                    'bottom':'0',
                    'margin':'auto',
                    'font-size':'16px',
                    'z-index':'10',
                    'color':'#ffffff'
    
                });
                jQuery('#resultLoading .bg').height('100%');
                jQuery('#resultLoading').fadeIn(300);
                jQuery('body').css('cursor', 'wait');
            }
            function notify(message, type){
                $.notify({
                    message: message
                }, {
                    type: type
                });
            }
    </script>
@stop
