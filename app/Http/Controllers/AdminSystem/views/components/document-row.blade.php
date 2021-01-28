<tr class="rule">

    <td style="padding:8px 8px">
        @if(\App\Elibs\Helper::showMongoDate($val['created_at'],'d/m/Y')==date('d/m/Y'))
            <b class="text-danger-800">Hôm nay</b>
        @else {{\App\Elibs\Helper::showMongoDate($val['created_at'],'d/m/Y')}}
        @endif

        <a onclick="_SHOW_FORM_REMOTE('{!! admin_link('/document/quick-view?id='.$val['_id']) !!}');return false;"
           title="Xem thông tin"> {{$val['name']}}</a>

        <div>
            @foreach($dataGroup as $k=>$meta)
                @if(isset($val['doc_type']) && $val['doc_type'] == $meta['_id'])
                    <span class="label bg-primary">{{$meta['name']}}</span>
                    @break
                @endif
            @endforeach
            @if(isset($allProject[$val['project']]) && $allProject[$val['project']])
                <span class="label bg-teal-400"> {{$allProject[$val['project']]['name']}}</span>
            @endif
            <span style="float: right;">
                @if(isset($val['files']) && $val['files'] && is_array($val['files']))
                    @if(count($val['files'])>1)
                        <div class="btn-group">
                    <a href="#" class="label bg-teal-400 dropdown-toggle"
                       data-toggle="dropdown">{{count($val->files)}} File đính kèm<span
                                class="caret"> </span></a>

                    <ul class="dropdown-menu dropdown-menu-right dropdown_fix">
                        @foreach($val->files as $item)
                            <li><a style="max-width: 250px; overflow: hidden;text-overflow:ellipsis;" target="_blank" href="{{\App\Http\Models\Media::getFileLink($item)}}"> <i
                                            class="icon-link"> </i> {{$item}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                    @else
                        <a class="label label-info" href="{{\App\Http\Models\Media::getFileLink($val['files'][0])}}" target="_blank">
                    <i class="icon-share2"> </i> Xem file
                </a>
                    @endif
                @else
                    <span class="label label-warning">Không có file</span>
                @endif
            </span>
        </div>
        <div>
            Số: <b class="text-green-800">{{$val['num_doc']}}</b>,
            @foreach($allDepartments as $derpart)
                @if(isset($val['department']) && $val['department'] == $derpart['_id'])
                    <b>{{$derpart['name']}}</b>
                @endif
            @endforeach

        </div>
    </td>
</tr>
{{--
<td width="100">
    <div title="Ngày tạo" class="text-grey-600">
        @if(\App\Elibs\Helper::showMongoDate($val['created_at'],'d/m/Y')==date('d/m/Y'))
            <b class="text-danger-800">Hôm nay</b>
        @else {{\App\Elibs\Helper::showMongoDate($val['created_at'],'d/m/Y')}} @endif</div>

    <div>
        @if(isset($val['files']) && $val['files'] && is_array($val['files']))
            @if(count($val['files'])>1)
                <div class="btn-group">
                    <a href="#" class="label bg-teal-400 dropdown-toggle"
                       data-toggle="dropdown">{{count($val->files)}} File đính kèm<span
                                class="caret"> </span></a>

                    <ul class="dropdown-menu dropdown-menu-left">
                        @foreach($val->files as $item)
                            <li><a style="max-width: 250px; overflow: hidden;text-overflow:ellipsis;" target="_blank" href="{{\App\Http\Models\Media::getFileLink($item)}}"> <i
                                            class="icon-link"> </i> {{$item}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <a class="label label-info" href="{{\App\Http\Models\Media::getFileLink($val['files'][0])}}" target="_blank">
                    <i class="icon-share2"> </i> Xem file
                </a>
            @endif
        @else
            <span class="label label-warning">Không có file</span>
        @endif
    </div>


</td>--}}
