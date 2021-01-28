@extends($THEME_EXTEND)

@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Quản lý danh sách đơn hàng</span></h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class=""><a href="{{url('')}}">Danh sách lịch sử giao dịch</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading d-flex">
            <div class="col-md-3">
                <h3 class="panel-title"><strong>Quản lý {{@$title_module??'đơn mua điểm MPG'}}</strong></h3>
                (Tìm thấy : {{$listObj->total()}} {{@$title_module??'đơn mua điểm MPG'}})
            </div>
            @isset($moneyFilter)
                <div class="col-md-6">
                    <div class="d-flex">
                        @foreach($moneyFilter as $t => $filter)
                            <ul class="ml-4">
                                @foreach($filter as $i)
                                    <li> @if($t == 'danh_sach_so_luong_don_theo_tung_trang_thai')Tổng @elseif($t == 'danh_sach_tien_don_theo_tung_trang_thai') Tổng tiền @endif số [Giao dịch {{ \App\Http\Models\Transaction::$objectRegister[@$i['_id']]['name'] }}]: <span class="text-danger text-bold">{{ \App\Elibs\Helper::formatMoney(@$i['total']) }}</span></li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            @endisset
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        {{--<div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" id="" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    @foreach(App\Http\Models\Orders::getListStatus($q_status, App\Http\Models\Orders::ORDER_BUY_MPG) as $status)
                                        <option @if(isset($status['checked'])) selected="selected"
                                                @endif value="{{ $status['id'] }}">{{ $status['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>--}}
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
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th width="10">STT</th>
                    <th>Tài khoản nhận</th>
                    <th>Tài khoản nguồn</th>
                    <th>MPG được nhận</th>
                    <th>Cập nhật</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$val)
                    <tr id="itemRow_{{$val->id}}">
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            Tài khoản nhận: <span class="text-success text-bold">{{$val['tai_khoan_nhan']['account']}}</span>

                            @if(@$listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_INACTIVE)
                                <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                            @elseif(@$listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_ACTIVE)
                                <span class="text-success" title="Đã xác thực">[<i class="icon-check"></i>]</span>
                            @elseif(@$listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_DISABLE)
                                <span class="text-danger" title="Đã khoá">[<i class="icon-lock2"></i>]</span>
                            @endif
                            <div>
                                Họ Tên KH: {{@$val['tai_khoan_nhan']['name']}}
                            </div>
                        </td>
                        @php($nameRoute = \Request::route()->getName())
                        <td>
                            Tài khoản nguồn: <span class="text-success text-bold">{{$val['tai_khoan_nguon']['account']}}</span>

                            @if(isset($listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]) && @$listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]['status'] == \App\Http\Models\Orders::STATUS_INACTIVE)
                                <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                            @elseif(isset($listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]) && @$listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]['status'] == \App\Http\Models\Orders::STATUS_ACTIVE)
                                <span class="text-success" title="Đã xác thực">[<i class="icon-check"></i>]</span>
                            @elseif(isset($listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]) && @$listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]['status'] == \App\Http\Models\Orders::STATUS_DISABLE)
                                <span class="text-danger" title="Đã khoá">[<i class="icon-lock2"></i>]</span>
                            @endif
                            <div>
                                Họ Tên KH: {{@$listObjCustomersInCustomer[$val['tai_khoan_nguon']['account']]['name']}}
                                @if($nameRoute != 'AdminTransactionKhoDiem')
                                @if($val['tai_khoan_nguon']['account'] != $val['tai_khoan_nhan']['account'])
                                    @php($giaPha = \App\Http\Models\Customer::checkF($val['tai_khoan_nguon']['account'], $val['tai_khoan_nhan']['account']))
                                    <b class="text-primary">  [F{{$giaPha}}]</b>
                                @endif
                                @endif
                            </div>

                        </td>
                        <td>
                            Số điểm: <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val->diem_da_nhan)}}</b>
                            @if(isset($val['debt']))
                                <div>
                                    Trạng thái nợ: {{ \App\Http\Models\Orders::getStatus($val['debt'])['text'] }}
                                    @if($val['debt'] == \App\Http\Models\Orders::DEBT_YES && $val['status'] == \App\Http\Models\Orders::STATUS_NO_PROCESS)
                                        <span title="Nhấn để ghi công nợ cho {{$val['tai_khoan_nhan']['account']}}" class="text-primary cursor-pointer">
                                        <i class="icon-link" aria-hidden="true"></i></span>
                                    @endif
                                </div>
                            @endif
                            <div>
                                <a href="javascript:;"

                                   @if($nameRoute == 'AdminTransactionKhoDiem')
                                   onclick="_SHOW_FORM_REMOTE('{{admin_link('danh-sach-don-tra-hang/quick-form?id='.$val['order_id'])}}')"
                                   @else
                                   onclick="_SHOW_FORM_REMOTE('{{admin_link('orders-mpg/quick-preview?id='.$val['order_id'])}}')"
                                        @endif
                                >Xem chi tiết đơn hàng</a>
                            </div>
                        </td>
                        <td>
                            Thêm: <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'], 'd-m-Y H:i:s')}}</i>
                            {{--@todo @kayn thêm trường loại giao dịch type_giaodich --}}
                            <div>
                                Loại: {{ \App\Http\Models\Transaction::$objectRegister[$val['type_giaodich']]['name'] }}
                            </div>
                            @if($val['detail_type_giaodich'])
                                <div>
                                    <i>{{\App\Elibs\Helper::showContent($val['detail_type_giaodich'])}}</i>
                                </div>
                            @endif
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
    </script>
@stop

@section('JS_BOTTOM_REGION')
    <script>
        source = [
            {value: 'done', text: 'Đã xử lý'},
            {value: 'no_process', text: 'Chờ xử lý'},
        ]
        _EDITABLE_SELECT('.editable-status-select', 'done', source)
        _EDITABLE_TEXT('.editable-text', 'done')
    </script>
@stop
