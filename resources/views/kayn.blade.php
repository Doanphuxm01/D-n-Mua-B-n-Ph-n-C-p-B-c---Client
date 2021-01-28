<!DOCTYPE html>
<html lang="en-US">

<!-- Mirrored from themescare.com/demos/fabon-preview2/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 30 May 2020 12:43:34 GMT -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="_token" content="{{ csrf_token() }}">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <!-- Title -->
    <title>Kayn's Office</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ public_link('kayn-favicon.png') }}">
    <!--Bootstrap css-->

    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/bootstrap.css') !!}
    <!--Font Awesome css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/font-awesome.min.css') !!}
    <!--Animatedheadline css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/jquery.animatedheadline.css') !!}
    <!--Magnific css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/magnific-popup.css') !!}
    <!--Owl-Carousel css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/owl.carousel.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/owl.theme.default.min.css') !!}
    <!--Site Main Style css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/style.css') !!}
    <!--Responsive css-->
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('fabon/assets/css/responsive.css') !!}
</head>
<body>


<!--Navbar Start-->
@include('site-kayn.header')
<!--Navbar End-->

@yield('CONTENT_REGION')

<!-- Footer Area Start -->
@include('site-kayn.footer')
<!-- Footer Area End -->


<!--Jquery js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/jquery-3.0.0.min.js') !!}
<!--Bootstrap js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/bootstrap.min.js') !!}
<!--ScrollIt js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/scrollIt.min.js') !!}
<!--Owl-Carousel js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/owl.carousel.min.js') !!}
<!--Ripples js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/jquery.ripples-min.js') !!}
<!--Barfiller js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/jquery.barfiller.js') !!}
<!--Animatedheadline js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/jquery.animatedheadline.min.js') !!}
<!-- Isotop Js -->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/isotope.pkgd.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/custom-isotop.js') !!}
<!--Magnific js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/jquery.magnific-popup.min.js') !!}
<!--Init js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/init.js') !!}
<!--Main js-->
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('fabon/assets/js/main.js') !!}
</body>

<!-- Mirrored from themescare.com/demos/fabon-preview2/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 30 May 2020 12:44:07 GMT -->
</html>

