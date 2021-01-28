<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx" style="border-bottom: 0">
        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gForum" aria-expanded="true">
                <i class="icon-forrst text-primary"> </i>
                Diễn đàn
            </a>
        </h3>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
                    <a title="Xem tất cả" href="{{admin_link('/forum')}}"> <i class="icon-redo2"> </i></a>
                </li>
            </ul>

        </div>

    </div>
    <div id="gForum" class="panel-collapse collapse in" aria-expanded="true">
        <div class="panel-body no-padding">
            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                <tbody>
                @foreach($lsForum as $ks=>$val)
                    <tr class="rule">
                        <td width="100">
                            <div title="Ngày tạo" class="text-grey-600">
                                @if(\App\Elibs\Helper::showMongoDate($val['updated_at'],'d/m/Y')==date('H:i d/m/Y'))
                                    <b class="text-danger-800">Hôm nay</b>
                                @else {{\App\Elibs\Helper::showMongoDate($val['updated_at'],'H:i d/m/Y')}} @endif</div>


                        </td>
                        <td style="padding:8px 8px">
                            <a class="text-semibold"
                               href="{{admin_link('forum/detail-post?id='.$val['_id'])}}">{{$val['name']}}</a>
                            <div>
                                <span>
                                    @if(isset($val['updated_by']['email']) && $val['updated_by']['email'])
                                        {{$val['updated_by']['email']}}
                                    @else
                                        {{$val['created_by']['email']}}
                                    @endif

                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>