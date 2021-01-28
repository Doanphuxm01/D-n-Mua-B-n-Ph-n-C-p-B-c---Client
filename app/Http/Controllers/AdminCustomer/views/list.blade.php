@extends($THEME_EXTEND)

@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Quản lý danh sách thành viên đăng ký</span></h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class=""><a href="{{admin_link('customer')}}">Danh sách thành viên</a></li>
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('customer/view-tree')}}">
                    <b><i class="icon-file-plus2"></i></b> Xem sơ đồ hình cây
                </a>
            </li>
            <li>
                <a href="{{admin_link('customer/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm thành viên mới
                </a>
            </li>

        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Quản lý danh sách thành viên</strong></h3>
            (Tìm thấy : {{$listObj->total()}} thành viên)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" id="" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    @foreach(App\Http\Models\Customer::getListStatus($q_status) as $status)
                                        <option @if(isset($status['checked'])) selected="selected"
                                                @endif value="{{ $status['id'] }}">{{ $status['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       {{-- <div class="form-group no-margin">
                            <div class="content-group">
                                <input name="actived_at" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóads">
                                <input name="q" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóasd">
                            </div>
                        </div>--}}
                        <div class="input-group content-group">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Lọc</button>
                                <button
                                type="button"
                                onclick="_SHOW_FORM_REMOTE('{{admin_link('/customer/export_popup')}}')"
                                title="Xuất toàn bộ kết quả tìm được"
                                class="btn btn btn-primary bg-info-800 btn-sm ml-1">
                                Xuất excel
                            </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th width="10">STT</th>
                    <th width="60"></th>
                    <th>Họ tên/Addr</th>
                    <th>Phone/Email</th>
                    <th>Cập nhật</th>
                    <th>Kích hoạt</th>
                    <th width="150" class="text-right">Chức năng</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$val)
                    <tr id="itemRow_{{$val->id}}">
                        <td>
                            {{$key+1}}
                        </td>
                        <td>

                        </td>
                        <td>
                            <b>{{$val->name}}</b>
                            <div>
                                Tên đăng nhập: <i>{{$val->account}}</i>
                            </div>
                        </td>
                        <td>
                            Phone: <b>{{$val->phone}}</b>
                            @if(isset($val->verified['phone']) && $val->verified['phone']=='true')
                                <span class="text-success"><i class="icon-check"></i></span>
                            @else
                                <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                            @endif
                            <div>Email: @if($val['email'])<i>{{$val['email']}}</i>
                                @if(isset($val->verified['email']) && $val->verified['email']=='true')
                                    <span class="text-success"><i class="icon-check"></i></span>
                                @else
                                    <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                                @endif
                                @else <span class="text-danger-800">Chưa có email</span> @endif</div>
                        </td>
                        <td>
                            Thêm: <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'])}}</i>
                            <div>
                                Cập nhật: <i>{{\App\Elibs\Helper::showMongoDate($val['updated_at'])}}</i>
                            </div>
                        </td>
                        <td>
                            Kích hoạt: <i>{{\App\Elibs\Helper::showMongoDate($val['actived_at'])}}</i>
                            <div>
                                Kết thúc: <i>{{\App\Elibs\Helper::showMongoDate($val['end_at'])}}</i>
                            </div>
                        </td>

                        <td class="text-right">
                            <span style="margin-bottom: 10px; display: inline-block; cursor: pointer;" data-type="select"
                                  data-url="{{ admin_link('customer/_update_status?token='.\App\Elibs\Helper::buildTokenString($val['_id'])) }}" data-pk="{{ $val['_id'] }}"
                                  data-title="{{ \App\Http\Models\Customer::getStatus($val['status'])['text'] }}" data-placement="left" data-popup="tooltip" title="Click cập nhật trạng thái"
                                  data-value="{{ $val['status'] }}"
                                  class="editable-status-select text-bold text-{{ \App\Http\Models\Customer::getStatus($val['status'])['style'] }}">
                            </span>
                            <ul class="icons-list">
                                <li class="text-primary-600">
                                    <a href="{{admin_link('customer/input?id='.$val['_id'])}}" title="Sửa"
                                    ><i class="icon-pencil7"></i>
                                    </a>
                                </li>
                                {{--<li class="text-danger-600">
                                    <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                       onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($val,'customer')}}','{{$val['_id']}}')">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>--}}
                            </ul>
                        </td>
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
        source = [
            {value: 'active', text: 'Đã kích hoạt'},
            {value: 'inactive', text: 'Chờ kích hoạt'},
            {value: 'disabled', text: 'Khóa'},
        ]
        _EDITABLE_SELECT('.editable-status-select', 'inactive', source)
    </script>
@stop
