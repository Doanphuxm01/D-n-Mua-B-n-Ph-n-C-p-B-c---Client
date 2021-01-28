@if(!empty($lsObj))
    @foreach (@$lsObj as $key => $item)
        <div class="{{ @$column }}">
            <div class="product-item">
                <a href="{{ public_link(@$item['alias'].'-p'.@$item['_id'].'.html') }}" class="img">
                    <img src="{{\App\Http\Models\Media::getImageSrc(@$item['avatar_url'])}}" alt="{{ @$item['name'] }}">
                    @if(@$item['finalPrice'] < @$item['regularPrice'])
                    <span class="sale-icon">
                    -{{ \App\Elibs\Helper::calcDiscount(@$item['finalPrice'], @$item['regularPrice']) }}%
                    </span>
                    @endif
                </a>
                <a href="{{ public_link(@$item['alias'].'-p'.$item['_id'].'.html') }}" class="name">{{ @$item['name'] }}</a>
                {{-- <span class="price">Giá bán: {{ \App\Elibs\Helper::formatMoney(@$item['regularPrice']) }}</span> --}}
                @if(@$item['finalPrice'])
                    <span class="price">Giá bán: {{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']) }}</span>
                @endif
                <div class="d-flex">
                    <div class="rate">
                <span class="rate-star">
                    <span class="star star5"></span>
                </span>
                        <span class="count-rate">({{ \App\Elibs\Helper::numberFormat(@$item['amount']) }})</span>
                    </div>
                    {{--<span class="tag-count">
                        <i class="icon icon-tag"></i>
                        2.8k
                    </span>--}}
                </div>
                <span class="uy-tin fs-11">
            <i class="icon icon-genuine"></i>
            <b class="text-theme1">UY TÍN - </b>MPGROUP
        </span>
            </div>
        </div>
    @endforeach
@endif