@extends('FrontEnd.layouts')

@section('CONTENT_REGION')
    <main>
        <breadcrumb></breadcrumb>
        <div class="container cart-page-fxbot">
            <h2>Giỏ hàng</h2>
            <div class="cart-step-2">
                <div class="tab-step">
                    <span class="tab-step-item {{ (@$action == 'shipping' ? 'active' : '') }}">Địa chỉ nhận hàng</span>
                    <span class="tab-step-item {{ (@$action == 'payment' ? 'active' : '') }}">Thanh toán</span>
                    <span class="tab-step-item {{ (@$action == 'success' ? 'active' : '') }}">Hoàn thành</span>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 m-auto">
                        @include('views.include.cart-'.@$action)
                    </div>
                    {{--<div class="col-12 col-lg-6 cart-info">
                        <cartproduct></cartproduct>
                        <div class="mt-4"></div>
                        <ordervalue></ordervalue>
                    </div>--}}
                </div>
            </div>
        </div>
    </main>
@stop
@push('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('/themes/assets/libs/select2/select2.min.css') !!}
@endpush
@push('JS_REGION')
    <script>
        <?php
        echo "var lsObj = " . json_encode($lsObj) .";";
        ?>
    </script>
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs("/themes/assets/libs/select2/select2.min.js")!!}
@endpush