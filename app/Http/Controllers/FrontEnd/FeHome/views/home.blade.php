@extends('frontend')

@section('CONTENT_REGION')
    <style>
        .section-product-sugget {
            background-color: #fff;
        }
        .product-sugget:hover .name {
            color: #007c39;
        }
        .product-sugget .name {
            color: #000;
        }
    </style>
    {{-- {!! $itemistCate !!} --}}
        <main>
            <div class="container-fluid">
                <!-- banner top -->
                <section class="banner-home d-md-flex">
                    <div class="banner-center col p-0">
                        <div class="banner-item">
                            <a href="#">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/mpg_banner/Untitled-2.jpg') }}" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="banner-right d-flex flex-column flex-sm-row flex-md-column">
                        <div class="banner-item">
                            <a href="#">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/mpg_banner/IMG_20200806_010544.jpg') }}" alt="">
                            </a>
                        </div>
                        <div class="banner-item">
                            <a href="#">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/mpg_banner/IMG_20200806_010549.jpg') }}" alt="">
                            </a>
                        </div>
                        <div class="banner-item">
                            <a href="#">
                                <img src="{{ url('mpg-tmp/giaodienmuahang/images/mpg_banner/IMG_20200806_010551.jpg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </section>
                <!-- end banner top -->

                <!-- banner adv -->
                <section class="banner-content text-center">
                    <a href="#">
                        <img src="{{ url('mpg-tmp/giaodienmuahang/images/mpg_banner/Untitled-1.jpg') }}" alt="">
                    </a>
                </section>
                <!-- end banner adv -->
                
                <!-- product sale -->
                <section class="section-product-sale">
                    <div class="sale-title d-flex flex-wrap align-items-center">
                        <h4>BIG SALE</h4>
                        <div class="d-flex align-items-center col p-0">
                            {{--<div class="time-down text-nowrap">
                                <span>05</span>
                                <i>:</i>
                                <span>00</span>
                                <i>:</i>
                                <span>00</span>
                            </div>--}}
                            <a href="#" class="view-all ml-auto">
                                
                            </a>
                        </div>
                    </div>
                    <div class="wrap-product-slider">
                        <div class="product-sale-slider owl-carousel owl-theme">
                            @if(!empty($bigSale))
                                @foreach (@$bigSale as $s => $item)
{{--                                    {{dd($item)}}--}}
                                    <div class="item">
                                        <div class="product-sale-item">
                                            <a href="{{ public_link(@$item['alias'].'-p'.@$item['_id'].'.html') }}" class="img">
                                                <img src="{{\App\Http\Models\Media::getImageSrc($item['avatar_url'])}}" alt="{{ @$item['name'] }}">
                                                @if(@$item['finalPrice'] < @$item['regularPrice'])
                                                <span class="sale-icon">
                                                    -{{ \App\Elibs\Helper::calcDiscount(@$item['finalPrice'], @$item['regularPrice']) }}%
                                                </span>
                                                @endif
                                            </a>
                                            <span class="price">{{ \App\Elibs\Helper::formatMoney(@$item['regularPrice']) }}</span>
                                            <div class="process-bar-sale">
                                                <span class="process" style="width: 85%"></span>
                                                {{--<span class="text">Đã bán 500</span>--}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </section>
                <!-- end product sale -->

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
                                                <span class="price">{{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}</span>
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

                <!-- product feature -->
                @foreach($lsObj['groupAndCountByCategory'] as $key => $cate)
                <section class="section-product-feature">
                    <div class="site-title d-flex flex-wrap align-items-center">
                        <a href="{{ route('FeProductCate', ['alias' => @$cate['alias']]) }}">
                            <h4>
                                <i class="icon icon-star"></i>
                                {{  @$cate['label'] }}
                            </h4>
                        </a>
                        <div class="d-flex align-items-center col px-0">
                            <a href="{{ route('FeProductCate', ['alias' => @$cate['alias']]) }}" class="view-all ml-auto">
                                Xem tất cả
                            </a>
                        </div>
                    </div>
                    <div class="product-list">
                        <div class="row">
                            {{-- @include('site-mpg.components.products.list-items', ['lsObj' => $sanPhamNoiBat, 'column' => 'col-6 col-sm-4 col-lg-3']) --}}
                            @include('site-mpg.components.products.list-items', ['lsObj' => @$cate['products'], 'column' => 'col-6 col-sm-4 col-lg-3'])
                        </div>

                        {{-- {{ $lsObj['groupAndCountByCategory']->links('site-mpg.pagination') }} --}}
                    </div>
                </section>
                @endforeach
                
                
                <section class="subscribe">
                    <div class="row align-items-end">
                        <div class="col-12 col-lg-5 d-flex align-items-lg-end subscribe-left flex-column align-items-center flex-lg-row">
                            <div class="image">
                                {{-- <img src="{{ url('mpg-tmp/giaodienmuahang/images/icon-email.png') }}" alt=""> --}}
                                <img class="logoButton" src="{{ url('mpg-tmp/giaodienmuahang/images/logo.png') }}" alt="">
                            </div>
                            <div class="text-subscribe">
                                <h4>
                                    Hệ thống bán hàng
                                </h4>
                                <p>
                                    Đừng bỏ lỡ hàng ngàn sản phẩm và chương trình siêu hấp dẫn
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 subscribe-right">
                            <h4>
                                ĐĂNG KÝ NHẬN TIN KHUYẾN MÃI
                            </h4>
                            <form action="" class="d-flex flex-column flex-sm-row">
                                <input type="text" placeholder="Nhập Email của bạn để đăng ký">
                                <button>ĐĂNG KÝ</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>

        </main>

@stop

@section('JS_REGION')
<script>
    $(function() {
        $('.js-show-menu-active').addClass('isHome');
    });

</script>
<!-- Contact Area End -->
@stop