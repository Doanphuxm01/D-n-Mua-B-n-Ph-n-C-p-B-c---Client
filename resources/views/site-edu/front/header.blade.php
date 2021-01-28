<?php
$prefix_link = \Request::route();
if ($prefix_link) {
    $prefix_link = $prefix_link->getPrefix();
}
?>
<header id="masthead" class="site-header navbar-static-top" role="banner">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-inverse row">
            @if($agent->isMobile())
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                        data-target=".navbar-collapse" aria-controls="bs4navbar" aria-expanded="false"
                        aria-label="hoc1h.com">
                    <span class="fa fa-bars menu-icon"></span>
                </button>
            @endif
            <div class="navbar-brand">
                <a href="{{url('/')}}"
                   title="Học trực tuyến, giải bài tập sách giáo khoa">
                    <img src="{{url('/images/edu-logo-ff6000.png?ver=8989898989')}}"
                         alt="Học trực tuyến, giải bài tập sách giáo khoa">
                </a>

            </div>
            @if($agent->isMobile())
                <form class="form-inline my-2 my-lg-0 msearch-form" role="search" method="get"
                      action="{{url('/search')}}">
                    <input class="form-control mr-sm-2 mserach-txt" type="text"
                           value="@if(isset($keyword) && $keyword){{$keyword}}@endif" name="s" placeholder="Tìm bài giải, chủ đề..."
                           aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0 msearch-btn" type="submit"><i class="fa fa-search"></i></button>
                </form>
            @else
                <div class="col-md-4">
                    <form class="navbar-form search-form widget_search ml-4" role="search" action="{{url('/search')}}">
                        <div class="input-group">
                            <input type="text" value="@if(isset($keyword) && $keyword){{$keyword}}@endif" class="form-control"
                                   placeholder="Tìm bài giải, chủ đề..." name="s">
                            <button class="btn search-btn " type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
            @endif
            <div class="collapse navbar-collapse justify-content-end">
                <ul id="menu-mainmenu" class="navbar-nav">
                    <li class="nav-item menu-item-object-custom">
                        <a title="Học trực tuyến" href="{{url('/')}}" class="nav-link"><i class="fa fa-book"></i> Góc học tập</a>
                    </li>
                    <li class="nav-item menu-item-object-custom @if($prefix_link=='/check') active @endif">
                        <a title="Tin tức giáo dục" href="{{url('/giao-duc')}}" class="nav-link"><i class="fa fa-bullhorn"></i> Tin giáo dục</a>
                    </li>
                    {{--<li class="nav-item menu-item-object-custom @if($prefix_link=='/hoi-dap') active @endif">
                        <a title="Hướng dẫn sử dụng" href="{{url('/hoi-dap')}}" class="nav-link"><i class="fa fa-info"></i> Hỏi đáp</a>
                    </li>--}}
                    @if(isset($_MEMBER['email']) && $_MEMBER['email'])
                        <li class="nav-item menu-item-object-custom dropdown">
                            <a title="Tài khoản của tôi" href="javascript:void(0)" data-toggle="dropdown" class="dropdown-toggle nav-link account-link" aria-haspopup="true">
                                <i class="fa fa-user-secret"></i> {{$_MEMBER['email']}}
                            </a>
                        </li>
                    @else
                        <li class="nav-item menu-item-object-custom">
                            <a title="Đăng nhập hệ thống" href="javascript:void(0)" class="nav-link" onclick="return Member.showLoginForm()"><i class="fa fa-key"></i> Tài khoản</a>
                        </li>
                    @endif

                </ul>
            </div>
        </nav>
    </div>
</header>
@if(isset($_REGION) && $_REGION=='news')

    <?php
    if (!isset($allCateNewsEdu)) {
        $allCateNewsEdu = \App\Http\Models\Cate::getAllCateNewsEdu();
    }
    ?>


    <div class="menu-sub">
        <div class="container">
            <ul class="nav row">
                <li class="nav-item bars">
                    <a class="nav-link dropdown-toggle menu-education" href="{{url('/giao-duc')}}">Thông tin giáo dục</a>
                </li>
                @if(isset($allCateNewsEdu['items']) && $allCateNewsEdu['items'])
                    <?php
                    $indexMenuClass = 0;
                    $numberMenuClass = 10;//đếm bằng cơm cho tối ưu
                    ?>
                    @foreach($allCateNewsEdu['items'] as $key=>$val)
                        <?php
                        $indexMenuClass++;
                        ?>
                        <li class="nav-item dropdown"><a title="{{$val['name']}}" href="{{$val['link']}}">{{$val['name']}}</a>
                            @if(isset($allCateNewsEdu['parents']) && isset($allCateNewsEdu['parents'][$val['alias']]))
                                <div class="dropdown-menu @if(count($allCateNewsEdu['parents'][$val['alias']])>6) big2col @endif @if($numberMenuClass-3 <=$indexMenuClass) last @endif">
                                    @foreach($allCateNewsEdu['parents'][$val['alias']] as $ks=>$_id)
                                        <a title="{{$allCateNewsEdu['items'][$_id]['name']}}" href="{{$allCateNewsEdu['items'][$_id]['link']}}">{{$allCateSubject['items'][$_id]['name']}}</a>
                                    @endforeach
                                </div>
                            @endif
                        </li>

                    @endforeach
                @endif
            </ul>
        </div>
    </div>
@else
    <?php
    if (!isset($allCateSubject)) {
        $allCateSubject = \App\Http\Models\Cate::getAllCateSubject();
    }
    if (!isset($allMenuSubject)) {
        $allMenuSubject = \App\Http\Models\Cate::getAllMenuSubject();
    }
    if (!isset($allMenuClass)) {
        $allMenuClass = \App\Http\Models\Cate::getAllMenuClass();
    }
    ?>
    <div class="menu-sub">
        <div class="container">
            <ul class="nav row">
                <li class="nav-item dropdown bars">
                    <a class="nav-link dropdown-toggle menu-bars" {{--data-toggle="dropdown"--}}
                    href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">Góc học tập</a>
                    @if(isset($allMenuClass['items']) && $allMenuClass['items'])
                        <ul class="dropdown-menu">
                            @foreach($allMenuClass['items'] as $key=>$val)
                                <li>
                                    <a class="dropdown-item" title="{{$val['name']}}" href="{{$val['link']}}">{{$val['name']}}</a>
                                    @if(isset($allCateSubject['parents']) && isset($allCateSubject['parents'][$val['alias']]))
                                        <div class="dropdown-submenu">
                                            @foreach($allCateSubject['parents'][$val['alias']] as $ks=>$_id)
                                                <a title="{{$allCateSubject['items'][$_id]['name']}}" href="{{$allCateSubject['items'][$_id]['link']}}">{{$allCateSubject['items'][$_id]['name']}}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
                @if(isset($allMenuSubject['items']) && $allMenuSubject['items'])
                    <?php
                    $indexMenuClass = 0;
                    $numberMenuClass = 10;//đếm bằng cơm cho tối ưu
                    ?>
                    @foreach($allMenuSubject['items'] as $key=>$val)
                        <?php
                        $indexMenuClass++;
                        ?>
                        <li class="nav-item dropdown"><a title="{{$val['name']}}" href="{{$val['link']}}">{{$val['name']}}</a>
                            @if(isset($allCateSubject['parents']) && isset($allCateSubject['parents'][$val['alias']]))
                                <div class="dropdown-menu @if(count($allCateSubject['parents'][$val['alias']])>6) big2col @endif @if($numberMenuClass-3 <=$indexMenuClass) last @endif">
                                    @foreach($allCateSubject['parents'][$val['alias']] as $ks=>$_id)
                                        <a title="{{$allCateSubject['items'][$_id]['name']}}" href="{{$allCateSubject['items'][$_id]['link']}}">{{$allCateSubject['items'][$_id]['name']}}</a>
                                    @endforeach
                                </div>
                            @endif
                        </li>

                    @endforeach
                @endif
            </ul>
        </div>
    </div>
@endif
<div class="sticky-stop-header"></div>