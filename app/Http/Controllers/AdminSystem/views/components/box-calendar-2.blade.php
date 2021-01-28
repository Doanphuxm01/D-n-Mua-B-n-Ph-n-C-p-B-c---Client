
<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx">

        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gCalendar2" aria-expanded="true">
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
    <div id="gCalendar2" class="panel-collapse collapse in p-2" aria-expanded="true" >

    </div>
    <style>
        #gCalendar2 .fc-view > table{
            min-width: unset;
        }
        #gCalendar2  .fc-scroller {
            overflow-y: scroll;
            overflow-x: hidden;
             min-height: unset;
        }
    </style>
    <script>
        var calendarHome;
        $(function () {
            var popTemplate = [
                '<div class="popover " style="max-width:600px;border:1px solid rgb(3, 83, 103); width:380px ;" >',
                '<div class="arrow"></div>',
                '<div class="popover-header">',
                // '<button id="closepopover" type="button" class="close" aria-hidden="true">&times;</button>',
                '<h3 class="popover-title"></h3>',
                '</div>',
                '<div class="popover-content">' +
                '</div>',
                '</div>'].join('');
            var _link_popup = '{!! admin_link('/calendar/show-detail?v=1') !!}';
            var _link_update = '{!! admin_link('/calendar/quick-update?v=1') !!}';
            calendarHome = $('#gCalendar2').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'basicWeek,listDay,month'
                },
                defaultView: 'basicWeek',
                defaultDate: $.fullCalendar.moment(new Date()),
                locale: 'vi',
                weekNumbers: false,
                height: 300,
                contentHeight: 250,
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                navLinks: true,
                selectable: false,
                selectHelper: false,
                columnHeaderText: function (mom) {
                    return mom.format('dddd');
                },

                events: function (start, end, timezone, callback) {
                    $.ajax({
                        url: '{!! admin_link('/calendar/load-event') !!}',
                        dataType: 'json',
                        data: {
                            start: start.unix(),
                            end: end.unix(),
                            q_dep: {!! json_encode(@$q_dep) !!},
                            q_member: {!! json_encode(@$q_member) !!},

                        },
                        success: function (doc) {
                            $('#q_date').val($.fullCalendar.moment($('#calendar').fullCalendar('getDate')).format());

                            var events = [];

                            if (doc.status == 1) {
                                for (let i in doc.data) {
                                    let item = doc.data[i];
                                    events.push(item);
                                }
                            } else {

                            }
                            console.log(events)
                            callback(events);
                        }
                    });
                },
                eventRender: function (eventObj, el) {
                    if (eventObj.description === undefined) {
                        eventObj.description = "";
                    }
                    $(el).popover({
                        trigger: 'hover',
                        title: eventObj.title,
                        content: eventObj.content,
                        animation: true,
                        delay: 0,
                        placement: 'top',
                        container: 'body'
                    });
                },
                eventClick: function (calEvent, jsEvent, view) {
                    console.log('calEvent', calEvent)
                    _SHOW_FORM_REMOTE(_link_popup + '&id=' + calEvent._id)
                },

            });
        });

    </script>
</div>