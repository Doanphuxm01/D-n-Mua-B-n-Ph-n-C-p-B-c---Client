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
                <a href="{{admin_link('customer')}}">
                    <b><i class="icon-file-plus2"></i></b> Xem danh sách chi tiết
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
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {!! $html !!}
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
