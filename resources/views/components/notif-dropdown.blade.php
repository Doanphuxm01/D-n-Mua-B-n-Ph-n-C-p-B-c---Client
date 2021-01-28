@php
    $listNotif = \App\Http\Models\Notification::getCurrentMemberNotif();
@endphp
<style>
    .notif-title {
        white-space: nowrap;
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .notif-date:hover .notif-read {
        display: inline-block;
    }

    .notif-date.notif-read {
        display: none;
    }

    .notif-item-area {
        background-color: #edf2fa;
    }

    .notif-item-area.is-read {
        background-color: white;
    }

    .notif-item-area:hover {
        background-color: #e4e9f1;
    }


</style>

<li class="dropdown dropdown-notification">
    <a class="dropdown-toggle" data-toggle="dropdown">
        <i class=" icon-bell3"></i>
        <span> <span
                    class="badge badge-primary"
                    id="notif-count">{{count(collect($listNotif->toArray()['data'])->filter(function ($item){return !@$item['read_at'];}))}}</span></span>
        <i class="caret"></i>
    </a>

    <ul style="max-height: 450px;overflow-y: scroll;padding:0px; padding-top:5px"
        class="dropdown-menu dropdown-menu-right media-list dropdown-content-body width-450 ">
        <div class="px-2 py-2 text-black bg-white"> Các thông báo
            <a href="javascript:void(0)" onclick="notif_mark_read_all()" class="pull-right">Đánh dấu tất cả là đã
                đọc</a>
        </div>
        @foreach( $listNotif as $item)
            <li class="notif-read border-grey py-2 px-2 notif-item-area @if(isset($item['read_at']) && $item['read_at'])is-read @endif"
                data-value="{{@$item['_id']}}"
                id="notif-{{@$item['_id']}}">
                <div class="media-left">
                    @if(isset($item['sender']['avatar_url']))
                        <div
                                style="border-radius:100%; height: 50px;width:50px;background-position: center;background-repeat: no-repeat;background-size: contain ;display: flex;align-items: center ;background-image: url('{{$avatar}}')"
                        ></div>
                    @else
                        <div
                                style="border-radius:100%; height: 50px;width:50px;background-position: center;background-repeat: no-repeat;background-size: contain ;display: flex;align-items: center ;background-image: url('/images/no-avatar.png')"
                        >
                        </div>
                    @endif
                </div>
                <div class="media-body">
                    <b class="text-bold">{{@$item['sender']['name']}}</b><a
                            @if(isset($item['ref_obj']['link']))
                            href="{{$item['ref_obj']['link']}}"
                            @else
                            href="#"
                            @endif >
                        @if(isset($item['content']['title']) && @isset($item['sender']['name']))
                            <span class="text-grey-800"
                                  data-trigger="hover"
                                  data-placement="bottom"
                                  data-popup="tooltip"
                                  title="@if(isset($item['content']['brief']))
                                  {{$item['content']['brief']}}@endif"> {{$item['content']['title']}}</span>
                        @endif
                    </a>


                    <div class="text-size-small text-grey" class="notif-date">
                        @if(isset($item['send_at']))
                            <i class="text-size-mini">{{\App\Elibs\Helper::showMongoDate($item['send_at'],'H:i:s d/m/Y')}}</i>
                        @endif
                        @if(!@$item['read_at'])
                            <span class="notif-read text-grey cursor-pointer "
                                  data-value="{{@$item['_id']}}"
                            ><i
                                        class="icon-check"></i>
                        Đánh dấu đã đọc</span>
                        @endif
                    </div>

                </div>

            </li>
        @endforeach
        <div class="px-2 py-2 text-black bg-white text-center"><a
                    href="{{admin_link('/notification/my-notif')}}">Xem tất cả</a>
        </div>
    </ul>
    <script>
        $(document).on('click', '.dropdown.dropdown-notification', function (e) {
            e.stopPropagation();
        });

        function notif_mark_read(id) {
            $(`#notif-${id}`).find('.notif-read').remove();
            $(`#notif-${id}`).addClass('is-read');
            $(`#notif-count`).html($(`.notif-read`).length)
        }


        $('.notif-read').on('click', function (e) {
            e.stopPropagation();

            let id = $(this).data('value')
            notif_mark_read(id);

            $.ajax({
                url: `{{admin_link('/notification/toggle-read')}}`,
                data: {id: id, read: 1},
                success: data => {
                    console.log(data)
                }
            })
        })

        function notif_mark_read_all() {
            let listid = {!! $listNotif->filter(function ($item){return !@$item['read_at'];})->pluck('_id')->toJson() !!};
            listid.forEach(id => {
                $.ajax({
                    url: `{{admin_link('/notification/toggle-read')}}`,
                    data: {id: id, read: 1},
                    success: data => {
                        console.log(data)
                    }
                });
                notif_mark_read(id)
            });
            $('#notif-count').text(0)
        }

    </script>
</li>
