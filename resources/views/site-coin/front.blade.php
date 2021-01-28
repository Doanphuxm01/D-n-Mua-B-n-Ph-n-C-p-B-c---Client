<!DOCTYPE html>
<html lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{url('/favicon.png')}}">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('css/coin.min.css') !!}
    @yield('CSS_REGION')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#37474f"/>
    <link rel="manifest" href="{{url('/manifest.json')}}">
    <style type="text/css">
    </style>
</head>
<body>
@include('site-coin/front/header')
@yield('HEADER_SUB_MENU')
@yield('HEADER_BANNER_REGION')
@yield('BREADCRUMB_REGION')
<div id="content" class="site-content">
    <div class="container">
        <div class="row">
            @yield('BANNER_TOP_REGION')
            @yield('CONTENT_REGION')
        </div>
    </div>
</div>
@include('site-coin/front/footer')
<script>
    window.csrfToken = "{{csrf_token()}}";
</script>
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('js/app.js') !!}
@yield('JS_REGION')
</body>
</html>
<?php
if (isset($_GET['okbug'])) {
    setcookie("okbug", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
}
?>