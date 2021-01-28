<?php
$tab = app('request')->input('tab', 'info');
$temp = admin_link('staff/input?id=' . app('request')->input('id', ''));
$tabs_components_data = [
    //"account" => ['name' => "Tài khoản", "href" => $temp . "&tab=account"],
    "info" => ['name' => "Thông tin cơ bản", "href" => $temp . "&tab=info"],
    "work" => ['name' => "Thông tin công việc", "href" => $temp . "&tab=work"],
    "edu" => ['name' => "Thông tin đào tạo", "href" => $temp . "&tab=edu"],
    "family" => ['name' => "Thông tin gia đình", "href" => $temp . "&tab=family"],
    //"role" => ['name' => "Phân quyền", "href" => $temp . "&tab=role"],
    "files" => ['name' => "Các file đã tải lên", "href" => $temp . "&tab=files"],
]

?>
<ul class="nav nav-tabs  " style="margin-bottom:0;margin-left:4px">
    @if(!app('request')->input('id',''))
        @foreach(["info" => ['name' => "Thông tin cơ bản", "href" => $temp . "&tab=info"],] as $key=>$item)
            <li class="{{$tab == $key ? 'active': ''}}"><a

                        {{--@if($key !=='account')--}}
                        {{--disabled="disabled"--}}
                        {{--onclick="alert ('Đang hoàn thiện...')"--}}
                        {{--@else--}}
                        href="{{$item['href']}}"
                        {{--@endif--}}
                >
                    {{$item['name']}}</a>
            </li>
        @endforeach
    @else
        @foreach($tabs_components_data as $key=>$item)
            <li class="{{$tab == $key ? 'active': ''}}"><a

                        {{--@if($key !=='account')--}}
                        {{--disabled="disabled"--}}
                        {{--onclick="alert ('Đang hoàn thiện...')"--}}
                        {{--@else--}}
                        href="{{$item['href']}}"
                        {{--@endif--}}
                >
                    {{$item['name']}}</a>
            </li>
        @endforeach
    @endif
</ul>
<style>
    @media screen and (max-width:600px){
        .modal {
            display: none;
            overflow: hidden;
            position: fixed;
            top: 15%;
            right: 0;
            bottom: 0;
            left: 0;
            margin-left: auto;
            margin-right: auto;
            width: 94%;
            z-index: 1050;
            -webkit-overflow-scrolling: touch;
            outline: 0;
        }
    }

</style>