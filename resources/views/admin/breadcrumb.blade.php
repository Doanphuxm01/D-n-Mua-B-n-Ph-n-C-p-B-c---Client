<div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">{{$_MAIN_TITLE}}</span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{url('/admin')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class=""><a href="{{$_LEVEL1['link']}}">{{$_LEVEL1['title']}}</a></li>
            @if(isset($_LEVEL2) && $_LEVEL2)
                <li><a href="{{$_LEVEL2['link']}}">{{$_LEVEL2['title']}}</a></li>
            @endif

            @if(isset($LINK_VIEW) && $LINK_VIEW)
                <li><a style="color: blue" href="{{$LINK_VIEW}}">[Xem chi tiết]</a></li>
            @endif
            
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{$EXTRA['link']}}">
                    <b><i class="icon-file-plus2"></i></b> {{$EXTRA['title']}}
                </a>
            </li>

        </ul>
    </div>