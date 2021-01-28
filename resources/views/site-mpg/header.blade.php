<style>
    header .header-menu .header-menu-right p i {
        margin-bottom: 4px;
    }
    header .header-menu .header-menu-right p {
        font-size: unset;
    }
</style>
<header>
    <div class="heade-topbar">
        <div class="container-fluid d-flex px-0 position-relative" style="z-index: 11">
            <div class="top-bar-left">
                <ul class="list-link">
                    <li><a href="#">Tải ứng dụng</a></li>
                    <li><a href="#">Kết nối</a></li>
                </ul>
            </div>
            <div class="top-bar-right ml-auto">
                <ul class="list-link">
                    {{--<li class="position-relative notification">
                        <a href="#">
                            <i class="icon icon-thong-bao"></i>
                            <span>Thông báo</span>
                        </a>
                        <div class="box-notification">
                            <h5>
                                THÔNG BÁO MỚI NHẬN
                            </h5>
                            <div class="notification-item d-flex align-items-start">
                                <a href="" class="img">
                                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/image-noti.png') }}" alt="">
                                </a>
                                <div class="box-right">
                                    <a href="#" class="link">
                                        Minh Phuc Group ƯU ĐÃI ĐẶC BIỆT 30/04 -1/5
                                    </a>
                                    <p>
                                        Cơ hội nhận nhiều giải thưởng lên đến hàng tỉ đồng. Mua 1 sản phẩm được tặng 1 sp.....
                                    </p>
                                </div>
                            </div>
                            <div class="notification-item d-flex align-items-start">
                                <a href="" class="img">
                                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/image-noti.png') }}" alt="">
                                </a>
                                <div class="box-right">
                                    <a href="#" class="link">
                                        Minh Phuc Group ƯU ĐÃI ĐẶC BIỆT 30/04 -1/5
                                    </a>
                                    <p>
                                        Cơ hội nhận nhiều giải thưởng lên đến hàng tỉ đồng. Mua 1 sản phẩm được tặng 1 sp.....
                                    </p>
                                    <div class="text-right">
                                        <a href="#" class="btn-noti-light">XEM CHI TIẾT</a>
                                        <a href="#" class="btn-noti-green">ĐÃ NHẬN HÀNG</a>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item d-flex align-items-start">
                                <a href="" class="img">
                                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/image-noti.png') }}" alt="">
                                </a>
                                <div class="box-right">
                                    <a href="#" class="link">
                                        Minh Phuc Group ƯU ĐÃI ĐẶC BIỆT 30/04 -1/5
                                    </a>
                                    <p>
                                        Cơ hội nhận nhiều giải thưởng lên đến hàng tỉ đồng. Mua 1 sản phẩm được tặng 1 sp.....
                                    </p>
                                    <div class="text-right">
                                        <a href="#" class="btn-noti-light">XEM CHI TIẾT</a>
                                        <a href="#" class="btn-noti-green">ĐÃ NHẬN HÀNG</a>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item d-flex align-items-start">
                                <a href="" class="img">
                                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/image-noti.png') }}" alt="">
                                </a>
                                <div class="box-right">
                                    <a href="#" class="link">
                                        Minh Phuc Group ƯU ĐÃI ĐẶC BIỆT 30/04 -1/5
                                    </a>
                                    <p>
                                        Cơ hội nhận nhiều giải thưởng lên đến hàng tỉ đồng. Mua 1 sản phẩm được tặng 1 sp.....
                                    </p>
                                </div>
                            </div>
                            <a href="#" class="view-all-noti">
                                xem tất cả
                            </a>
                        </div>
                    </li>--}}
                    <li>
                        <a href="#">
                            <i class="icon icon-tro-giup"></i>
                            <span>Trợ giúp</span>
                        </a>
                    </li>

                    @php($member = \App\Http\Models\Member::getCurent())
                    @if(isset($member['_id']))
                        <li>
                            <a href="{{ public_link('admin') }}" class="font-weight-bold">
                                {{ $member['name'] }}
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ public_link('auth/register') }}" class="font-weight-bold">
                                Đăng ký
                            </a>
                        </li>
                        <li>
                            <a href="{{ public_link('auth/login') }}" class="font-weight-bold">
                                Đăng nhập
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="header-main">
        <div class="container-fluid d-flex align-items-center">
            <div class="logo">
                <a href="{{ public_link('/') }}">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/logo.png') }}" alt="">
                </a>
            </div>
            <div class="search-form d-xl-block">
                <form action="{{route('FeHome')}}">
                    <input name="q" value="{{app('request')->input('q')}}"  type="text" placeholder="Tìm sản phẩm">
                    <button><i class="icon icon-search"></i><span>Tìm kiếm</span></button>
                </form>
            </div>
            <a href="javascript:;" class="search-mobile">
                <i class="icon icon-search"></i>
            </a>

            <a href="{{ public_link('checkout') }}" class="cart-head">
                <i class="icon icon-cart"></i>
                <span class="text">Giỏ hàng</span>
                <div class="add-to-cart-success">
                    <span class="close"><i class="fxboticon fa fa-times" aria-hidden="true"></i></span>
                    <p class="text">
                        Thêm vào giỏ hàng thành công!
                    </p>
                    <button class="btn checkout-cart">Xem giỏ hàng và thanh toán</button>
                </div>
                <span class="counter-cart counter">0</span>
            </a>
            <a href="#menu" class="menu-mobile trigger">
                <i class="icon icon-menu"></i>
            </a>
        </div>
    </div>
    <div class="header-menu">
        <div class="container-fluid d-flex align-items-end">
            <div class="menu-wrap js-show-menu-active"> <!-- isHome -->
                <a href="javascript:;" class="menu-btn js-menu-pc"><i class="fa fa-bars"></i> DANH MỤC SẢN PHẨM</a>
                <div class="main-menu">
                    {!! $tpl['html'] !!}
                    
                </div>
            </div>
            <div class="header-menu-right d-none d-lg-flex flex-wrap flex-lg-nowrap justify-content-between justify-content-lg-end align-items-end px-0">
                <p class="d-flex align-items-end justify-content-center justify-content-lg-start ml-0">
                    <i class="icon icon-location"></i>
                    Bạn muốn<br>  giao hàng tới đâu
                </p>
                <p class="d-flex align-items-end justify-content-center justify-content-lg-start">
                    <i class="icon icon-ship"></i>
                    Giao nhanh<br> Hàng trăm nghìn sản phẩm
                </p>
                <p class="d-flex align-items-end justify-content-center justify-content-lg-start">
                    <i class="icon icon-genuine"></i>
                    Sản phẩm<br> 100% chính hiệu
                </p>
                <p class="d-flex align-items-end justify-content-center justify-content-lg-start">
                    <i class="icon icon-cart-2"></i>
                    Đổi hàng dễ dàng <br> Thân thiện khách hàng
                </p>
            </div>
        </div>
    </div>
    <div style="display: none" class="nav-mobile" id="menu">
        {!! $tpl['html'] !!}
        {{-- <ul class="menu">
            <li class="menu-item hasSub">
                <a href="#" class="menu-link">
                    <img src="images/ic_menu-1.png" alt="">
                    Mỹ Phẩm - Làm Đẹp
                </a>
                <ul class="sub-menu">
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item hasSub">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-2.png') }}" alt="">
                    MP - LUXURY
                </a>
                <ul class="sub-menu">
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item hasSub">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                        <ul class="sub-menu">
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                            <li class="sub-item">
                                <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                    <li class="sub-item">
                        <a href="#">Mỹ Phẩm - Làm Đẹp</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-3.png') }}" alt="">
                    Hàng Tiêu Dùng
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-4.png') }}" alt="">
                    Quà Tặng
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-5.png') }}" alt="">
                    Đồ Gia Dụng
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-6.png') }}" alt="">
                    Thực Phẩm Bảo Vệ Sức Khỏe
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-7.png') }}" alt="">
                    Voucher - Dịch Vụ - Thẻ Cào
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-8.png') }}" alt="">
                    Máy tính - Điện Thoại
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-9.png') }}" alt="">
                    Nội Thất Gia Đình
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-10.png') }}" alt="">
                    Thực Phẩm Bảo Vệ Sức Khỏe
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-11.png') }}" alt="">
                    Voucher - Dịch Vụ - Thẻ Cào
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-12.png') }}" alt="">
                    Máy tính - Điện Thoại
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <img src="{{ url('mpg-tmp/giaodienmuahang/images/ic_menu-13.png') }}" alt="">
                    Nội Thất Gia Đình
                </a>
            </li>
        </ul> --}}
    </div>
</header>

<script>
   $('.nav-mobile').hide();
   $('.trigger').click(function() {
    $('.nav-mobile').show();
});
</script>