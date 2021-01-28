<?php

?>
<h2>Thông báo TEXO: {{@$obj['content']['title']}}</h2>
<p>
    Người gửi: {{@$obj['sender']['name']}}
</p>

@if(isset($obj['ref_obj']['link']))
    <div>
        Xem liên kết: <a href="{{$obj['ref_obj']['link']}}">{{$obj['ref_obj']['name']}}</a>
    </div>
@endif
<p>
    {{@$obj['content']['brief']}}

</p>
