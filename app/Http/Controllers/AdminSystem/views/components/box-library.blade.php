<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx" style="border-bottom: 0">

        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gLibrary" aria-expanded="true">
                <i class="icon-book2 text-primary"> </i>
                Tài liệu tham khảo
            </a>
        </h3>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
                    <a title="Xem tất cả" href="{{admin_link('/library')}}"> <i class="icon-redo2"> </i></a>
                </li>
            </ul>

        </div>

    </div>
    <div id="gLibrary" class="panel-collapse collapse in" aria-expanded="true">
        <div class="panel-body no-padding">
            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                <tbody>
                @foreach($lsLibrary as $ks=>$val)
                    <tr class="rule">
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

                                            <ul class="dropdown-menu dropdown-menu-left dropdown_fix">
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

                        </td>
                        <td style="padding:8px 8px">
                            <a onclick="_SHOW_FORM_REMOTE('{!! admin_link('/library/quick-view?id='.$val['_id']) !!}');return false;" href="#" >{{$val['name']}}</a>
                            <div>
                                @foreach(\App\Http\Models\MetaData::COMPANY_ISSUED as $com)
                                    @if(isset($val['company']) && $val['company'] == $com['key'])
                                        <span class="label bg-teal">{{$com['name']}}</span>
                                    @endif
                                @endforeach
                                    @foreach($dataGroup as $v)
                                        @if(isset($val['profile_type']) && is_array($val['profile_type']) && in_array($v['_id'],$val['profile_type']))
                                            <span class="label bg-success">{{$v['name']}}</span>
                                        @endif
                                    @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>