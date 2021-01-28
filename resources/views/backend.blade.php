<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <meta name="_token" content="{{csrf_token()}}">
        <link rel="shortcut icon" href="{{asset('/images/logo.png')}}" id="favicon"/>
        <title>{{$HtmlHelper['Seo']['title']}} - Minh Phúc Group</title>

        <!-- Global stylesheets -->

    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/icons/icomoon/styles.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/bootstrap.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/bootstrap-editable.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/jqueryui-editable.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/core.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/components.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/colors.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/fontawesome-all.min.css') !!}
    <!-- /global stylesheets -->
    @yield('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/io/io.css') !!}
    <!-- Core JS files -->
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/loaders/pace.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/core.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/bootstrap.min.js') !!}

    {{--
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/pnotify.min.js') !!}
    --}}
    @yield('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/io/io.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('texo.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/app.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/bootstrap-editable.min.js') !!}

    <!-- /theme JS files -->
        <script>var BASE_URL = "{{ url('/') }}";

            // Select all links with hashes

        </script>

        </head>

<body class="sidebar-xs navbar-top  pace-done">
<!-- Main navbar -->
<div class="navbar navbar-inverse bg-brand navbar-fixed-top">
    <div class="navbar-header" style="min-width:110px;">
        <a class="navbar-brand" href="{{public_link('/')}}"{{-- onclick="MNG_PROJECT.showSwitch()"--}}>
            <img src="{{asset('/images/logo.png')}}" style="    height: auto;
    position: relative;
    width: 64px;
    float: left;
    margin-right: 8px;
    top: -6px;
    left: -3px;">
            <strong style="font-size: 16px;position: relative;top: 0px;">{{ config('app.cms_name') }} </strong>
            {{-- <div>
                 <small style="position: relative;top: -11px;font-weight: 100;font-size: 85%;color: greenyellow;">
                     @if(!\App\Http\Models\Project::$curentProject)
                         Bạn chưa chọn dự án
                     @else
                         {{\App\Http\Models\Project::$curentProject['name']}}
                         <i class="fa fa-angle-down"></i>
                     @endif
                 </small>
             </div>--}}
        </a>
        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-database"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav ">

            @if (isset($MAIN_MENU_BACKEND) && $MAIN_MENU_BACKEND)
                @foreach($MAIN_MENU_BACKEND as $key=>$level1)
                    <li class="dropdown @if ( isset($level1['group']) && strtolower(Route::current()->getName())==strtolower($level1['group'])) active @endif {{'menu-group-'.@$level1['group']}}">
                        <a class="dropdown-toggle" data-toggle="dropdown"
                           href="@if (isset($level1['link']) && $level1['link']) {!! ($level1['link']) !!} @endif" @if (isset($level1['action']) && $level1['action']) {!! $level1['action'] !!} @endif>
                            {!! $level1['icon'] !!}  {{$level1['label']}} <span class="caret"></span>
                        </a>
                        @if(isset($level1['sub']) && $level1['sub'])
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach($level1['sub'] as $k=>$level2)
                                    @if(isset($level2['label']))
                                        @if (!isset($level2['status']) || $level2['status']!='off')
                                            <li @if(isset($level2['sub']) && $level2['sub']) class="dropdown-submenu {{@$level2['class']}}"  @else class=" {{@$level2['class']}}" @endif>
                                                <a
                                                        @if(isset($level2['disabled']) && $level2['disabled'] ) class="text-grey" @endif
                                                @if (isset($level2['action']) && $level2['action'])
                                                onclick='{!! $level2['action'] !!}'
                                                        @endif href="{{(@$level2['link'])}}">
                                                    {!! $level2['icon'] !!}
                                                    {{@$level2['label']}}

                                                </a>
                                                @if(isset($level2['sub']) && $level2['sub'])
                                                    <ul class="dropdown-menu">
                                                        @foreach($level2['sub'] as $level3)
                                                            @if(isset($level3['label']))

                                                                <li @if(isset($level3['sub']) && $level3['sub']) class="dropdown-submenu" @endif>
                                                                    <a
                                                                            @if(isset($level3['disabled']) && $level3['disabled'] ) class="text-grey" @endif
                                                                    @if (isset($level3['action']) && $level3['action'])
                                                                    onclick='{!! $level3['action'] !!}'
                                                                            @endif href="{{(@$level3['link'])}}">
                                                                        {!! @$level3['icon'] !!}
                                                                        {{@$level3['label']}}

                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="divider"></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endif
                                    @else
                                        <li class="divider"></li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            @endif

        </ul>

        {{--Tạm dùng css đây sau chuyển sang file--}}

        <ul class="nav navbar-nav navbar-right">

            @php
                \App\Http\Models\Project::getAllProjectByMe();
            @endphp

            {{--Khu vực thông báo--}}{{--
            @include("components.tools_2")
            @include("components.tools")
            --}}{{--            @include("components.msg-dropdown")--}}{{--
            @include("components.notif-dropdown")--}}

            <li class="dropdown dropdown-user" title="Tài khoản: {{isset($_MEMBER['account'])?$_MEMBER['account']:'Guest'}}">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    @if(isset($_MEMBER['avatar_url']) && $_MEMBER['avatar_url'])
                        {{--<img src="{{$_MEMBER['avatar_url']}}" alt="">--}}
                        <img

                                style="width: 33px;height: 33px; margin-top:3px"
                                src="{{\App\Http\Models\Media::getImageSrc($_MEMBER['avatar_url'])}}" alt="">
                    @else
                        <img
                                style="width: 33px;height: 33px; margin-top:3px"

                                src="/images/no-avatar.png" alt=""

                        >
                    @endif
                    {{--<i class=" icon-user-check"></i>--}}
                    <span></span>
                    <i class="caret"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{admin_link('/staff/input?id='.$_MEMBER['_id'])}}"><i class="icon-user"></i> Thông tin của tôi</a></li>
                    <li>
                        <a id="change-pass-btn" href="javascript:;"
                                onclick="_SHOW_FORM_REMOTE('{{admin_link('/staff/change-password?id='.\App\Http\Models\Member::getCurentId())}}')">
                            <i class="icon-key"></i> Đổi mật khẩu
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li><a onclick="return confirm('Bạn có chắc chắn muốn thoát?');" href="{{url('/auth/logout')}}"><i
                                    class="icon-switch2"></i> Logout</a></li>

                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">
        <div class="content-wrapper">

            <!-- Page header -->
            <div class="page-header page-header-xs">
                @yield('BREADCRUMB_REGION')
            </div>
            <!-- /page header -->


            <!-- Content area -->
            <div class="content" style="{{@$_content_flex}}">
                @yield('FILTER_REGION')
                @yield('CONTENT_REGION')
            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->

</body>
@yield('JS_BOTTOM_REGION')
@stack('JS_BOTTOM_REGION')
<script>
    (function () {
        INPUT_NUMBER();
        INPUT_NUMBER_FORMAT();
        // Checkboxes, radios
        $(".styled").uniform({ radioClass: 'choice' });

        // File input
        $(".file-styled").uniform({
            fileButtonHtml: '<i class="icon-googleplus5"></i>',
            wrapperClass: 'bg-warning'
        });
        _EDITABLE_SELECT('.editable-status-select')
        // hold onto the drop down menu
        var dropdownMenu;
        // and when you show it, move it to the body
        $(window).on('show.bs.dropdown', function (e) {

            // grab the menu
            dropdownMenu = $(e.target).find('.dropdown-menu.dropdown_fix');

            // detach it and append it to the body
            $('body').append(dropdownMenu.detach());

            // grab the new offset position
            var eOffset = $(e.target).offset();
            var eOffsetLeft = eOffset.left;
            // make sure to place it where it would normally go (this could be improved)
            if(dropdownMenu.hasClass('dropdown-menu-right')){
                dropdownMenu.attr('style',`display: block;top: ${eOffset.top + $(e.target).outerHeight()}px;left: ${eOffsetLeft + $(e.target).outerWidth() - dropdownMenu.outerWidth()}px!important;right: auto!important`);
            }else{
                dropdownMenu.css({
                    'display': 'block',
                    'top': eOffset.top + $(e.target).outerHeight(),
                    'left': eOffsetLeft
                });
            }
        });

        // and when you hide it, reattach the drop down, and hide it normally
        $(window).on('hide.bs.dropdown', function (e) {
            if(dropdownMenu)
            {
                $(e.target).append(dropdownMenu.detach());
                dropdownMenu.hide();
            }

        });

    })();
</script>
</html>
<?php
if (isset($_GET['okbug'])) {
    setcookie("okbug", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
    //setcookie("okbugbar", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
}
?>
