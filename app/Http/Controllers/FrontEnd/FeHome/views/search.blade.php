@extends('frontend')

@section('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css') !!}
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
@endsection

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/magiczoom.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js') !!}
@endsection

@section('CONTENT_REGION')
        <!-- <breadcrumb></breadcrumb> -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-12 col-xl-12">
                    <div class="product-head">
                        <div class="product-breadcrumb">
                            <a href="{{ public_link('/') }}">
                                Trang chủ
                            </a>
                            <a href="#">
                                Sản phẩm được tìm thấy
                            </a>
                        </div>
                        <h2 class="product-title">
                            <span><b>{{ @$lsObj->total() }}</b> sản phẩm</span>
                        </h2>
                    </div>
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
{{--                        @include('components.paging',['count'=>count($lsObj),'page'=>$page,'itemPerPage'=>$itemPerPage])--}}
                        @include('site-mpg.pagination',['paginator'=>$lsObj])
                    </div>
                </div>
                <div class="col-12 col-lg-12 col-xl-12">
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
                </div>
            </div>
        </div>
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