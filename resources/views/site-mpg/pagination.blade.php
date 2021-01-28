
<div class="paging-box d-flex align-items-center flex-column flex-md-row">
    <span class="count">
        Đang hiển thị <strong>{!! ($paginator->total() > $paginator->perPage()) ? $paginator->perPage() : $paginator->total()  !!}</strong> trên tổng số <strong>{!! $paginator->total() !!}
        </strong> sản phẩm</span>
    @if (isset($paginator) && $paginator->lastPage() > 1)
    <ul class="paging d-flex mx-auto align-items-center">
        <?php
        $interval = isset($interval) ? abs(intval($interval)) : 3 ;
        $from = $paginator->currentPage() - $interval;
        if($from < 1){
            $from = 1;
        }
        $to = $paginator->currentPage() + $interval;
        if($to > $paginator->lastPage()){
            $to = $paginator->lastPage();
        }
        ?>
        <!-- first/previous -->
        @if($paginator->currentPage() > 1)
            <li >
                <a class="prev" href="{{ $paginator->url(1) }}" aria-label="First">
                    <img src="{{asset('mpg-tmp/giaodienmuahang/images/ic_arrow-right.png')}}" alt="">
                </a>
            </li>
        @endif
        <!-- links -->
        @for($i = $from; $i <= $to; $i++)
            <?php 
            $isCurrentPage = $paginator->currentPage() == $i;
            ?>
            <li >
                <a class="{{ $isCurrentPage ? 'item active' : 'item' }}" href="{{ !$isCurrentPage ? $paginator->url($i) : '#' }}">
                    {{ $i }}
                </a>
            </li>
        @endfor
        <!-- next/last -->
        @if($paginator->currentPage() < $paginator->lastPage())
            <li >
                <a href="#" class="next">
                    <img src="{{asset('mpg-tmp/giaodienmuahang/images/ic_arrow-right.png')}}" alt="">
                </a>
            </li>
        @endif
    </ul>
@endif
</div>