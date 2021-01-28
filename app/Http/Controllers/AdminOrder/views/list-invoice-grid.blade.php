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
            <li class=""><a href="{{admin_link('customer')}}">Danh sách thành viên</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    @include("views.include.sidebar")
    <div class="content-wrapper">
        <div class="" style="padding: 0px 12px">
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
                                    <li> @if($t == 'danh_sach_so_luong_don_theo_tung_trang_thai')Tổng @elseif($t == 'danh_sach_tien_don_theo_tung_trang_thai') Tổng tiền @endif số [Đơn {{ \App\Http\Models\Orders::getStatus(@$i['_id'])['text'] }}]: <span class="text-danger text-bold">{{ \App\Elibs\Helper::formatMoney(@$i['total']) }}</span></li>
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
        @foreach($listObj as $key=>$val)
        <div class="col-md-6">
            <div class="panel invoice-grid">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="text-semibold no-margin-top">{{ $val['code'] }}</h6>
                            <ul class="list list-unstyled">
                                <li>Invoice #: &nbsp;0027</li>
                                <li>Issued on: <span class="text-semibold">2015/02/24</span></li>
                            </ul>
                        </div>

                        <div class="col-sm-6">
                            <h6 class="text-semibold text-right no-margin-top">$5,100</h6>
                            <ul class="list list-unstyled text-right">
                                @php($status = \App\Http\Models\PurchaseOrder::getListStatus(FALSE, @$val['status']))
                                @php($pay = \App\Http\Models\PurchaseOrder::paymentType(FALSE, @$val['payment_type']))
                                <li>HT thanh toán: <i><b class="text-semibold text-{{ $pay['style'] }}">{{ $pay['text'] }}</b></i></li>
                                <li class="dropdown">
                                    Trạng thái: &nbsp;
                                    <a href="#" class="label bg-{{ $status['style'] }}-400 dropdown-toggle" data-toggle="dropdown">{{ $status['text'] }} </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <ul>
                        <li><span class="status-mark border-success position-left"></span> Ngày tạo: <span class="text-semibold">{{\App\Elibs\Helper::showMongoDate($val['created_at'], 'd/m/Y H:i')}}</span></li>
                    </ul>

                    <ul class="pull-right">
                        <li><a href="#" data-toggle="modal" data-target="#invoice"><i class="icon-eye8"></i></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i> <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="#"><i class="icon-printer"></i> Print invoice</a></li>
                                <li><a href="#"><i class="icon-file-download"></i> Download invoice</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-file-plus"></i> Edit invoice</a></li>
                                <li><a href="#"><i class="icon-cross2"></i> Remove invoice</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
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
        </div>
    </div>
@stop