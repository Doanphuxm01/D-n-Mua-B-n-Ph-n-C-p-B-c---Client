@extends('frontend')

@section('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css') !!}
@endsection

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/magiczoom.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js') !!}
@endsection
    
@section('CONTENT_REGION')
<main class="page-product">
    <!-- <breadcrumb></breadcrumb> -->
    <div class="container-fluid">
        <div class="shadow bg-white">
            <div class="product-signle-detail mt-3 py-3 px-3 mb-5">
                <div class="row mb-4">
                    <div class="col-12 col-detail-left">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="big-img">
                                    @foreach(@$obj['images'] as $key => $item)
                                    <div class="item">
                                        <a href="{{ \App\Http\Models\Media::getImageSrc($item['path']) }}" data-fancybox="gallery" class="MagicZoom" data-options="zoomMode: magnifier;cssClass: mz-square">
                                        <img src="{{\App\Http\Models\Media::getImageSrc($item['path']) }} " alt=""></a>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="small-img">
                                    @foreach(@$obj['images'] as $key => $item)
                                    <div class="item">
                                        <img src="{{ \App\Http\Models\Media::getImageSrc($item['path']) }}"alt="">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" name="obj[_id]" value="{{ @$obj['_id'] }}">
                            <input type="hidden" name="obj[sku]" value="{{ @$obj['sku'] }}">
                            <input type="hidden" name="obj[amount]" value="{{ @$obj['amount']??0 }}">
                            <div class="col-12 col-md-6">
                                <div class="product-head">
                                    {{-- <div class="product-breadcrumb">
                                        <a href="#">
                                            minhphucgroup.com.vn
                                        </a>
                                        <a href="#">
                                            Sức Khỏe - Sắc Đẹp
                                        </a>
                                        <a href="#">
                                            Mỹ phẩm làm đẹp 
                                        </a>
                                    </div> --}}
                                    <h2 class="product-title">
                                       {{ @$obj['name'] }}
                                    </h2>
                                    <span class="p-model">
                                        Mã sản phẩm: <span> {{ @$obj['sku'] }}</span>
                                    </span>
                                </div>
                                <div class="single-price">
                                    <span class="title">Giá bán:</span>
                                    <span class="price">
                                        {{ \App\Elibs\Helper::formatMoney(@$obj['finalPrice']) }}
                                        {{-- {{ $obj['original_price'] }} --}}
                                    </span>
                                </div>
                                {{-- <ul class="list-desc">
                                    <li>
                                        <span>Giảm hình thành nếp nhăn</span>
                                    </li>
                                    <li>
                                        <span>Tẩy tế bào chết</span>
                                    </li>
                                    <li>
                                        <span>Da sáng và tươi trẻ</span>
                                    </li>
                                    <li>
                                        <span>Xóa mờ nám, tàn nhanh</span>
                                    </li>
                                    <li>
                                        <span>Nâng cơ và săn chắc da</span>
                                    </li>
                                </ul> --}}
                                <div class="add-to-cart">
                                    <div class="d-flex align-items-center" style="margin-bottom: 28px;">
                                        <div class="row">
                                            <span class="fs-18 col-12">Số lượng</span> <small class="text-danger col-12">(tối đa có thể mua: {{ @$obj['amount']??0 }})</small>
                                        </div>
                                        <div class="d-flex update-count mb-0">
                                            <a href="javascript:void(0);" class="minus">-</a> 
                                            <input  type="text" value="1" max="{{ @$obj['amount']??0 }}" name="count">
                                            <a href="javascript:void(0);" class="plus">+</a>
                                        </div>
                                    </div>
                                    <div class="btn-gr">
                                        <a @if(\App\Http\Models\Member::getCurent()) href="javascript:void(0);" id="them-vao-gio" @else href="{{ public_link('checkout') }}" @endif class="btn-add-cart btn-buy"><i class="icon icon-cart"></i> THÊM VÀO GIỎ HÀNG</a>
                                    </div>
                                </div>

                                {{-- sale ap-to Tuyết đẹp trai --}}
{{--                                <div class="group border-top">--}}
{{--                                    <div class="grid FrequentlyProducts__Wrapper-elrz6y-0 khSZLo">--}}
{{--                                        <h2 class="group-title">THƯỜNG ĐƯỢC MUA CÙNG</h2>--}}
{{--                                        <div class="images">--}}
{{--                                            @foreach($lsQuaTang as $item)--}}
{{--                                                <a href="javascript:;" title="{{ \App\Elibs\Helper::showContent(@$item['name']) }}" class="image ">--}}
{{--                                                    <div class="zoom-image" data-image="{{ \App\Http\Models\Media::getImageSrc(@$item['avatar_url']) }}">--}}
{{--                                                        <img src="{{ \App\Http\Models\Media::getImageSrc(@$item['avatar_url']) }}" alt="{{ \App\Elibs\Helper::showContent(@$item['name']) }}">--}}
{{--                                                    </div>--}}
{{--                                                </a>--}}
{{--                                                @if(!$loop->last)--}}
{{--                                                    <p class="plus">+</p>--}}
{{--                                                @endif--}}
{{--                                            @endforeach--}}
{{--                                        </div>--}}
{{--                                        <div class="frequently-products">--}}
{{--                                            @php($total = 0)--}}
{{--                                            @foreach($lsQuaTang as $item)--}}
{{--                                                @php($total += $item['finalPrice'])--}}
{{--                                                <div class="frequently-product-item">--}}
{{--                                                    <input id="isImage_tuyetDz" class="radio-custom myCheckbox" name="radio-group" disabled type="checkbox" checked>--}}
{{--                                                    <a href="javascript:;" class="frequently-product-name "><span><b>{{ \App\Elibs\Helper::showContent(@$item['name']) }}</b></span></a>--}}
{{--                                                    <p class="frequently-product-price ">{{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}</p>--}}
{{--                                                </div>--}}
{{--                                            @endforeach--}}
{{--                                        </div>--}}
{{--                                        <div class="frequently-add-to-cart">--}}
{{--                                            <p class="frequently-sum-price">Tổng tiền: <span>{{ \App\Elibs\Helper::formatMoney(@$total) }}</span></p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-detail-right">
                        <div class="content">
                            <div class="item d-flex align-items-center flex-column flex-sm-row">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_detail-right-1.png') }}" alt="">
                                <p>
                                    <b>GIAO HÀNG TẬN NƠI</b>
                                    Miễn phí vận chuyển với đơn hàng trên.
                                </p>
                            </div>
                            <div class="item d-flex align-items-center flex-column flex-sm-row">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_detail-right-2.png') }}" alt="">
                                <p>
                                    <b>THANH TOÁN KHI NHẬN HÀNG</b>
                                    Bạn nhận hàng & được kiểm tra hàng trước khi thanh toán.
                                </p>
                            </div>
                            <div class="item d-flex align-items-center flex-column flex-sm-row">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_detail-right-3.png') }}" alt="">
                                <p>
                                    <b>BẢO HÀNH CHÍNH HÃNG</b>
                                    Được bảo hành sản phẩm theo quy định.
                                </p>
                            </div>
                            <div class="item d-flex align-items-center flex-column flex-sm-row">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_detail-right-4.png') }}" alt="">
                                <p>
                                    <b>ĐẶT HÀNG ONLINE</b>
                                    <b class="text-danger">024.632.86686</b>
                                </p>
                            </div>
                            <b class="fs-18 text-dark">Bạn muốn mua giá sỉ?</b>
                            <a href="{{ public_link('auth/register') }}" class="btn-green">
                                ĐĂNG KÝ LÀM ĐẠI LÝ
                            </a>
                        </div>
                    </div>
                </div>
                {{-- <div class="detail-desc">
                    <div class="title">
                        <h4>
                            THÔNG TIN CHI TIẾT
                        </h4>
                    </div>
                    <div class="table-desc">
                        <ul>
                            <li>
                                <span class="desc-title">
                                    Thương hiệu
                                </span>
                                <span class="desc">
                                    SYN - AKE
                                </span>
                            </li>
                            <li>
                                <span class="desc-title">
                                    Xuất xứ thương hiệu
                                </span>
                                <span class="desc">
                                    Thái Lan
                                </span>
                            </li>
                            <li>
                                <span class="desc-title">
                                    Sản xuất
                                </span>
                                <span class="desc">
                                    Thái Lan
                                </span>
                            </li>
                            <li>
                                <span class="desc-title">
                                    Kích thước
                                </span>
                                <span class="desc">
                                    9 x 9 x 5 cm
                                </span>
                            </li>
                        </ul>
                    </div>
                </div> --}}
                <div class="detail-desc">
                    <div class="title">
                        <h4>
                            MÔ TẢ SẢN PHẨM
                        </h4>
                    </div>
                    <div class="content">
                        <div class="mf-2">
                            {!! (@$obj['content']) ? @$obj['content'] : ' - <b>Hiện tại chúng tôi đang cập nhập mô tả cho sản phẩm này</b>' !!}
                        </div>
                        {{-- <img src="{{ url('mpg-tmp/giaodienmuahang/images/content-demo.png') }}" alt=""> --}}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Todo_Blade --}}
    <div class="container-fluid mt-5 mb-3">
        @if(count($goiYDanhChoBan) > 0)
        <!-- product sugget -->
            <section class="section-product-sugget">
                <div class="site-title d-flex flex-wrap align-items-center">
                    <a href="#">
                        <h4>
                            <i class="icon icon-star"></i>
                            GỢI Ý DÀNH RIÊNG CHO BẠN
                        </h4>
                    </a>
                    <div class="d-flex align-items-center col px-0">
                        <a href="#" class="view-all ml-auto">

                        </a>
                    </div>
                </div>
                <div class="wrap-sugget">
                    <div class="sugget-product-slider owl-carousel owl-theme">
                        @foreach (@$goiYDanhChoBan as $s => $item)
                            <div class="item">
                                <div class="product-sale-item">
                                    <a href="{{ route('FeProductDetail', ['alias' => @$item['alias'], 'id' => @$item['_id']]) }}" class="img">
                                        <img src="{{\App\Http\Models\Media::getImageSrc($item['avatar_url'])}}" alt="{{ @$item['name'] }}">
                                        @if(@$item['finalPrice'] < @$item['regularPrice'])
                                            <span class="sale-icon">
                                                        -{{ \App\Elibs\Helper::calcDiscount(@$item['finalPrice'], @$item['regularPrice']) }}%
                                                    </span>
                                        @endif
                                    </a>
                                    <span style="color: white" class="price">{{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}</span>
                                    <div class="process-bar-sale">
                                        <span class="process" style="width: 85%"></span>
                                        {{--<span class="text">Đã bán 500</span>--}}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </section>
            <!-- end product sugget -->
        @endif
    </div>
    {{-- <div class="container-fluid mt-5 mb-3">
        <div class="site-title d-flex flex-wrap align-items-center">
            <a href="#">
                <h4>
                    <i class="icon icon-star"></i>
                    SẢN PHẨM LIÊN QUAN
                </h4>
            </a>
            <div class="d-flex align-items-center col px-0">
                <a href="#" class="view-all ml-auto">
                    Xem tất cả >>
                </a>
            </div>
        </div>
    </div>
    <div class="mb-4">
        <div class="container-fluid page-product-list">
            <div class="row">
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="container-fluid mt-5 mb-3">
        <div class="site-title d-flex flex-wrap align-items-center">
            <a href="#">
                <h4>
                    <i class="icon icon-star"></i>
                    SẢN PHẨM ĐÃ XEM
                </h4>
            </a>
            <div class="d-flex align-items-center col px-0">
                <a href="#" class="view-all ml-auto">
                    Xem tất cả >>
                </a>
            </div>
        </div>
    </div>
    <div class="bg-white mb-4">
        <div class="container-fluid product-list">
            <div class="row">
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-3 col-xl-25">
                    <div class="product-item">
                        <a href="#" class="img w-100">
                            <img src="{{ url('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                            <span class="sale-icon">
                                17%
                            </span>
                        </a>
                        <a href="" class="name">Nước rửa tay thảo dược Amy</a>
                        <span class="price">Giá bán: 50,000đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</main>
@stop
@section('JS_BOTTOM_REGION')
    <script type="text/javascript">
        $('[data-fancybox="gallery"]').fancybox({});
        if (window.matchMedia && window.matchMedia('(max-width: 979px)').matches && MagicZoom !== undefined) {
            MagicZoom.options['zoom-position'] = 'inner';
            MagicZoom.refresh();
        } else if (window.matchMedia && window.matchMedia('(min-width: 980px)').matches && MagicZoom !== undefined) {
            MagicZoom.options['zoom-position'] = 'right';
            MagicZoom.options = {
                'zoom-width': 380,
                'zoom-height': 380
            }
            MagicZoom.refresh();
        }
        $(window).bind('load', function(){
            $('#menu').mmenu();

        });
        // hove image
        $(function(){
            $('.myCheckbox').prop('checked', true);
            var isImage_tuyetDz = $('#isImage_tuyetDz').is(":checked");
            if(isImage_tuyetDz == false){
                console.log('dz');
                $("#isImage_tuyetDz").addClass("disable");
            }
        })
    </script>
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/cart/pdetail.js') !!}
@endsection