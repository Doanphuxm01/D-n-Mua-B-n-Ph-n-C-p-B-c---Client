<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx">

        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gCalendar" aria-expanded="true">
                <i class="icon-calendar text-danger"></i>
                Lịch làm việc
            </a>
        </h3>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a title="Xem tất cả" href="{{admin_link('/calendar')}}"> <i class="icon-redo2"></i></a>
                </li>
            </ul>

        </div>

    </div>
    <div id="gCalendar" class="panel-collapse collapse in" aria-expanded="true">
        <div class="panel-body no-padding">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-bottom mb-0">
                    <li class="active"><a href="#calendar-today" data-toggle="tab" aria-expanded="true">Hôm
                            nay</a>
                        @if(isset($lsCalendarToDay) && $lsCalendarToDay && !$lsCalendarToDay->isEmpty())
                            <span class="badge bg-danger-400 badge-count">{{$lsCalendarToDay->count()}}</span>
                        @endif
                    </li>
                    <li><a href="#calendar-this-week" data-toggle="tab" aria-expanded="true">7 Ngày tới </a>
                        @if(isset($lsCalendarNext) && $lsCalendarNext && !$lsCalendarNext->isEmpty())
                            <span class="badge bg-danger-400 badge-count">{{$lsCalendarNext->count()}}</span>
                        @endif
                    </li>
                </ul>
                <div class="tab-content pb-10" style="max-height: 500px;overflow: auto">
                    <div class="tab-pane active" id="calendar-today">
                        @if(isset($lsCalendarToDay) && $lsCalendarToDay && !$lsCalendarToDay->isEmpty())
                            <table class="table table-striped table-io">
                                <tbody>
                                @foreach($lsCalendarToDay as $key=>$val)
                                    <tr id="itemRow_{{$val->id}}">
                                        <td width="150">
                                            <i>{{\App\Elibs\Helper::showDate($val['started_at'],'H:i d/m/Y')}}</i>
                                            <div>
                                                <i>{{\App\Elibs\Helper::showDate($val['ended_at'],'H:i d/m/Y')}}</i>
                                            </div>
                                        </td>
                                        <td>
                                            <a class="text- text-default"
                                               onclick="_SHOW_FORM_REMOTE('{{admin_link('/calendar/show-detail?id='.$val->_id)}}')">{{$val->title}}</a>
                                            <div>
                                                @if(!isset($val['departments']) || !$val['departments'])
                                                    <span class="text-muted">Lịch chung</span>
                                                @else
                                                    @foreach($val['departments'] as $item)
                                                        <span class="text-muted">{{$item['name']}}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center pt-10 pl-10 pr-10">Hôm nay không có lịch phát sinh</div>
                        @endif
                    </div>
                    <div class="tab-pane" id="calendar-this-week">
                        @if(isset($lsCalendarNext) && $lsCalendarNext && !$lsCalendarNext->isEmpty())
                            <table class="table table-striped table-io">
                                <tbody>
                                @foreach($lsCalendarNext as $key=>$val)
                                    <tr id="itemRow_{{$val->id}}">
                                        <td width="150">
                                            <i>{{\App\Elibs\Helper::showDate($val['started_at'],'H:i d/m/Y')}}</i>
                                            <div>
                                                <i>{{\App\Elibs\Helper::showDate($val['ended_at'],'H:i d/m/Y')}}</i>
                                            </div>
                                        </td>
                                        <td>
                                            <a class="text- text-default"
                                               onclick="_SHOW_FORM_REMOTE('{{admin_link('/calendar/show-detail?id='.$val->_id)}}')">{{$val->title}}</a>
                                            <div>
                                                @if(!isset($val['departments']) || !$val['departments'])
                                                    <span class="text-muted">Lịch chung</span>
                                                @else
                                                    @foreach($val['departments'] as $item)
                                                        <span class="text-muted">{{$item['name']}}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center pt-10 pl-10 pr-10">Chưa có lịch phát sinh trong 7 ngày
                                tới
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>