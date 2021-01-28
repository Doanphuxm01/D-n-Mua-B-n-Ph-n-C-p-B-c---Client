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
            <li class=""><a href="{{admin_link('customer')}}">Danh sách lịch sử giao dịch</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading d-flex">
            <div class="col-md-3">
                <h3 class="panel-title"><strong>Quản lý lịch sử yêu cầu rút tiền</strong></h3>
                (Tìm thấy : {{$listObj->total()}} bản ghi)
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
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" id="" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    @foreach(App\Http\Models\Orders::getListStatus($q_status, App\Http\Models\Orders::ORDER_BUY_MPG) as $status)
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
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th>Ngày giao dịch</th>
                    <th>Ngày hiệu lực</th>
                    <th>Số tiền giao dịch</th>
                    <th>Số TK</th>
                    <th>Số dư cuối</th>
                    <th>Trạng thái giao dịch</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$val)
                    <tr id="itemRow_{{$val->id}}">
                        <td>
                            {{ \App\Elibs\Helper::showMongoDate($val['created_at'], 'd/m/Y H:i:s') }}
                        </td>
                        <td>
                            {{ \App\Elibs\Helper::showMongoDate($val['actived_at'], 'd/m/Y H:i:s') }}
                        </td>
                        <td>
                            <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val['so_tien_muon_rut'])}}</b>
                        </td>
                        <td>
                            <b class="text-primary">Số TK: </b>{{\App\Elibs\Helper::showContent($val['tk_ngan_hang']['so'])}}<br>
                            <b class="text-primary">Ngân hàng: </b>{{\App\Elibs\Helper::showContent($val['tk_ngan_hang']['name'])}}
                        </td>
                        <td>
                            <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val['so_du_cuoi'])}}</b>
                        </td>
                        <td>
                            @php($status = \App\Http\Models\Withdrawal::getListStatus(FALSE, @$val['status']))
                            <b class="text-{{ $status['style'] }}">{{ $status['text'] }}</b>
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
