$(window).bind('load', function(){
    $('.search-mobile').click(function(){
        $('.search-form').stop().slideToggle(0);
        $('.main-menu').hide();
    });
    function menu(hoder, menu){
        $(hoder).click(function(){
            $(menu).stop().slideToggle(0);
            $('.search-form').hide();
        });
    }
    menu('.menu-mobile', '.main-menu');
    menu('.js-menu-pc', '.main-menu');
    function setsize(){
        if($('.banner-center').height() > $('.isHome .menu').height()){
            $('.isHome .menu').css('height', $('.banner-center').height()+'px');
            $('.banner-right img').css('height', ($('.banner-center').height() - 20)/3 + 'px' )
        }else {
            $('.banner-center img').css('height', $('.isHome .menu').innerHeight()+'px');
            $('.banner-right img').css('height', ($('.isHome .menu').innerHeight()- 20)/3 + 'px' )
        }
    }
    if($(window).width() > 1200){
        setsize();
        $(window).on('resize', function(){
            setsize();
        });
    }
    // Qty
    $('.plus').click(function () {
        $(this).prev().val(+$(this).prev().val() + 1);
    });
    $('.minus').click(function () {
        if ($(this).next().val() > 0) $(this).next().val(+$(this).next().val() - 1);
    });
    
    // ============
    // ============
    if($('.product-sale-slider').length > 0){
        $('.product-sale-slider').owlCarousel({
            loop: true,
            margin: 0,
            dots: false,
            responsive: {
                0:{
                    items: 2
                },
                380:{
                    items: 3
                },
                600:{
                    items: 4
                },
                1200:{
                    items: 6
                },
                1600:{
                    items: 8
                }
            }
        })
    }
    
    // ============
    // ============
    if($('.sugget-product-slider').length > 0){
        $('.sugget-product-slider').owlCarousel({
            loop: true,
            margin: 0,
            dots: false,
            nav: true,
            navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
            responsive: {
                0:{
                    items: 2
                },
                480:{
                    items: 3
                },
                768:{
                    items: 4
                },
                1200:{
                    items: 5
                },
                1600:{
                    items: 7
                }
            }
        });
    }

    // ============
    // ============
    $('.js-close-popup').click(function(){
        $('.popup-dia-chi').hide();
    });
    $('.js-show-address').click(function(){
        $('.popup-dia-chi').show();
    });

    // ============
    // ============

    if($('.slider-for').length > 0){
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav'
        });
        $('.slider-nav').slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: true,
            arrows: true,
            focusOnSelect: true,
            responsive: [
                {
                    breakpoint: 1600,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });
    }

    if($('.big-img').length > 0){
        $('.big-img').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.small-img'
        });
        $('.small-img').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.big-img',
            dots: false,
            arrows: false,
            focusOnSelect: true,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 3
                    }
                }
            ]
        });
    }

    $('#menu').mmenu();
});