@php
    $allTools2 = \App\Http\Models\MetaData::getAllByTypeId(\App\Http\Models\MetaData::TOOL_EXTRA_2);
@endphp
<li class="dropdown dropdown-notification">
    <a class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon-link2"></i>
        <i class="caret"></i>
    </a>

    <ul class="dropdown-menu dropdown-menu-left tool-dropdown">
        @foreach($allTools2 as $key=>$value)

            <?php
            $dt = explode('|', $value['name']);
            ?>
            <li>
                <a target="_blank" href="{!! @trim($dt[1]) !!}">
                    <i class="fa fa-link"></i>
                    {!! @trim($dt[0]) !!}
                </a>
            </li>
        @endforeach
    </ul>
</li>
