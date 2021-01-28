<!DOCTYPE html>
<html lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="shortcut icon" href="{{url('/favicon.png')}}">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('css/edu.min.css') !!}
    @yield('CSS_REGION')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#37474f"/>
    <meta name="google-site-verification" content="WxNsNff7LIzlOZ12WY6zy4Q-KnXVF3Rk1E8bWWkYQp4" />
    <link rel="manifest" href="{{url('/manifest.json')}}">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-108036103-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-108036103-4');
    </script>
</head>

<body @if(isset($_HEADER_TEMPLATE))class="book-region" @endif>

@include('site-edu/front/header')


@yield('HEADER_SUB_MENU')
@yield('HEADER_BANNER_REGION')
@yield('BREADCRUMB_REGION')
<div class="container">
    <div class="row">
        @yield('BANNER_TOP_REGION')
        @yield('CONTENT_REGION')
    </div>
</div>
@yield('BREADCRUMB_REGION_BOTTOM')
@include('site-edu/front/footer')
<!-- Modal -->
<div id="loginFormContainer"></div>
<div id="registerFormContainer"></div>
<script>
    window.csrfToken = "{{csrf_token()}}";
</script>
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('js/app.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('js/edu.js') !!}
@yield('JS_REGION')
</body>
</html>
<?php
if (isset($_GET['okbug'])) {
    setcookie("okbug", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
}
?>