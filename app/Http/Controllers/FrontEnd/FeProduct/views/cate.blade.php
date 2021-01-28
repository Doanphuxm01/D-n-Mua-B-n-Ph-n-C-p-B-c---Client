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
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-2 sidebar-left mb-3">
                {{-- <div class="sidebar-product">
                    <div class="box-filter">
                        <a href="javacript:;" class="d-flex title-box">
                            <h4>BỘ LỌC SẢN PHẨM </h4>
                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                        </a>
                        <div class="filter-checkbox">
                            <label for="" class="d-block">
                                <input type="checkbox">
                                Mua nhiều giảm giá
                            </label>
                            <label for="" class="d-block">
                                <input type="checkbox">
                                Khuyến mại
                            </label>
                            <label for="" class="d-block">
                                <input type="checkbox">
                                Chuyển phát hỏa tốc
                            </label>
                            <label for="" class="d-block">
                                <input type="checkbox">
                                Chiết khấu đại lý
                            </label>
                        </div>
                    </div>
                    <div class="box-filter">
                        <a href="javacript:;" class="d-flex title-box">
                            <h4>ĐÁNH GIÁ</h4>
                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                        </a>
                        <div class="filter-star">
                            <a href="#" class="d-flex align-items-start">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/star_4.png') }}" alt="">
                                <span>(ít nhất 4 sao)</span>
                            </a>
                            <a href="#" class="d-flex align-items-start">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/star_3.png') }}" alt="">
                                <span>(ít nhất 3 sao)</span>
                            </a>
                            <a href="#" class="d-flex align-items-start">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/star_2.png') }}" alt="">
                                <span>(ít nhất 2 sao)</span>
                            </a>
                            <a href="#" class="d-flex align-items-start">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/star_1.png') }}" alt="">
                                <span>(ít nhất 1 sao)</span>
                            </a>
                        </div>
                    </div>
                    <div class="box-filter">
                        <a href="javacript:;" class="d-flex title-box">
                            <h4>CHỌN THEO KHOẢNG GIÁ</h4>
                        </a>
                        <div class="filter-price">
                            <input type="text" placeholder="Thấp nhất">
                            <span class="mx-2">-</span>
                            <input type="text" placeholder="Cao nhất">
                            <button>
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/ic_arrow-right.png') }}" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="box-filter">
                        <a href="javacript:;" class="d-flex title-box">
                            <h4>NHÀ CUNG CẤP</h4>
                        </a>
                        <div class="list-link">
                            <a href="#">Tabeaute</a>
                            <a href="#">Alaska</a>
                            <a href="#">Phương Thảo</a>
                            <a href="#">Vinalink</a>
                            <a href="#">Atomy</a>
                            <a href="#">Hispa</a>
                            <a href="#">MT cup</a>
                            <a href="#">Jyla’s Herb</a>
                            <a href="#">Varis Luxury</a>
                            <a href="#">Koya</a>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="banner-sidebar d-flex flex-row flex-lg-column">
                    <a href="{{ public_link('/') }}" class="banner">
                        <img src="{{ asset('mpg-tmp/giaodienmuahang/images/banner-sidebar-1.png') }}" alt="">
                    </a>
                    <a href="{{ public_link('/') }}" class="banner">
                        <img src="{{ asset('mpg-tmp/giaodienmuahang/images/banner-sidebar-2.png') }}" alt="">
                    </a>
                    <a href="{{ public_link('/') }}" class="banner">
                        <img src="{{ asset('mpg-tmp/giaodienmuahang/images/banner-sidebar-1.png') }}" alt="">
                    </a>
                </div> --}}
            </div> 
            {{-- <div class="col-12 col-lg-8 col-xl-10 product-main"> --}}
            <div class="col-12 col-lg-12 col-xl-12">
                <div class="product-head">
                    <div class="product-breadcrumb">
                        <a href="{{ public_link('/') }}">
                            Trang chủ
                        </a>
                        <a href="#">
                            {{ @$curCate['name'] }}
                        </a>
                    </div>
                    <h2 class="product-title">
                        {{ @$curCate['name'] }} <span>(<b>{{ $lsObj->total() }}</b> sản phẩm)</span>
                    </h2>
                </div>
                {{-- <div class="slider-banner">
                    <div class="slider-for">
                        <div class="item">
                            <a href="#">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/banner-product-slider.png') }}" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ff0" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ffc" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ccf" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ff0" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ffc" alt="">
                            </a>
                        </div>
                        <div class="item">
                            <a href="#">
                                <img src="https://via.placeholder.com/1388x466/ccf" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="slider-nav">
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-content d-flex">
                                <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
                                <span>Mỹ phẩm</span>
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="short-box d-flex flex-column flex-md-row">
                    <div class="sort-link">
                        <span class="title">Ưu tiên xem:</span>
                        <div class="d-none d-lg-inline-block">
                            <a href="#" class="short-item">HÀNG MỚI</a>
                            <a href="#" class="short-item">BÁN CHẠY</a>
                            <a href="#" class="short-item">GIẢM GIÁ NHIỀU</a>
                            <a href="#" class="short-item">GIÁ THẤP</a>
                            <a href="#" class="short-item">GIÁ CAO</a>
                            <a href="#" class="short-item">CHỌN LỌC</a>
                        </div>
                        <select class="d-inline-block d-lg-none px-3 py-2">
                            <option value="">HÀNG MỚI</option>
                            <option value="">BÁN CHẠY</option>
                            <option value="">GIẢM GIÁ NHIỀU</option>
                            <option value="">GIÁ THẤP</option>
                            <option value="">GIÁ CAO</option>
                            <option value="">CHỌN LỌC</option>
                        </select>
                    </div>
                    <div class="search ml-0 ml-md-auto">
                        <form action="" class="d-flex">
                            <input class="" type="text" placeholder="Tìm trong Làm đẹp - Sức khỏe"> 
                            <button>
                                <i class="icon icon-search"></i>
                            </button>
                        </form>
                    </div>
                </div> --}}
                <div class="page-product-list product-list mt-3">
                    <div class="row">
                        @if(!empty($lsObj))
                        @foreach (@$lsObj as $key => $item)
                            <div class="col-6 col-sm-4 col-lg-3">
                                <div class="product-item" >
                                    <a href="{{ public_link(@$item['alias'].'-p'.@$item['_id'].'.html') }}" class="img">
                                        <img src="{{\App\Http\Models\Media::getImageSrc($item['avatar_url'])}}" alt="{{ @$item['name'] }}">
                                        
                                    </a>
                                    <a href="{{ public_link(@$item['alias'].'-p'.$item['_id'].'.html') }}" class="name">{{ @$item['name'] }}</a>
                                    <span class="price">Giá bán: {{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}</span>
                                    
                                    <div class="d-flex">
                                        <div class="rate">
                                            <span class="rate-star">
                                                <span class="star star5"></span>
                                            </span>
                                            <span class="count-rate">(1500)</span>
                                        </div>
                                        <span class="tag-count">
                                            <i class="icon icon-tag"></i>
                                            2.8k
                                        </span>
                                    </div>
                                    <span class="uy-tin fs-11">
                                        <i class="icon icon-genuine"></i>
                                        <b class="text-theme1">UY TÍN - </b>MPGROUP
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        @endif 
                    </div>
                    @include('components.paging',['count'=>count($lsObj),'page'=>$page,'itemPerPage'=>$itemPerPage])
            </div>
        </div>
    </div>
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
                            <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
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
                            <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
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
                            <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
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
                            <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
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
                            <img src="{{ asset('mpg-tmp/giaodienmuahang/images/product-image.png') }}" alt="">
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

    </script>
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/cart/pdetail.js') !!}
@endsection