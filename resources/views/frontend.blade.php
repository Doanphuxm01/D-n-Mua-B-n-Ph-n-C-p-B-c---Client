<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <meta name="_token" content="{{csrf_token()}}">
    <link rel="shortcut icon" href="{{asset('/images/logo.png')}}" id="favicon"/>
    <title>{{$HtmlHelper['Seo']['title']}}</title>
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <!-- Global stylesheets -->

{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/owl.carousel.min.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/owl.theme.default.min.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/style.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/jquery.mmenu.all.css') !!}

{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/slick.min.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/slick-theme.min.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('mpg-tmp/giaodienmuahang/css/custom.css') !!}

{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/public/alertifyjs/css/alertify.css') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/public/alertifyjs/css/themes/default.min.css') !!}
<!-- /global stylesheets -->
@yield('CSS_REGION')
<!-- Core JS files -->

{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/vue.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/jquery-3.4.1.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/interactions.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/popper.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/bootstrap.min.js') !!}

{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/slick.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/owl.carousel.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/jquery.mmenu.all.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/main.js') !!}

{{--
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/pnotify.min.js') !!}
--}}
@yield('JS_REGION')
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/io/io.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('texo.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/app.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/sweetalert2@9.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/public/alertifyjs/alertify.min.js') !!}

<!-- /theme JS files -->
    <script>var BASE_URL = "{{ url('/') }}";

        // Select all links with hashes

    </script>

</head>

<body class="bg-gray">

    <div id="app">
    @include('site-mpg.header')
    <main>
{{--        <div class="breadcrumb">--}}
{{--            <ul class="d-flex flex-wrap container-fluid">--}}
{{--                <li>--}}
{{--                    <a href="#">Trang chủ</a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a href="#">Tài Khoản</a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a href="#">Tài Khoản</a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </div>--}}
        @yield("CONTENT_REGION")
    </main>
    @include('site-mpg.footer')
</div>
</body>
@yield('JS_BOTTOM_REGION')
@stack('JS_BOTTOM_REGION')
@if(\App\Http\Models\Member::getCurent())
    <script>
        cart_load_number()
    </script>
@endif
<script>

    (function () {
        $('.checkout-cart').click(function () {
            $(location).attr('href', public_link('checkout'))
        });
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
        setTimeout(function(){
            $('.MagicZoomHint').remove()
            $('.eapps-remove-link').parents('a').remove()
            $('.DefaultButton__DefaultButtonComponent-ulobej-0.dEbKoN').next().remove()
        }, 1000);

    })();
</script>


</html>
<?php
if (isset($_GET['okbug'])) {
    setcookie("okbug", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
    //setcookie("okbugbar", $_GET['okbug'], time() + 840000, '/');  /* expire in 1 hour */
}
?>
