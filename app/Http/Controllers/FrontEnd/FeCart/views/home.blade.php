@extends('frontend')
@section('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/icons/icomoon/styles.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/core.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/css/components.min.css') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/js/plugins/forms/select2/select2.min.css') !!}
@stop
@section('CONTENT_REGION')
    @if(!isset($allCity))
        @php
            $allCity = collect(\App\Http\Models\Location::getAllCity());
        @endphp
    @endif
    <style>
        .icon-cart:before, .icon-ship:before, .icon-menu:before {
            content: "";
        }
        select.form-control:not([size]):not([multiple]) {
            display: block;
            background-color: #f5f3f3;
            border: 1px solid #b7b7b7;
            width: 100%;
            height: 52px;
            padding: 0 18px;
            margin-bottom: 20px;
        }

        .select2-container {
            background-color: #f5f3f3;
            border: 1px solid #b7b7b7;
            display: block;
            background-color: #f5f3f3;
            border: 1px solid #b7b7b7;
            width: 100%;
            height: 52px;
            margin-bottom: 20px;
        }

        .select2-container--default .select2-selection--single {
            height: 100%;
            border: none;
            background-color: #f5f3f3;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-top: 11px;
            padding-left: 18px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 11px;
        }

        .select2-search:after {
            left: 15px;
        }

        .select2-search--dropdown .select2-search__field {
            padding: 4px 30px;
        }

        .select2-choice {
            background-color: #f5f3f3!important;
            border: none!important;
        }
    </style>
    <main class="page-order pb-5">
        <div class="container-fluid" id="info-products">
            <div class="bg-white shadow rounded-8 px-4 py-3 mb-3 d-none d-md-block">
                <table class="table-list-order w-100 table-responsive d-block d-xl-table">
                    <thead>
                    <tr>
                        <td><b class="fs-22 d-block" style="min-width: 470px;">Sản phẩm</b></td>
                        <td align="center" class="text-nowrap"><b style="min-width: 210px" class="d-block fs-20 text-gray">Đơn giá</b></td>
                        <td align="center" class="text-nowrap"><b style="min-width: 210px" class="d-block fs-20 text-gray">Số lượng</b></td>
                        <td align="center" class="text-nowrap"><b style="min-width: 160px" class="d-block fs-20 text-gray">Số tiền</b></td>
                        <td align="center" class="text-nowrap"><b style="min-width: 110px" class="d-block fs-20 text-gray">Thao tác</b></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(obj, ob) in lsObj['details']">
                        <td>
                            <div class="product d-flex align-items-center">
                                <a :href="obj['link']" class="img">
                                    <img v-if="typeof obj['image'] != 'undefined'"
                                         :src="'https://vpdt.minhphucgroup.com.vn/data/'+obj['image']['path']" :alt="obj['image']['name']"/>
                                </a>
                                <a :href="obj['link']" class="p-name link text-primary" v-text="obj['name']"></a>
                            </div>
                        </td>
                        <td align="center">
                                    <span class="price fs-20">
                                        @{{ formatMoney(obj['finalPrice']) }}
                                    </span>
                                <p class="price2">
                                    <del>@{{ formatMoney(obj['regularPrice']) }}</del> <span class="sale text-bold text-danger" v-text="calcDiscount(obj['finalPrice'], obj['regularPrice'])"></span>
                                </p>
                        </td>
                        <td align="center">
                            <input type="number" name="qty" v-model="obj['amount']" @change="changeAmount(obj)">
                        </td>
                        <td align="center">
                                    <span class="price fs-20">
                                        @{{ formatMoney(obj['finalPrice']*obj['amount']) }}
                                    </span>
                        </td>
                        <td align="center">
                            <a href="javascript:;" @click="remove($event,ob,obj)" class="text-gray fs-20">
                                Xóa
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="product-cart-list bg-white shadow rounded-8 px-2 px-sm-4 py-3 mb-3 d-block d-md-none">
                <div class="product-cart-item d-flex align-items-start mb-3 pb-3 border-bottom" v-for="(obj, ob) in lsObj['details']">
                    <img v-if="typeof obj['image'] != 'undefined'" :src="obj['image']['path']" class="img rounded-3 mr-2" :alt="obj['image']['name']">
                    <p>
                        <span class="name d-block fs-18"><a :href="obj['link']" class="p-name link" v-text="obj['name']"></a></span>
                        <span class="count fs-18">Đơn giá: <span class="text-danger">@{{ formatMoney(obj['finalPrice']) }}</span></span>
                        <span class="d-block my-2 fs-18">Số lượng: <input type="number" @change="changeAmount(obj)" v-model="obj['amount']" style="width: 40px;"></span>
                        <span class="fs-18 d-block">Số tiền: <b class="text-danger">@{{ formatMoney(obj['finalPrice']*obj['amount']) }}</b></span>
                    </p>
                    <a href="javascript:;" @click="remove($event,ob,obj)" class="ml-auto text-gray">Xóa</a>
                </div>
            </div>
            @if(!empty($lsQuaTang))
                <div class="group border-top @if(isset($lsQuaTang['da_tang'])) da_tang @endif">
                    <div class="grid px-4 py-3 bg-white FrequentlyProducts__Wrapper-elrz6y-0 khSZLo">
                        @if(isset($lsQuaTang['da_tang']))
                            <h2 class="group-title" style="font-size: 22px">Sản phẩm đã được tặng từ đợt mua trước<small class="text-danger text-italic">
                                    &nbsp; (Sản phẩm chỉ được áp dụng khi đặt hàng tại CÔNG TY CỔ PHẦN TẬP ĐOÀN TRUYỀN THÔNG MINH PHÚC và sản phẩm chỉ tặng duy nhất 1 lần, từ lần kế tiếp bạn muốn có sản phẩm này, bạn cần trả phí cho sản phẩm tặng này)</small></h2>
                        @else
                            <h2 class="group-title" style="font-size: 22px">Quà tặng đính kèm<small class="text-danger text-italic">
                                    &nbsp; (Sản phẩm chỉ được áp dụng khi đặt hàng tại CÔNG TY CỔ PHẦN TẬP ĐOÀN TRUYỀN THÔNG MINH PHÚC và sản phẩm chỉ được tặng duy nhất 1 lần, từ lần kế tiếp bạn muốn có sản phẩm này, bạn cần trả phí cho sản phẩm tặng này)</small></h2>
                        @endif
                        <div class="images row mx-0">
                            @foreach($lsQuaTang['san_pham_ap_dung'] as $item)
                                <a href="{{ public_link(@$item['alias'].'-p'.@$item['_id'].'.html') }}" title="{{ \App\Elibs\Helper::showContent(@$item['name']) }}" class="image mb-3">
                                    <div class="h-100" data-image="{{ \App\Http\Models\Media::getImageSrc(@$item['avatar_url']) }}">
                                        <img src="{{ \App\Http\Models\Media::getImageSrc(@$item['avatar_url']) }}" alt="{{ \App\Elibs\Helper::showContent(@$item['name']) }}">
                                    </div>
                                </a>
                                @if(!$loop->last)
                                    <p class="plus mb-3">+</p>
                                @endif
                            @endforeach
                        </div>
                        <div class="frequently-products">
                            @php($total = 0)
                            @foreach($lsQuaTang['san_pham_ap_dung'] as $item)
                                @php($total += @$item['finalPrice'])
                                <div class="frequently-product-item row mx-0">
                                    <input id="isImage_tuyetDz" class="radio-custom myCheckbox" name="radio-group" disabled type="checkbox" checked>
                                    <a href="{{ public_link(@$item['alias'].'-p'.@$item['_id'].'.html') }}" class="frequently-product-name text-primary">
                                        <span><b>{{ \App\Elibs\Helper::showContent(@$item['name']) }}</b></span>
                                    </a>
                                    <p class="frequently-product-price text-italic">[SL: {{ @$item['amount'] }}] &nbsp;</p>
                                    <p class="text-danger font-weight-bold frequently-product-price ">{{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}/1sp</p>
                                </div>
                            @endforeach
                        </div>
                        {{--<div class="frequently-add-to-cart">
                            <p class="frequently-sum-price">Tổng tiền: <span>{{ \App\Elibs\Helper::formatMoney(@$total) }}</span></p>
                        </div>--}}
                    </div>
                </div>
            @endif
            <div class="bg-white pb-4">

                <div class="d-flex p-4">
                    <div class="ml-lg-auto d-flex align-items-center flex-column flex-md-row box-dat-hang">
                        <table class="table-total">
                            <tr>
                                <td>
                                    <span class="fs-24 text-gray">Tổng tiền hàng (@{{ lsObj['number'] }} sản phẩm):</span>
                                </td>
                                <td align="right">
                                    <b class="fs-34 price">@{{ formatMoney(lsObj['grandTotal']) }}</b>
                                </td>
                            </tr>
                        </table>
                        <a  href="javascript:void(0);" class="js-show-address btn-green float-right ml-3 fs-24 py-lg-3 px-lg-5">
                            ĐẶT HÀNG
                        </a>
                    </div>
                </div>
            </div>
            <form id="address-info">
                <div class="popup-dia-chi" style="display: none;">
                <input type="hidden" name="ref" value="shipping">
                <div class="h-100 d-flex justify-content-center align-items-center">
                    <div class="popup-main bg-white rounded-8 shadow">
                        <h4 class="title">ĐỊA CHỈ</h4>
                        <p class="sub-title">Để đặt hàng Quý khách vui lòng ghi địa chỉ nhận hàng</p>
                        <input type="text" name="order[full_name]" value="{{ @$info['full_name']??$info['name'] }}" placeholder="Họ & tên">
                        <input type="text" name="order[telephone]" value="{{ $info['telephone']??$info['phone'] }}" placeholder="Số điện thoại">
                        <input type="text" name="order[email]" value="{{ @$info['email'] }}" placeholder="Email">
                        <div class="group-khu-vuc">
                            <div class="form-group">
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-district','Chọn quận huyện')"
                                        class="select-search select-md city" name="order[city]">
                                    <option value="">Chọn tỉnh thành</option>
                                    @foreach($allCity as $key=>$value)
                                        <option @if(isset($info['city']['id']) && @$info['city']['id']==$value->slug) selected
                                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-town','Chọn xã phường')"
                                        id="location-district" class="district select-search"
                                        name="order[district]">
                                    <option value="">Chọn quận huyện</option>
                                    @if(@$info['district']) <option selected value="{{ @$info['district']['id'] }}">{{ @$info['district']['name'] }}</option> @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select style="width: 100%;height: 35px"
                                        id="location-town" class="town select-search"
                                        name="order[town]">
                                    <option value="">Chọn xã phường</option>
                                    @if(@$info['town']) <option  selected value="{{ @$info['town']['id'] }}">{{ @$info['town']['name'] }}</option> @endif
                                </select>
                            </div>
                        </div>
                        {{--<div class="group-khu-vuc">
                            <select class="form-control city" name="order[city]" id="city" style="margin-bottom: 20px;">
                                <option value="">Tỉnh/Thành phố</option>
                                @if(@$info['city']) <option selected value="{{ @$info['city']['id'] }}">{{ @$info['city']['name'] }}</option> @endif
                            </select>
                            <select class="form-control district" name="order[district]" id="district" style="margin-bottom: 20px;">
                                <option value="">Quận/Huyện</option>
                                @if(@$info['district']) <option selected value="{{ @$info['district']['id'] }}">{{ @$info['district']['name'] }}</option> @endif
                            </select>
                            <select class="form-control town" name="order[town]" id="town" style="margin-bottom: 20px;">
                                <option value="">Phường/Xã</option>
                                @if(@$info['town']) <option  selected value="{{ @$info['town']['id'] }}">{{ @$info['town']['name'] }}</option> @endif
                            </select>
                        </div>--}}
                        <input type="hidden" name="ref" value="shipping">
                        <input type="text" value="{{ @$info['street'] }}" name="order[street]" placeholder="Địa chỉ">
                        <div>
                            <select class="form-control dai_ly_tra_hang" name="order[dai_ly_tra_hang]" id="dai_ly_tra_hang" style="margin-bottom: 20px;">
                                <option value="">Chọn đại lý trả hàng</option>
                                @if(@$info['dai_ly_tra_hang']) <option selected value="{{ @$info['dai_ly_tra_hang']['id'] }}">{{ @$info['dai_ly_tra_hang']['name'] }}</option> @endif
                            </select>
                        </div>

                        @if(\App\Http\Models\Member::getCurrentChucDanh() != \App\Http\Models\Member::IS_CTV)
                        <div class="d-flex">
                            <p class="checkbox">
                                <input type="radio" name="pay-method" checked value="payment_vitieudung" id="check-tieudung">
                                <span class="icon"></span>
                            </p>
                            <label style="margin-top: 8px;margin-bottom: 8px;" for="check-tieudung">Thanh toán bằng ví tiêu dùng</label>
                        </div>
                        <div class="d-flex">
                            <p class="checkbox">
                                <input type="radio" name="pay-method" value="payment_khodiem" id="check-kho-diem">
                                <span class="icon"></span>
                            </p>
                            <label style="margin-top: 8px;margin-bottom: 8px;" for="check-kho-diem">Thanh toán bằng ví kho điểm</label>
                        </div>
                        @endif

                        <div class="btn-gr flex-sm-row flex-column d-flex justify-content-center align-items-center">
                            <a href="javascript:;" class="btn-light js-close-popup">TRỞ LẠI</a>
                            <a href="javascript:" class="btn-green btn-order">HOÀN THÀNH</a>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>

    </main>

@stop

@push('JS_BOTTOM_REGION')
    <script>
        <?php
        echo "var lsObj = " . json_encode($lsObj) .";";
        ?>
        $(document).ready(function () {

            select_tinh_thanh_quan_huyen($('.group-khu-vuc'), '/public-api/location/');
            $('.btn-order').click(function () {
                _POST_FORM($('#address-info'), '/checkout/save')
            })
        });
        $(function () {
            $('.fck-order-fxbot').click(function () {
                $(location).attr('href', public_link('checkout/shipping'))
            })
        })
    </script>
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/select2/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/cart/axios.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/cart/cart.js') !!}
@endpush
