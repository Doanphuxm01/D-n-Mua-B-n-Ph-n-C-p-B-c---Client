@extends($THEME_EXTEND)

@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Các thông báo của tôi</span>
            </h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('notification')}}">Các thông báo của tôi</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            {{--<li>--}}
            {{--<a href="{{admin_link('notification/input')}}">--}}
            {{--<b><i class="icon-file-plus2"></i></b> Thêm thông báo mới--}}
            {{--</a>--}}

            {{--</li>--}}
            <li>
                <a onclick="_SHOW_FORM_REMOTE('{{admin_link('notification/quick-input')}}')">
                    <b><i class="icon-file-plus2"></i></b> Gửi thông báo
                </a>
            </li>

        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Quản lý danh sách thông báo</strong></h3>
            (Tìm thấy : {{$listObj->total()}} thông báo)

            <div class="heading-elements">


                <div class="input-group-btn">

                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle  " style="width:150px" type="button"
                                data-toggle="dropdown">Thao tác
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">

                            <li><a href="#" onclick="_read_notif()"><i class="icon-reading"></i> Đánh dấu đọc</a></li>
                            <li><a href="#" onclick="_remove_notif()"><i class="icon-trash"></i> Xoá thông báo</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th width="1"></th>
                    <th width="10">STT</th>
                    <th width="200">Tiêu đề</th>
                    <th width="300">Nội dung</th>
                    <th width="10">Liên kết</th>
                    <th width="50">Người tạo</th>
                    <th width="50">Thời gian</th>
                    <th width="1" class="text-right">Đã đọc</th>
                    {{--<th width="150" class="text-right">Chức năng</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$val)
                    <tr id="itemRow_{{$val['_id']}}">
                        <td>
                            <input type="checkbox" class="checker check_id" value="{{$val['_id']}}" name="listid[]">
                        </td>
                        <td>
                            {{$key+1}}
                        </td>

                        <td>
                            <a onclick="_SHOW_FORM_REMOTE('{{admin_link('notification/quick-input?id='.$val['root_id'])}}')">
                                {{@$val['content']['title']}}
                            </a>

                        </td>
                        <td>
                            {{@$val['content']['brief']}}
                        </td>
                        <td>
                            @if(isset($val['ref_obj']))
                                <a href="{{$val['ref_obj']['link']}}" target="_blank"
                                   data-toggle="tooltip"
                                   data-trigger="hover"
                                   title="{{@$val['ref_obj']['name']}}"
                                >Xem liên kết</a>
                            @endif
                        </td>
                        <td>
                            {{@$val['sender']['name']}}
                        </td>
                        <td>
                            <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'])}}</i>
                        </td>
                        <td class="text-right">
                            <input type="checkbox"
                                   @if(isset($val['read_at']) && $val['read_at'])
                                   checked
                                   @endif
                                   onchange="toggleRead('{{$val['_id']}}')" class="checker">
                        </td>

                        {{--<td class="text-center">--}}
                        {{--<ul class="icons-list">--}}
                        {{--<li class="text-primary-600">--}}
                        {{--<a href="{{admin_link('notification/input?id='.$val['_id'])}}" title="Sửa"--}}
                        {{--><i class="icon-pencil7"></i>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<li class="text-danger-600">--}}
                        {{--<a href="javascript:void(0);" data-popup="tooltip" title="Xóa"--}}
                        {{--onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Notification::buildLinkDelete($val,'notification')}}','{{$val['_id']}}')">--}}
                        {{--<i class="icon-trash"></i>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--</ul>--}}
                        {{--</td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="panel-body">
            @if(!$listObj->count())
                <div class="alert alert-danger alert-styled-left alert-bordered">
                    Không tìm thấy dữ liệu nào ở trang này. (Hãy kiểm tra lại các điều kiện tìm kiếm hoặc
                    phân trang...)
                </div>
            @endif
            <div class="text-center pagination-rounded-all">{{ $listObj->render() }}</div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });

        function toggleRead(id) {
            $.ajax({
                url: `{{admin_link('/notification/toggle-read')}}`,
                data: {id: id}
            })
        }

        function _remove_notif() {

            let id = [];
            $('input.check_id').each(function () {
                let $this = $(this);
                if ($this.prop('checked')) {
                    id.push($this.val())
                }
            });
            $.ajax({
                url: `{{admin_link('/notification/remove_multi')}}`,
                data: {id: id}
            }).done((data) => {
                alert(data.msg)
                if (data.status === 1) {
                    location.reload()
                }
            }).fail(function () {
                alert("Lỗi kết nối");
            })

        }

        function _read_notif() {

            let id = [];
            $('input.check_id').each(function () {
                let $this = $(this);
                if ($this.prop('checked')) {
                    id.push($this.val())
                }
            });
            $.ajax({
                url: `{{admin_link('/notification/toggle-read-multi')}}`,
                data: {id: id}
            }).done((data) => {
                alert(data.msg)
                if (data.status === 1) {
                    location.reload()
                }
            }).fail(function () {
                alert("Lỗi kết nối");
            })

        }

        // $('input[type=checkbox].styled').uniform()
    </script>
@stop
