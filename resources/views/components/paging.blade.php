<style>
    .navigation .nav-previous {
        float: left;
        width: 49%;
        text-align: left;
    }
    .navigation .nav-next {
        width: 49%;
        float: right;
        text-align: right;
    }
    .nav-links a {
        background: #37474f;
        padding: 5px 10px;
        display: inline-block;
        color: #fff;
    }

    .navigation {
        overflow: hidden;
        width: 100%;
        border-radius: 5px;
    }
</style>
<?php
$next_link = '';
$previous_link = '';
$params_get = $_GET;
unset($params_get['page']);
if ($page > 1 && $count == $itemPerPage) {
    $params_get['page'] = $page + 1;
    $next_link = url()->current() . '?' . http_build_query($params_get);
    if ($page > 2) {
        $params_get['page'] = $page - 1;
    } else {
        unset($params_get['page']);
    }
    $previous_link = url()->current() . '?' . http_build_query($params_get);
} else if ($page == 1 && $count == $itemPerPage) {
    $params_get['page'] = $page + 1;
    $next_link = url()->current() . '?' . http_build_query($params_get);
} else if ($page > 1 && $count < $itemPerPage) {
    $params_get['page'] = $page - 1;
    $previous_link = url()->current() . '?' . http_build_query($params_get);
}
?>
@if($next_link || $previous_link)
<div class="paging-box d-flex align-items-center flex-column flex-md-row">

    <nav class="navigation" role="navigation">
        <div class="nav-links">
            @if($previous_link)
                <div class="nav-previous fr"><a href="{!! $previous_link !!}"><i class="fa fa-arrow-circle-left mr-1"></i> Previous Page </a></div>
            @endif
            @if($next_link)
                @if(!isset($max_page) || $max_page<=$page)
                    <div class="nav-next fr"><a href="{!! $next_link !!}"> Next Page <i class="fa fa-arrow-circle-right ml-1"></i></a></div>
                @endif
            @endif
        </div>
    </nav>
</div>
@endif
