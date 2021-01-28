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

    <!-- JavaScript -->
{{--    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>--}}

    <!-- CSS -->
{{--    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>--}}
    <!-- Default theme -->
{{--    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>--}}
    <!-- Semantic UI theme -->
{{--    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>--}}
    <!-- Bootstrap theme -->
{{--    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>--}}
    <!-- Global stylesheets -->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/css/icons/icomoon/styles.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/css/bootstrap.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/css/core.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/css/components.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/css/colors.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/backend-ui/assets/io/io.css') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/public/alertifyjs/css/alertify.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/public/alertifyjs/css/themes/default.min.css') !!}
    <!-- /global stylesheets -->
    @yield('CSS_REGION')
    <link href="{{url('/backend-ui/assets/io/io.css')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}" rel="stylesheet" type="text/css">
    <!-- Core JS files -->
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/plugins/loaders/pace.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/core/libraries/jquery.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/core/libraries/jquery_ui/core.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/core/libraries/bootstrap.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/plugins/loaders/pace.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/plugins/notifications/pnotify.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('vue.js') !!}


            <!-- /core JS files -->
    @yield('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/io/io.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('/backend-ui/assets/js/core/app.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/sweetalert2@9.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/public/alertifyjs/alertify.min.js') !!}

    <!-- Theme JS files -->
    <!-- /theme JS files -->
    <script>var BASE_URL = "{{ url('/') }}";</script>

</head>

<body class="sidebar-xs">

<!-- Main navbar -->
<div class="navbar navbar-inverse bg-brand">
    <div class="navbar-header" style="min-width:205px;">
        <a class="navbar-brand" href="{{public_link('/')}}">
{{--
            <i style="font-size: 27px; top: -6px; float: left; margin-right: 9px;" class=" icon-IE"></i> <strong style="font-size: 18px">Texo's Office System</strong>
--}}
            <img src="{{asset('/images/logo.png')}}" style="    height: auto;
    position: relative;
    width: 64px;
    float: left;
    margin-right: 8px;
    top: -6px;
    left: -3px;"> <strong style="font-size: 18px">{{ config('app.cms_name') }} </strong>
        </a>

        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-database"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav navbar-right">
           
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <i class=" icon-user-check"></i>
                    <span>Hi you!</span>
                    <i class="caret"></i>
                </a>
        </ul>
    </div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">
        <!-- Main sidebar -->
        <div class="sidebar sidebar-main bg-brand">
            <div class="sidebar-content">
                <div class="sidebar-user">
                </div>
                <div class="sidebar-category sidebar-category-visible">
                    <div class="category-content no-padding">
                        <ul class="navigation navigation-main navigation-accordion">
                            <li class="navigation-header"><span>Hi..</span> <i class="icon-menu" title="You need login!"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Content area -->
            <div class="content">
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
</html>