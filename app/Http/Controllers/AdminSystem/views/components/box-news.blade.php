<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx">

        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gNews" aria-expanded="true">
                <i class="icon-newspaper text-success"></i>
                Tin tức gần đây
            </a>
        </h3>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a title="Xem tất cả" href="{{admin_link('/news')}}"> <i class="icon-redo2"></i></a></li>
            </ul>

        </div>

    </div>
    <div id="gNews" class="panel-collapse collapse in" aria-expanded="true">
        <div class="panel-body no-padding">

            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-bottom mb-0" style="display: flex;border-bottom: 0">
                    <li class="active"><a href="#calendar-today"
                                          onclick="$('#new-tab-others').hide().load('{{admin_link('news?&options[view]=mini')}}').fadeIn(500)"
                                          data-toggle="tab" aria-expanded="true">Thông tin
                            chung</a>
                        @if(isset($lsNews) && $lsNews && !$lsNews->isEmpty())
                            <?php
                            $countTemp = $lsNews->filter(function ($item) {
                                return \App\Elibs\Helper::showMongoDate($item['updated_at'], 'd/m/Y') == date('d/m/Y');
                            })->count();
                            ?>
                            @if($countTemp)
                                <span class="badge bg-danger-400 badge-count">{{$countTemp}}</span>
                            @endif
                        @endif

                    </li>
                    <?php
                    $allDepByMember = $allDepByMember->sortBy('name');

                    ?>
                    @foreach($allDepByMember->slice(0,2) as $item)
                        <li
                                id="box-new-nav={{$item['_id']}}"
                        ><a

                                    onclick="$('#new-tab-others').hide().load('{{admin_link('news?q_dep[]='.$item->_id.'&options[view]=mini')}}').fadeIn(500)"
                                    href="#new-tab-others" data-toggle="tab" aria-expanded="true">{{$item->name}}</a>
                            @if(isset($lsNews) && $lsNews && !$lsNews->isEmpty())
                                <?php
                                $countTemp = $lsNews->filter(function ($itemNews)use($item) {
                                    return @$item['_id'] == @$itemNews['departments']['id']  && \App\Elibs\Helper::showMongoDate($itemNews['updated_at'], 'd/m/Y') == date('d/m/Y');
                                })->count();
                                ?>
                                @if($countTemp)
                                    <span class="badge bg-danger-400 badge-count">{{$countTemp}}</span>
                                @endif
                            @endif


                        </li>
                    @endforeach
                    @if(count($allDepByMember)>1)
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Thêm
                            nữa <span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">

                            @foreach($allDepByMember->slice(2) as $item)
                                <li
                                        id="box-new-nav={{$item['_id']}}"
                                ><a href="#new-tab-others" data-toggle="tab"
                                    onclick="$('#new-tab-others').hide().load('{{admin_link('news?q_dep[]='.$item->_id.'&options[view]=mini')}}').fadeIn(500)"
                                    aria-expanded="true">{{$item->name}}</a>
                                    @if(isset($lsNews) && $lsNews && !$lsNews->isEmpty())
                                        <?php
                                        $countTemp = $lsNews->filter(function ($itemNews)use($item) {
                                            return @$item['_id'] == @$itemNews['departments']['id']  && \App\Elibs\Helper::showMongoDate($itemNews['updated_at'], 'd/m/Y') == date('d/m/Y');
                                        })->count();
                                        ?>
                                        @if($countTemp)
                                            <span class="badge bg-danger-400 badge-count">{{$countTemp}}</span>
                                        @endif
                                    @endif

                                </li>

                            @endforeach

                        </ul>
                    </li>
                    @endif
                </ul>
                <div class="tab-content pb-10" style="max-height: 500px;overflow: auto">
                    <div class="tab-pane active" id="news-tabs-default">
                        @if(isset($lsNews) && $lsNews)
                            {{--@foreach($lsNews as $key=>$value)--}}
                            @if(!$lsNews->isEmpty())
                                <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                    <tbody>
                                    @foreach($lsNews->slice(0,10) as $ks=>$vs)
                                        <tr class="rule">
                                            <td width="100">
                                                    <span title="Ngày tạo" class="text-grey-600">
                                                         @if(\App\Elibs\Helper::showMongoDate($vs['updated_at'],'d/m/Y')==date('d/m/Y'))
                                                            <b class="text-danger-800">Hôm nay</b>
                                                        @else {{\App\Elibs\Helper::showMongoDate($vs['updated_at'],'d/m/Y')}} @endif</span>


                                            </td>

                                            <td style="padding:8px 8px">

                                                <a onclick="_SHOW_FORM_REMOTE('{{admin_link('/news/show-detail?id='.$vs['_id'])}}')"
                                                   title="Xem thông tin"> {{$vs['name']}}</a>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-bold">{{@$allDepByMemberKeyById[@$vs['departments']['id']]['name']}}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>


                            @endif
                            {{--@endforeach--}}
                        @else
                            <div class="text-center"><i class="fa fa-info"></i> Chưa có tin tức mới</div>
                        @endif
                    </div>
                    <div class="tab-pane" id="new-tab-others">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>