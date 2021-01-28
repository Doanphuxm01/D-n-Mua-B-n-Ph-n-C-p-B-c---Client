@php
    $listNotif = \App\Http\Models\Msg::getCurrentMemberNotif();
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

    .msg-item-area {
        background-color: #edf2fa;
    }

    .msg-item-area.is-read {
        background-color: white;
    }

    .msg-item-area:hover {
        background-color: #e4e9f1;
    }


</style>
@php
    $currentMemberId = \App\Http\Models\Member::getCurentId();
@endphp
<li class="dropdown dropdown-notification">
    <a class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon-comments"></i>
        <span> <span
                    class="badge badge-primary"
                    id="msg-count">{{count(collect($listNotif->toArray()['data'])->filter(function ($item) {
                        $is_read = (isset ($item['read_by']) && is_array($item['read_by']) && in_array(strval(\App\Http\Models\Member::getCurentId()), $item['read_by']));
                return !$is_read;
                    }))}}</span></span>
        <i class="caret"></i>
    </a>

    <ul style="max-height: 450px;overflow-y: scroll;padding:0px; padding-top:5px"
        class="dropdown-menu dropdown-menu-right media-list dropdown-content-body width-450 ">
        <div class="px-2 py-2 text-black bg-white"><a href="{{admin_link('msg')}}">Các tin nhắn</a>
            <a class="pull-right" onclick="_SHOW_FORM_REMOTE('{{admin_link('msg/quick-input')}}')">
                <b><i class="icon-reply"></i></b> Gửi tin nhắn
            </a>
        </div>
        @foreach( $listNotif as $item)
            @php
                $is_read = (isset ($item['read_by']) && is_array($item['read_by']) && in_array(strval(\App\Http\Models\Member::getCurentId()), $item['read_by']));
            @endphp

            <li class="cursor-pointer border-grey py-2 px-2 msg-item-area @if($is_read) is-read @endif"
                id="notif-{{$item['_id']}}">
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
                <div
                        onclick="_SHOW_FORM_REMOTE('{{admin_link('msg/quick-input?id='.$item['_id'])}}')"

                        class="media-body">

                    <b class="text-bold">{{$item['sender']['name']}}</b><a
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
                                  {{$item['content']['brief']}}@endif">{{$item['content']['title']}}:
                                {{$item['content']['brief']}}
                            </span>


                        @endif
                    </a>

                    <div>
                        @if( @$item['replies'] && count(@$item['replies']))
                            @php
                                $replier=  @collect($item['replies'])->last()['created_by'];
                                $replier = $replier ['name'] ? $replier['name'] : $replier['account'] ;
                            @endphp
                            <i><u> {{$replier}} </u></i> đã phản hồi

                        @endif
                    </div>
                    <div class="text-size-small text-grey" class="notif-date">
                        @if(isset($item['send_at']))
                            <i class="text-size-mini">{{\App\Elibs\Helper::showMongoDate($item['send_at'])}}</i>
                        @endif

                        @if(!$is_read)
                            <span class="msg-read text-grey cursor-pointer "
                                  data-value="{{$item['_id']}}"
                            ><i
                                        class="icon-check"></i>
                        Đánh dấu đã đọc</span>
                        @endif
                    </div>

                </div>

            </li>
        @endforeach
        <div class="px-2 py-2 text-black bg-white text-center"><a
                    href="{{admin_link('/msg/')}}">Xem tất cả</a>
        </div>
    </ul>
    <script>
        $(document).on('click', '.dropdown.dropdown-notification', function (e) {
            e.stopPropagation();
        });

        function notif_mark_read(id) {
            $(`#msg-${id}`).find('.notif-read').remove();
            $(`#msg-${id}`).addClass('is-read');
            $(`#msg-count`).html($(`.notif-read`).length)
        }


        $('.msg-read').on('click', function (e) {
            e.stopPropagation();

            let id = $(this).data('value')
            notif_mark_read(id);

            $.ajax({
                url: `{{admin_link('/msg/toggle-read')}}`,
                data: {id: id, read: 1},
                success: data => {
                    console.log(data)
                }
            })
        })
    </script>
</li>
