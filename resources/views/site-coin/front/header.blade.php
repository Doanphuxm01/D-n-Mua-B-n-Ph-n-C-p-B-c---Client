<?php
$prefix_link = \Request::route();
if ($prefix_link) {
    $prefix_link = $prefix_link->getPrefix();
}
?>
<header id="masthead" class="site-header navbar-static-top" role="banner">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-inverse">
            @if($agent->isMobile())
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                        data-target=".navbar-collapse" aria-controls="bs4navbar" aria-expanded="false"
                        aria-label="Techhandle.net">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif
            <div class="navbar-brand">
                <a href="{{url('/')}}"
                   title="Mua bán tiền ảo uy tín tin cậy">
                    <img src="{{url('/images/coin-logo.png?ver=8989898989')}}"
                         alt="Mua bán tiền ảo uy tín tin cậy">
                </a>

            </div>
            {{--@if($agent->isMobile())
                <form class="form-inline my-2 my-lg-0 msearch-form" role="search" method="get"
                      action="{{url('/search')}}">
                    <input class="form-control mr-sm-2 mserach-txt" type="text"
                           value="@if(isset($keyword)) {{trim($keyword)}} @endif" name="s" placeholder="Từ khóa..."
                           aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0 msearch-btn" type="submit">Search</button>
                </form>
            @else
                <div class="col-md-4">
                    <form class="navbar-form search-form widget_search ml-4" role="search" action="{{url('/search')}}">
                        <div class="input-group">
                            <input type="text" value="@if(isset($keyword)) {{$keyword}} @endif" class="form-control"
                                   placeholder="Từ khóa..." name="s">
                            <button class="btn search-btn " type="submit">Search</button>
                        </div>
                    </form>
                </div>
            @endif--}}
            <div class="collapse navbar-collapse justify-content-end">
                <ul id="menu-mainmenu" class="navbar-nav">

                    <li class="nav-item menu-item-object-custom {{--dropdown--}} @if($prefix_link=='/news') active @endif">
                        <a title="Tutorials" href="{{url('/news')}}" {{--data-toggle="dropdown"--}}
                        class="{{--dropdown-toggle--}} nav-link"><i class="fa fa-newspaper-o"></i> Tin tức tài chính</a>
                    </li>
                    <li class="nav-item menu-item-object-custom @if($prefix_link=='/check') active @endif">
                        <a title="Download" href="{{url('/check')}}" class="nav-link"><i class="fa fa-search"></i> Tra cứu giao dịch</a>
                    </li>
                    <li class="nav-item menu-item-object-custom @if($prefix_link=='/help') active @endif">
                        <a title="libraries code" href="{{url('/help')}}" class="nav-link"><i class="fa fa-info"></i> Hướng dẫn</a>
                    </li>
                    <li class="nav-item menu-item-object-custom">
                        <a title="Đăng ký tài khoản" href="javascript:void(0)" class="nav-link"><i class="fa fa-user"></i> Đăng ký</a>
                    </li>
                    <li class="nav-item menu-item-object-custom">
                        <a title="Đăng nhập hệ thống" href="javascript:void(0)" class="nav-link"><i class="fa fa-key"></i> Đăng nhập</a>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</header>