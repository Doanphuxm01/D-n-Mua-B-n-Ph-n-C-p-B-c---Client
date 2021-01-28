<div class="table-responsive" id="memberTbl">
    <table class="table table-striped  table-io">
        <thead>
        <tr>
            <th width="10">STT</th>
            <th>Thông tin khách hàng</th>
            <th>Chi tiết đơn hàng</th>
            <th>Địa chỉ giao hàng</th>
            <th>Đại lý trả hàng</th>
            <th>Tình trạng đơn hàng</th>
        </tr>
        </thead>
        <tbody>
        @foreach($listObj as $key=>$val)
            <tr class="cursor-pointer" id="itemRow_{{$val->id}}" title="Đơn hàng của {{$val['fullname']}}" onclick="_SHOW_FORM_REMOTE('{{admin_link('don-hang-cua-toi/quick-form?id='.$val['_id'])}}')">
                <td>
                    {{$key+1}}
                </td>
                <td>

                    Họ tên: <span class="text-success text-bold">{{$val['fullname']}}</span>
                    <div>
                        Số điện thoại: {{$val['phone']}}
                    </div>
                    <div>
                        Email: @if($val['email']){{$val['email']}}@else Chưa cập nhật @endif
                    </div>
                </td>
                <td>
                    Mã đơn hàng: <span class="text-primary cursor-pointer text-bold">{{$val['code']??$val['_id']}}</span><br>
                    Số sản phẩm: <b class="text-danger">{{$val['number']??'Chưa cập nhật'}}</b>
                    <div>
                        Tổng tiền: <b class="text-danger">{{ \App\Elibs\Helper::formatMoney($val['grandTotal']) }}</b>
                    </div>
                </td>
                <td>
                    {{ $val['street'] ? $val['street']. ', ' : ''}} {{$val['town']['name']}}, {{$val['district']['name'].', '.$val['city']['name']}}
                    <br>
                    @if($val['note'])
                        Ghi chú: {{ $val['note'] }}
                    @endif
                </td>
                <td>
                    <b class="text-success">{{ $val['agency']['name'] }}</b>
                </td>
                <td>
                    @php($status = \App\Http\Models\PurchaseOrder::getListStatus(FALSE, @$val['status']))
                    @php($pay = \App\Http\Models\PurchaseOrder::paymentType(FALSE, @$val['payment_type']))
                    Thanh toán: <i><b class="text-{{ $pay['style'] }}">{{ $pay['text'] }}</b></i> <br>
                    Trạng thái đơn: <i><b class="text-{{ $status['style'] }}">{{ $status['text'] }}</b></i> <br>
                    Ngày tạo: <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'], 'd/m/Y H:i')}}</i>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>